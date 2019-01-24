<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\module\ClaimsModule;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;

use AndreasHGK\Arena\Arena;
use pocketmine\utils\TextFormat;

class ClaimsCommand implements CommandExecutor
{

    private $plugin;
    private $manager;
    private $module;

    public function __construct(Arena $plugin, ArenaManager $manager, ClaimsModule $module)
    {
        $this->plugin = $plugin;
        $this->manager = $manager;
        $this->module = $module;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if(strtolower($command->getName()) == "claim"){
            if(!isset($args[0])){
                $cmd = new CHelpCommand($this->plugin, $sender, $args, $this->manager, $this->module);
                $cmd->execute();
                return true;
            }
            switch(strtolower($args[0])){
                case "abandon":
                    $cmd = new CAbandonCommand($this->plugin, $sender, $args, $this->manager, $this->module);
                    $cmd->execute();
                    return true;
                    break;
                case "abandonall":
                    $cmd = new CAbandonAllCommand($this->plugin, $sender, $args, $this->manager, $this->module);
                    $cmd->execute();
                    return true;
                    break;
                case "here":
                    $cmd = new CHereCommand($this->plugin, $sender, $args, $this->manager, $this->module);
                    $cmd->execute();
                    return true;
                    break;
                case "allow":
                    $cmd = new CTrustCommand($this->plugin, $sender, $args, $this->manager, $this->module);
                    $cmd->execute();
                    return true;
                    break;
                case "deny":
                    $cmd = new CUntrustCommand($this->plugin, $sender, $args, $this->manager, $this->module);
                    $cmd->execute();
                    return true;
                    break;
                default:
                    $cmd = new CHelpCommand($this->plugin, $sender, $args, $this->manager, $this->module);
                    $cmd->execute();
                    return true;
                    break;
            }
        }else{
            return false;
        }
    }
}