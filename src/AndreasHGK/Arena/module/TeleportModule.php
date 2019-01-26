<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\module;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\commands\TPServerCommand;
use AndreasHGK\Arena\commands\TPSpawnCommand;
use AndreasHGK\Arena\commands\TPWildCommand;
use AndreasHGK\Arena\task\HealthTagTask;
use pocketmine\command\PluginCommand;

class TeleportModule extends ModuleBase {

    private $task;

    public function execute() : void{
        $s = new PluginCommand("server", $this->arena);
        $s->setExecutor(new TPServerCommand($this->arena, $this->manager, $this));
        $s->setDescription("transfer to another server");
        $s->setPermission("arena.server");
        $spawn = new PluginCommand("spawn", $this->arena);
        $spawn->setExecutor(new TPSpawnCommand($this->arena, $this->manager, $this));
        $spawn->setDescription("go to spawn");
        $spawn->setPermission("arena.spawn");
        $w = new PluginCommand("wild", $this->arena);
        $w->setExecutor(new TPWildCommand($this->arena, $this->manager, $this));
        $w->setDescription("go to wild");
        $w->setPermission("arena.wild");
        $this->arena->getServer()->getCommandMap()->register("arena", $s, "server");
        $this->arena->getServer()->getCommandMap()->register("arena", $spawn, "spawn");
        $this->arena->getServer()->getCommandMap()->register("arena", $w, "wild");
        $this->arena->getLogger()->debug("enabled module: Teleport");
    }

}