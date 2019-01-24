<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\module;

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
use pocketmine\utils\TextFormat;
use AndreasHGK\Arena\claims\Claim;

class ClaimsModule extends ModuleBase implements Listener {

    private $creationmode = [];

    /** @var ClaimManager */
    public $claimManager;

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
            $event->getPlayer()->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You have INFINITE claim blocks left. Destroy blocks to set claim positions."));
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
        if(!$player->hasPermission("claim.use")){
            return;
        }elseif($this->isCreating($player->getName())){
            if($this->creationmode[$player->getName()] == 1){
                $this->creationmode[$player->getName()]++;
                $this->claimManager->setPos1($player->getName(), $block);
                $event->setCancelled();
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX()+1, $block->getLevel()->getHighestBlockAt($block->getX()+1, $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX()-1, $block->getLevel()->getHighestBlockAt($block->getX()-1, $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()+1), $block->getZ()+1, $block->getLevel()));
                $this->sendBlocks(BlockIds::GLOWINGOBSIDIAN, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()-1), $block->getZ()-1, $block->getLevel()));
            }elseif($this->creationmode[$player->getName()] == 2){
                $event->setCancelled();
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

}