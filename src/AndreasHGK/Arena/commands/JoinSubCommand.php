<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

class JoinSubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        if($this->manager->playerIsInArena($this->sender)){
            $this->sender->sendMessage("already in arena");
        }elseif(!isset($this->args[1])){
            $this->sender->sendMessage("missing argument");
        }elseif($this->manager->arenaExists($this->args[1])){
            $this->manager->playerJoin($this->sender, $this->args[1]);
            $this->sender->sendMessage("join");
        }else{
            $this->sender->sendMessage("arena doesn't exist");
        }
    }

}