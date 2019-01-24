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
        if(!$this->sender->hasPermission("claim.trust")){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
            return;
        }elseif(!isset($this->args[1])){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Some arguments are missing"));
        }
        $cm = $this->module->claimManager;
        if($cm->isClaimed($this->sender->getPosition(), $this->sender->getLevel()->getName())){
            if($cm->ownsPos($this->sender->getPosition(), $this->sender->getLevel()->getName(), $this->sender)){
                $claim = $cm->getClaim($this->sender->getPosition(), $this->sender->getLevel()->getName());
                if($claim->isTrusted($this->args[1])){
                    $claim->unTrust($this->args[1]);
                    $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Untrusted player &c".$this->args[1]." &7from the current claim"));
                }else{
                    $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Player &c".$this->args[1]." &7isn't trusted in the current claim"));
                }
            }else{
                $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't own this area"));
            }
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You're not in a claimed area"));
        }
    }

}