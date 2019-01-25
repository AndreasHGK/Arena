<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\module;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\claims\ClaimManager;
use AndreasHGK\Arena\commands\ClaimsCommand;
use Composer\Script\Event;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\command\PluginCommand;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use AndreasHGK\Arena\claims\Claim;

class ClaimsModule extends ModuleBase implements Listener {

    private $creationmode = [];

    /** @var ClaimManager */
    public $claimManager;

    public $claimscfg;
    public $claims;

    private $format = [
        "pos1x" => null,
        "pos1z" => null,
        "pos2x" => null,
        "pos2z" => null,
        "owner" => null,
        "level" => null,
        "trusted" => []
    ];

    public function __construct(Arena $arena, ArenaManager $manager, Config $claimscfg)
    {
        parent::__construct($arena, $manager);
        $this->claimscfg = $claimscfg;
        $this->claims = $this->claimscfg->getAll();
    }

    public function execute() : void{
        $this->claimManager = new ClaimManager($this->arena, $this);
        $this->arena->getServer()->getPluginManager()->registerEvents($this, $this->arena);

        $cmd = new PluginCommand("claim", $this->arena);
        $cmd->setExecutor(new ClaimsCommand($this->arena, $this->manager, $this));
        $cmd->setDescription("claim to protect land");
        $cmd->setPermission("claim.command");
        $this->arena->getServer()->getCommandMap()->register("arena", $cmd, "claim");
        $this->arena->getLogger()->debug("enabled module: Claims");
    }

    public function onSwitch(PlayerItemHeldEvent $event){
        if($event->getItem()->getId() == ItemIds::GOLDEN_SHOVEL){
            $event->getPlayer()->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You have &cINFINITE &7claim blocks left. Destroy blocks to set claim positions."));
            $this->creationmode[$event->getPlayer()->getName()] = 1;
        }elseif(isset($this->creationmode[$event->getPlayer()->getName()])){
            unset($this->creationmode[$event->getPlayer()->getName()]);
        }
    }

    public function isCreating(string $name) : bool{
        return isset($this->creationmode[$name]);
    }

    public function BreakEvent(BlockBreakEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (isset($this->arena->timeout[$player->getName()]) && $this->arena->timeout[$player->getName()] > microtime(true)) {
            $event->setCancelled();
            return;
        }
        if(!$player->hasPermission("claim.use")){
            return;
        }elseif($this->isCreating($player->getName())){
            if(in_array($player->getLevel()->getName(), $this->arena->cfg["disabledworlds"])){
                $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't claim in this world"));
                $event->setCancelled();
                $this->arena->timeout[$player->getName()] = microtime(true) + 1;
                return;
            }
            if($this->creationmode[$player->getName()] == 1){
                $this->creationmode[$player->getName()]++;
                $this->claimManager->setPos1($player->getName(), $block);
                $event->setCancelled();
                $this->arena->timeout[$player->getName()] = microtime(true) + 1;
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX()+1, $block->getLevel()->getHighestBlockAt($block->getX()+1, $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX()-1, $block->getLevel()->getHighestBlockAt($block->getX()-1, $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()+1), $block->getZ()+1, $block->getLevel()));
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()-1), $block->getZ()-1, $block->getLevel()));
            }elseif($this->creationmode[$player->getName()] == 2){
                $event->setCancelled();
                $this->arena->timeout[$player->getName()] = microtime(true) + 1;
                $this->creationmode[$player->getName()] = 1;
                foreach($this->getAllPositions($this->claimManager->getPos1($player->getName()), $block) as $pos){
                    if($this->claimManager->isClaimed($pos, $pos->getLevel()->getName())){
                        $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Your claim is overlapping with another claim"));
                        $this->claimManager->unsetPos1($player->getName());
                        return;
                    }
                }
                $this->claimManager->claim($player->getName(), $block);
                $claim = $this->claimManager->getClaim($block, $block->getLevel()->getName());
                if($claim->getDimensions()[0] < 10 || $claim->getDimensions()[1] < 10){
                    $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Your claim needs to by at least a 10 by 10 area"));
                    $claim->delete();
                    return;
                }
                $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 claim created"));
                $claim->displayCorners();
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event){
        $player = $event->getPlayer();
        if($this->isCreating($player->getName())) {
            unset($this->creationmode[$player->getName()]);
        }
    }

    public function levelChange(EntityLevelChangeEvent $event) {
        $entity = $event->getEntity();
        if(!$entity instanceof Player){
            return;
        }else if($this->isCreating($entity->getName())){
            if($this->creationmode[$entity->getName()] == 2){
                $this->creationmode[$entity->getName()] = 1;
                $this->claimManager->unsetPos1($entity->getName());
            }
        }
    }

    public function sendBlocks(int $id, Position $pos) : void{
        $pk = new UpdateBlockPacket();
        $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($id);
        $pk->x = (int) $pos->getX();
        $pk->y = (int) $pos->getY();
        $pk->z = (int) $pos->getZ();
        $pk->flags = UpdateBlockPacket::FLAG_ALL_PRIORITY;
        $pos->getLevel()->broadcastPacketToViewers($pos, $pk);
    }

    public function getAllPositions(Position $pos1, Position $pos2) : array{
        $positions = [];
        for($x = min($pos1->getX(), $pos2->getX()); $x <= max($pos1->getX(), $pos2->getX()); $x++){
            for($z = min($pos1->getZ(), $pos2->getZ()); $z <= max($pos1->getZ(), $pos2->getZ()); $z++){
                $pos = new Position($x, 256, $z, $pos1->getLevel());
                array_push($positions, $pos);
            }
        }
        return $positions;
    }

    public function save() : void{
        $this->arena->getLogger()->debug("saving claims...");
        $saves = [];
        $this->claimscfg->set("claims", NULL);
        foreach($this->claimManager->claims as $key){
            foreach($key as $claim){
                $save = $this->format;
                $save["pos1x"] = $claim->getPos1()->getX();
                $save["pos1z"] = $claim->getPos1()->getZ();
                $save["pos2x"] = $claim->getPos2()->getX();
                $save["pos2z"] = $claim->getPos2()->getZ();
                $save["owner"] = $claim->getOwner();
                $save["level"] = $claim->getLevel();
                $save["trusted"] = $claim->getTrusted();
                $saves["claims"][$claim->getOwner()][$claim->getID()] = $save;
                $this->arena->getLogger()->debug("saved claim ".$claim->getID());
            }
        }
        $this->claimscfg->setAll($saves);
        $this->claimscfg->save();
    }

    public function load() : void{
        $this->arena->getLogger()->debug("loading claims...");
        foreach($this->claims["claims"] as $key){
            foreach($key as $claim){
                $this->claimManager->addClaim($claim["owner"], new Position($claim["pos1x"], 256, $claim["pos1z"], $this->arena->getServer()->getLevelByName($claim["level"])),  new Position($claim["pos2x"], 256, $claim["pos2z"], $this->arena->getServer()->getLevelByName($claim["level"])), $claim["level"]);
                $obj = $this->claimManager->getClaim(new Position($claim["pos1x"], 256, $claim["pos1z"], $this->arena->getServer()->getLevelByName($claim["level"])), $claim["level"]);
                foreach($claim["trusted"] as $str){
                    $obj->trust($str);
                }
                $this->arena->getLogger()->debug("loaded claim ".$obj->getID());
            }
        }
        if(empty($this->claims["claims"])){
            $this->arena->getLogger()->debug("there are no claims to load");
        }
    }

}