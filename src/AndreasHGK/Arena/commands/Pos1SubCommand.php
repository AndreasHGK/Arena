<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Pos1SubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        if(!isset($this->args[1])){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Some arguments are missing"));
        }elseif(in_array($this->sender->getLevel()->getName(), $this->arena->cfg["disabledworlds"])){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't create an arena in this world"));
            return;
        }elseif($this->manager->arenaExists($this->args[1])){
            $this->arena->pos($this->sender->getName(), 1, $this->args[1]);
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Set position 1 for arena &c".$this->args[1]));
            return;
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That arena doesn't exist"));
            return;
        }
    }

}