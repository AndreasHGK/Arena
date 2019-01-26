<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\module\TeleportModule;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

use AndreasHGK\Arena\Arena;
use pocketmine\utils\TextFormat;
use raklib\protocol\ACK;

class TPServerCommand implements CommandExecutor {

    private $plugin;
    private $manager;
    private $tp;

    public function __construct(Arena $plugin, ArenaManager $manager, TeleportModule $tp){
        $this->plugin = $plugin;
        $this->manager = $manager;
        $this->tp = $tp;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "server"){
            if(!isset($args[0])){
                $str = "&l&8[&c!&8]&r&7 Servers: &c";
                foreach($this->plugin->cfg["servers"] as $server){
                    $str = $str.$server["name"]."&7, ";
                }
                $sender->sendMessage(TextFormat::colorize($str));
                return true;
            }else{
                if(array_key_exists($args[0], $this->plugin->cfg["servers"])){
                    $sender->transfer($this->plugin->cfg["servers"][$args[0]]["ip"], $this->plugin->cfg["servers"][$args[0]]["port"]);
                    return true;
                }else{
                    $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That is not a valid server"));
                    return true;
                }
            }
        }
        return false;
    }
}