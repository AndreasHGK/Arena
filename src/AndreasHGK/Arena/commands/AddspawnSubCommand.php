<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

class AddspawnSubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{

        if(isset($this->args[1]) && isset($this->args[2])){
            $arena = $this->args[1];
            $name = $this->args[2];
            $arena = $this->manager->getArena($arena);
            if($arena->spawnExists($name)){
                $this->sender->sendMessage("spawn already exists");
            }else{
                $arena->addSpawn($name, $this->sender->getPosition());
                $this->sender->sendMessage("created spawn ".$name);
            }
        }else{
            $this->sender->sendMessage("missing argument");
        }
    }

}