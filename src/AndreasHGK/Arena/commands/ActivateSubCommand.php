<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

class ActivateSubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        if(isset($this->args[1])) {
            $arena = $this->args[1];
            if ($this->manager->arenaExists($arena)) {
                $arena = $this->manager->getArena($arena);
                $arena->activate();
                $this->sender->sendMessage("activating");
            }else{
                $this->sender->sendMessage("arena doesn't exist");
            }
        }else{
            $this->sender->sendMessage("missing argument");
        }
    }

}