<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\module\ClaimsModule;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CUntrustCommand extends SubCommand {

    private $manager;
    private $module;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager, ClaimsModule $module){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
        $this->module = $module;
    }

    public function execute() : void{
        $this->sender->sendMessage("C Untrust");
    }

}