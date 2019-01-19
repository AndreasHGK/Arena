<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ListSubCommand extends SubCommand {

    protected $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        $str = "&l&8[&c!&8]&r&7 arenas: &c&l";
        foreach($this->manager->getAll() as $arena){
            $str = $str.$arena->getName()."&r&7, &c&l";
        }
        $this->sender->sendMessage(TextFormat::colorize($str));
    }

}