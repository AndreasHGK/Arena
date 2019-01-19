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

class ArenaCommand implements CommandExecutor {

    private $plugin;
    private $manager;

    public function __construct(Arena $plugin, ArenaManager $manager){
        $this->plugin = $plugin;
        $this->manager = $manager;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "arena"){
            if(!isset($args[0])){
                $cmd = new HelpSubCommand($this->plugin, $sender, $args);
                $cmd->execute();
                return true;
            }
            switch(strtolower($args[0])){
                case "join":
                    $cmd = new JoinSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "list":
                    $cmd = new ListSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "create":
                    $cmd = new CreateSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "delete":
                    $cmd = new DeleteSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "leave":
                    $cmd = new LeaveSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "pos1":
                    $cmd = new Pos1SubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "pos2":
                    $cmd = new Pos2SubCommand($this->plugin, $sender, $args, $this->manager);
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
                    $cmd = new HelpSubCommand($this->plugin, $sender, $args);
                    $cmd->execute();
                    return true;
                    break;
            }
        }
        return false;
    }
}