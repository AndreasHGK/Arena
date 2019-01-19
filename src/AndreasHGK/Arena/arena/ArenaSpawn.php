<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use pocketmine\level\Position;
use pocketmine\Player;

class ArenaSpawn{

    private $arena;
    private $pos;

    public function __construct(ArenaClass $arena, Position $pos){
        $this->arena = $arena;
        $this->pos = $pos;
    }


}