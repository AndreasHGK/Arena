<?php

namespace AndreasHGK\Arena\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

use AndreasHGK\Arena\Arena;
use pocketmine\utils\TextFormat;

class ArenaCommand implements CommandExecutor {

    private $plugin;

    public function __construct(Arena $plugin){
        $this->plugin = $plugin;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "arena"){
            if(!isset($args[0])){
                $sender->sendMessage("help");
                return true;
            }
            switch(strtolower($args[0])){
                case "join":
                    $sender->sendMessage("join");
                    return true;
                    break;
                case "list":
                    $sender->sendMessage("list");
                    return true;
                    break;
                case "create":
                    $cmd = new CreateSubCommand($this->plugin, $sender);
                    $cmd->execute();
                    return true;
                    break;
                case "testtip":
                    $sender->sendMessage("testtip");
                    $sender->addActionBarMessage(TextFormat::colorize("&7Killed player &4test12"));
                    $level = $sender->getLevel();
                    $pos = new Vector3($sender->getX(), $sender->getY(), $sender->getZ());
                    $level->addSound(new AnvilFallSound($pos));
                    return true;
                    break;
                default:
                    $sender->sendMessage("help");
                    return true;
                    break;
            }
        }
        return false;
    }
}