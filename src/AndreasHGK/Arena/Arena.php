<?php

declare(strict_types=1);

namespace AndreasHGK\Arena;

use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use AndreasHGK\Arena\commands\ArenaCommand;

class Arena extends PluginBase{

    public $manager;

	public function onEnable() : void{
	    $this->manager = new ArenaManager($this);
	    $cmd = new PluginCommand("arena", $this);
	    $cmd->setExecutor(new ArenaCommand($this, $this->manager));
	    $cmd->setDescription("join or create arenas");
	    $cmd->setPermission("arena.command");
	    $this->getServer()->getCommandMap()->register("arena", $cmd, "arena");
	}

	public function onDisable() : void{
	}
}