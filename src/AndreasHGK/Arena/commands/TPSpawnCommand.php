<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\module\TeleportModule;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\Player;

use AndreasHGK\Arena\Arena;
use pocketmine\utils\TextFormat;

class TPSpawnCommand implements CommandExecutor {

    private $plugin;
    private $manager;
    private $tp;

    public function __construct(Arena $plugin, ArenaManager $manager, TeleportModule $tp){
        $this->plugin = $plugin;
        $this->manager = $manager;
        $this->tp = $tp;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "spawn"){
            $sender->teleport($this->plugin->getServer()->getLevelByName($this->plugin->cfg["spawnworld"])->getSafeSpawn());
            $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Teleporting to spawn..."));
        }
        return false;
    }
}