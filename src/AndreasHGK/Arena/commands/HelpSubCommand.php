<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use Sheep\Command\Command;

class HelpSubCommand extends SubCommand {

    public function __construct(Arena $arena, CommandSender $sender, array $args){
        parent::__construct($arena, $sender, $args);
    }

    public function execute() : void{
        $string = "&7-&l&4Arena &7help&r&7- 
        \n &r&c/arena list &7- lists arenas
        \n &r&c/arena join {arena} &7- join an arena
        \n &r&c/arena leave &7- leave an arena";
        if($this->sender->hasPermission("arena.create")){
            $string = $string."
        \n &r&c/arena create {arena} [mode] &7- create an arena
        \n &r&c/arena delete {arena} &7- delete an arena
        \n &r&c/arena activate {arena} &7- activate an arena
        \n &r&c/arena deactivate {arena} &7- deactivate an arena
        \n &r&c/arena pos1 {arena} &7- sets pos1 for arena
        \n &r&c/arena pos2 {arena} &7- sets pos2 for arena
        \n &r&c/arena addspawn {arena} {spawnname} &7- adds a spawn to arena
        \n &r&c/arena delspawn {arena} {spawnname} &7- removes a spawn from arena";
        }
        $this->sender->sendMessage(TextFormat::colorize($string));
    }

}