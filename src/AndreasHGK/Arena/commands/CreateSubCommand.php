<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use Sheep\Command\Command;

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
            $this->sender->sendMessage("missing argument");
            return;
        }if(!isset($this->args[2])){
            $this->args[2] = "elimination";
        }
        $this->manager->create((string)$this->args[1], $this->arena->getServer()->getPlayerByUUID($this->sender->getUniqueId()), (string)$this->args[2]);
        $this->sender->sendMessage("created arena ".(string)$this->args[1]." with mode ".(string)$this->args[2]);
    }
}