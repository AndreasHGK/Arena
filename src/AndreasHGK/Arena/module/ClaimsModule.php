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
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\ItemIds;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

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
        if($this->isCreating($player->getName())){
            if($this->creationmode[$player->getName()] == 1){
                $this->creationmode[$player->getName()]++;
                $this->claimManager->setPos1($player->getName(), $block);
                $event->setCancelled();
                $this->sendBlocks(152, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(152, new Position($block->getX()+1, $block->getLevel()->getHighestBlockAt($block->getX()+1, $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(152, new Position($block->getX()-1, $block->getLevel()->getHighestBlockAt($block->getX()-1, $block->getZ()), $block->getZ(), $block->getLevel()));
                $this->sendBlocks(152, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()+1), $block->getZ()+1, $block->getLevel()));
                $this->sendBlocks(152, new Position($block->getX(), $block->getLevel()->getHighestBlockAt($block->getX(), $block->getZ()-1), $block->getZ()-1, $block->getLevel()));
            }elseif($this->creationmode[$player->getName()] == 2){
                $event->setCancelled();
                $this->claimManager->claim($player->getName(), $block);
                $claim = $this->claimManager->getClaim($block, $block->getLevel()->getName());
                $this->creationmode[$player->getName()] = 1;
                $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 claim created"));
                foreach($claim->getCorners() as $corner){
                    $this->sendBlocks(BlockIds::GLOWSTONE, $corner);
                }
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

}