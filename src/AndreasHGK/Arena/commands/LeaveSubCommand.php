<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use Sheep\Command\Command;

class LeaveSubCommand extends SubCommand {

    public function __construct(Arena $arena, CommandSender $sender, array $args){
        parent::__construct($arena, $sender, $args);
    }

    public function execute() : void{
        $this->sender->sendMessage("leave");
    }

}