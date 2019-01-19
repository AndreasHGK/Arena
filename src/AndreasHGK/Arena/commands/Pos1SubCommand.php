<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

class Pos1SubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        if(!isset($this->args[1])){
            $this->sender->sendMessage("missing argument");
        }elseif($this->manager->arenaExists($this->args[1])){
            $this->arena->pos($this->sender->getName(), 1, $this->args[1]);
            $this->sender->sendMessage("set pos1");
        }else{
            $this->sender->sendMessage("arena doesn't exist");
        }
    }

}