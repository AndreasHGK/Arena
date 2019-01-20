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
    private $name;

    public function __construct(ArenaClass $arena, Vector3 $pos, string $name){
        $this->arena = $arena;
        $this->pos = $pos;
        $this->name = $name;
    }

    public function spawnPlayer(Player $player) : void{
        $player->teleport($this->pos);
    }

    public function getPos() : Vector3{
        return $this->pos;
    }

    public function getName() : string {
        return $this->name;
    }

}