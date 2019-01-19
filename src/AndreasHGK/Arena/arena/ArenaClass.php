<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use pocketmine\level\Position;
use pocketmine\Player;

abstract class ArenaClass{

   protected $arena;
   protected $active;

   protected $pos1;
   protected $pos2;
   protected $name;
   protected $creator;

   public function __construct(Arena $arena, string $name, Player $creator){
       $this->active = false;
       $this->arena = $arena;
       $this->name = $name;
       $this->creator = $creator;
   }

   public function setPos1(Position $pos1) : void{
       $this->pos1 = $pos1;
   }

    public function setPos2(Position $pos2) : void{
        $this->pos2 = $pos2;
    }

    public function getPos1() : Position{
       return $this->pos1;
    }

    public function getPos2() : Position{
        return $this->pos2;
    }

    public function activate() : void{
       if(isset($this->pos1) && isset($this->pos2)){
           $this->active = true;
       }
    }

    public function deActivate() : void{
       $this->active = false;
    }

    public function isActive() : bool{
       return $this->active;
    }

    public function getName() : string{
       return $this->name;
    }

}