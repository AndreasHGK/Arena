<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena\modes;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaClass;
use pocketmine\Player;

class Elimination extends ArenaClass {

    public function __construct(Arena $plugin, string $name, Player $creator){
        parent::__construct($plugin, $name, $creator);
    }

}