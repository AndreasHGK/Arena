<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

abstract class SubCommand{

    protected $arena;
    protected $sender;
    protected $args = [];

    public function __construct(Arena $arena, CommandSender $sender, array $args){
        $this->arena = $arena;
        $this->sender = $sender;
        $this->args = $args;
    }

    public abstract function execute();

}