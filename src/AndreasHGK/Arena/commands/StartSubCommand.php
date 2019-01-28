<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class StartSubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        if(!isset($this->args[1])){
            if($this->manager->playerIsInArena($this->sender)){
                $arena = $this->manager->getPlayerArena($this->sender);
            }else{
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Some arguments are missing"));
                return;
            }
        }else{
            if($this->manager->arenaExists($this->args[1])){
                $arena = $this->manager->getArena($this->args[1]);
            }else{
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That arena doesn't exist"));
                return;
            }

        }

        if($arena->allowSkip()){
            if($arena->isWaiting()){
                $arena->skip();
                $arena->broadcast("&l&8[&c!&8]&r&7 Player &c".$this->sender->getName()."&7 skipped the wait timer");
            }else{
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't skip the timer at this time"));
            }
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do that on this arena"));
        }
    }

}