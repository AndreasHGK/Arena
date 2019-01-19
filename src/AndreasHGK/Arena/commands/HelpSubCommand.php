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
        $this->sender->sendMessage(TextFormat::colorize("&7-&l&4Arena &7help&r&7- 
        \n&r&c/arena create {name} [mode] &7- create an arena
        \n&r&c/arena delete {name} &7- delete an arena
        \n&r&c/arena list &7- lists arenas
        \n&r&c/arena join {name} &7- join an arena
        \n&r&c/arena leave &7- leave an arena"));
    }

}