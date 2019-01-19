<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use Sheep\Command\Command;

class ListSubCommand extends SubCommand {

    protected $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        $str = "arenas: ";
        foreach($this->manager->getAll() as $arena){
            $str = $str.$arena->getName()." ";
        }
        $this->sender->sendMessage($str);
    }

}