<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

use AndreasHGK\Arena\Arena;
use pocketmine\utils\TextFormat;

class AdminCommand implements CommandExecutor {

    private $plugin;
    private $manager;

    public function __construct(Arena $plugin, ArenaManager $manager){
        $this->plugin = $plugin;
        $this->manager = $manager;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "adminmode"){
            if($this->plugin->isAdminMode($sender->getName())){
                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You left admin mode"));
                $this->plugin->unsetAdminMode($sender->getName());
                return true;
            }else{
                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You are now in admin mode"));
                $this->plugin->setAdminMode($sender->getName());
                return true;
            }
        }
        return false;
    }
}