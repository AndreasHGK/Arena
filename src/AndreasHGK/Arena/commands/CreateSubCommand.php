<?php

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use Sheep\Command\Command;

class CreateSubCommand extends SubCommand {

    public function __construct(Arena $arena, CommandSender $sender){
        parent::__construct($arena, $sender);
    }

    public function execute() : void{
        $this->sender->sendMessage("create");
    }

}