<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CreateSubCommand extends SubCommand{

    protected $manager;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager)
    {
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute(): void
    {
        if (!isset($this->args[1])) {
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Some arguments are missing"));
            return;
        }if(!isset($this->args[2])){
            $this->args[2] = "FFA";
        }
        $this->manager->create((string)$this->args[1], $this->arena->getServer()->getPlayerByUUID($this->sender->getUniqueId()), (string)$this->args[2]);
        $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 created a &c&l".$this->args[2]."&r&7 arena named &c&l".$this->args[1]));
    }
}