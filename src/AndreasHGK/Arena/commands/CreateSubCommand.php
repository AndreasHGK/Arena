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
        }elseif(in_array($this->sender->getLevel()->getName(), $this->arena->cfg["disabledworlds"])){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't create an arena in this world"));
            return;
        }if(!isset($this->args[2])){
            $this->args[2] = "FFA";
        }
        if($this->manager->arenaExists($this->args[1])){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 There is already an arena with this name"));
            return;
        }
        $this->manager->create((string)$this->args[1], $this->sender->getName(), (string)$this->args[2], $this->sender->getLevel()->getName());
        $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 created arena &c".$this->args[1]."&r&7 with mode &c".$this->args[2]));
    }
}