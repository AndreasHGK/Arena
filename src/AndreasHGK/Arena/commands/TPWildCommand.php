<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\module\TeleportModule;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

use AndreasHGK\Arena\Arena;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

class TPWildCommand implements CommandExecutor {

    private $plugin;
    private $manager;
    private $tp;
    private $random;

    public function __construct(Arena $plugin, ArenaManager $manager, TeleportModule $tp){
        $this->plugin = $plugin;
        $this->manager = $manager;
        $this->tp = $tp;
        $this->random = new Random();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "wild"){
            $x = $this->random->nextRange(-$this->plugin->cfg["wildradius"], $this->plugin->cfg["wildradius"]);
            $z = $this->random->nextRange(-$this->plugin->cfg["wildradius"], $this->plugin->cfg["wildradius"]);
            $this->plugin->getServer()->loadLevel($this->plugin->cfg["wildworld"]);
            $lvl = $this->plugin->getServer()->getLevelByName($this->plugin->cfg["wildworld"]);
            $lvl->loadChunk($x >> 4, $z >> 4);
            $pos = new Position($x, 128, $z, $lvl);
            $sender->teleport($pos);
            $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Teleporting to wild"));
        }
        return false;
    }
}