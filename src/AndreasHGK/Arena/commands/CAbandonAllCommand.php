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

class CAbandonAllCommand extends SubCommand {

    private $manager;
    private $module;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager, ClaimsModule $module){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
        $this->module = $module;
    }

    public function execute() : void{
        if(!$this->sender->hasPermission("claim.use")){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
            return;
        }
        $cm = $this->module->claimManager;
        $success = false;
        foreach($cm->claims[$this->sender->getName()] as $claim){
            $claim->delete();
            $success = true;
        }
        if($success){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You abandoned all your claims"));
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have any claims to abandon"));
        }
    }

}