<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ActivateSubCommand extends SubCommand {

    private $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        if(isset($this->args[1])) {
            $arena = $this->args[1];
            if ($this->manager->arenaExists($arena)) {
                $arena = $this->manager->getArena($arena);
                if(!$arena->pos1Isset() || !$arena->pos2Isset() || empty($arena->getSpawns())){
                    $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't activate an arena that is not fully set up"));
                    return;
                }
                $arena->activate();
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Activated arena &c".$arena->getName()));
            }else{
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That arena doesn't exist"));
            }
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Some arguments are missing"));
        }
    }

}