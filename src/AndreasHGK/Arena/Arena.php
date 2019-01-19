<?php

declare(strict_types=1);

namespace AndreasHGK\Arena;

use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

use AndreasHGK\Arena\commands\ArenaCommand;

class Arena extends PluginBase{

	public function onEnable() : void{
	    $cmd = new PluginCommand("arena", $this);
	    $cmd->setExecutor(new ArenaCommand($this));
	    $cmd->setDescription("join or create arenas");
	    $cmd->setPermission("arena.command");
	    $this->getServer()->getCommandMap()->register("arena", $cmd, "arena");
	}

	public function onDisable() : void{
	}
}