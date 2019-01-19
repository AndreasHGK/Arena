<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;

class DeleteSubCommand extends SubCommand{

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
        } elseif (NULL !== $this->manager->getArena($this->args[1])) {
            $this->manager->delete($this->args[1]);
            $this->sender->sendMessage("delete");
            return;
        } else {
            $this->sender->sendMessage("arena doesn't exist");
            return;
        }

    }
}