<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class DelspawnSubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        $arena = $this->args[1];
        $name = $this->args[2];
        if(isset($arena) && isset($name)){
            $arena = $this->manager->getArena($arena);
            if(!$arena->spawnExists($name)){
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That spawn does not exist"));
            }else{
                $arena->delSpawn($name);
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Deleted spawn &c&l".$name."&r&7 from arena &c&l".$arena->getName()));
            }
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Some arguments are missing"));
        }
    }

}