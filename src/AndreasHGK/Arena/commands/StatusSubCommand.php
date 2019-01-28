<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class StatusSubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        if(!isset($this->args[1])){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Some arguments are missing"));
            return;
        }
        if(!isset($this->args[2]) || !is_int((int)$this->args[2])){
            $this->args[2] = 0;
            return;
        }
        if($this->args[2] > 2 || $this->args[2] < 0){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Status is out of bounds"));
            return;
        }
        if($this->manager->arenaExists($this->args[1])){
            $arena = $this->manager->getArena($this->args[1]);
            $arena->setStatus((int)$this->args[2]);
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Changed status of arena &c".$arena->getName()."&7 to &c".$arena->getStatusString()));
            return;
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That arena doesn't exist"));
            return;
        }
    }

}