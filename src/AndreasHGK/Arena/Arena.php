<?php

declare(strict_types=1);

namespace AndreasHGK\Arena;

use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\PluginCommand;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

use AndreasHGK\Arena\commands\ArenaCommand;

class Arena extends PluginBase implements Listener {

    public $manager;

    private $pos = [];
    private $posa = [];

	public function onEnable() : void{
//        @mkdir($this->getDataFolder());
//        $this->saveDefaultConfig();
//        $this->cfg = $this->getConfig()->getAll();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

	    $this->manager = new ArenaManager($this);
	    $cmd = new PluginCommand("arena", $this);
	    $cmd->setExecutor(new ArenaCommand($this, $this->manager));
	    $cmd->setDescription("join or create arenas");
	    $cmd->setPermission("arena.command");
	    $this->getServer()->getCommandMap()->register("arena", $cmd, "arena");
	}

	public function getArenaManager() : ArenaManager{
	    return $this->manager;
    }

	public function onDeath(PlayerDeathEvent $event){
        if($event->getPlayer()->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
            if($event->getPlayer()->getLastDamageCause()->getDamager() instanceof Player) {
                $player = $event->getPlayer();
                $killer = $player->getLastDamageCause()->getDamager();
                if($this->manager->playerIsInArena($player) && $this->manager->playerIsInArena($killer)){
                    $arena = $this->manager->getPlayerArena($player);
                    $arena->onKill($killer, $player);
                }
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event){
	    $player = $event->getPlayer();
	    if($this->manager->playerIsInArena($player)){
	        $this->manager->playerLeave($player);
        }
    }

    public function onBreak(BlockBreakEvent $event){
	    $player = $event->getPlayer();
	    if($this->pos[$player->getName()] = 1){
	        $pos = new Position($event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ());
	        $this->manager->getArena($this->posa[$player->getName()])->setPos1($pos);
	        unset($this->pos[$player->getName()]);
            unset($this->posa[$player->getName()]);
        }elseif($this->pos[$player->getName()] = 2){
            $pos = new Position($event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ());
            $this->manager->getArena($this->posa[$player->getName()])->setPos2($pos);
            unset($this->pos[$player->getName()]);
            unset($this->posa[$player->getName()]);
        }
    }

    public function pos(string $name, int $pos, string $arena) : void{
	    if($pos == 1 && $pos == 2){
            $this->pos[$name] = $pos;
            $this->posa[$name] = $arena;
        }
    }

	public function onDisable() : void{
	}
}