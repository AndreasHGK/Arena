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

class CHelpCommand extends SubCommand {

    private $manager;
    private $module;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager, ClaimsModule $module){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
        $this->module = $module;
    }

    public function execute() : void{
        $str = "&7-&l&4Claims &7help&r&7-\n &r&c/claim help &7- shows this message";
        if($this->sender->hasPermission("claim.use")){
            $str = $str."\n &r&c/claim create &7- claims an area with given positions\n &r&c/claim abandon &7- abandons the claim you're standing in\n &r&c/claim abandonall &7- abandons all owned \n &r&c/claim pos1 &7- selects first position\n &r&c/claim pos2 &7- selects second position\n &r&c/claim trust {player} &7- allows a player to build on your claims\n &r&c/claim untrust {player} &7- disallows a player to build on your claims\n &r&c/claim here &7- gives info about the land you're standing on";
        }
        $this->sender->sendMessage(TextFormat::colorize($str));
    }

}