<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use pocketmine\level\Position;
use pocketmine\Player;

abstract class ArenaClass{

   protected $arena;

   protected $pos1;
   protected $pos2;
   protected $name;
   protected $creator;

   public function __construct(Arena $arena, Position $pos1, Position $pos2, string $name, Player $creator){
       $this->arena = $arena;
       $this->pos1 = $pos1;
       $this->pos2 = $pos2;
       $this->name = $name;
       $this->creator = $creator;
   }

}