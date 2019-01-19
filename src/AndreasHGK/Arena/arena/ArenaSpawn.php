<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ArenaSpawn{

    private $arena;
    private $pos;

    public function __construct(ArenaClass $arena, Vector3 $pos){
        $this->arena = $arena;
        $this->pos = $pos;
    }

    public function spawnPlayer(Player $player){
        $player->teleport($this->pos);
    }

}