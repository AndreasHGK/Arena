<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

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
            if(!$this->manager->arenaExists($arena)){
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That arena doesn't exist"));
            }elseif(!$this->manager->getArena($arena)->spawnExists($name)){
                $arena = $this->manager->getArena($arena);
                $arena->addSpawn($name, $this->sender->getPosition());
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Added spawn &c".$name."&r&7 in arena &c".$arena->getName()));
            }else{
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That spawn already exists"));
            }
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Some arguments are missing"));
        }
    }

}