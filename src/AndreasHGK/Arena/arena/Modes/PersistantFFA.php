<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena\modes;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaClass;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PersistantFFA extends ArenaClass {

    protected $type;

    public function __construct(Arena $plugin, string $name, Player $creator){
        parent::__construct($plugin, $name, $creator);
        $this->type = "PersistantFFA";
        $this->ffa = true;
    }

    public function onKill(Player $killer, Player $killed) : void
    {
        $killer->addActionBarMessage(TextFormat::colorize("&7Killed player &4".$killed->getName()));
        $level = $killed->getLevel();
        $pos = new Vector3($killed->getX(), $killed->getY(), $killed->getZ());
        $level->addSound(new AnvilFallSound($pos));
    }

}