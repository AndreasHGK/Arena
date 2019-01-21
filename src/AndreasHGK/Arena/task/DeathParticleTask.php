<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\task;

use AndreasHGK\Arena\Arena;
use pocketmine\level\Level;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\Random;

class DeathParticleTask extends Task {

    public $pos;
    public $arena;
    public $level;
    public $random;
    public $ticks = 0;

    public function __construct($arena, Vector3 $pos, Level $level){
        $this->pos = $pos;
        $this->arena = $arena;
        $this->level = $level;
        $this->random = new Random();
    }

    public function onRun(int $currentTick){
        if($this->ticks === 10) {
            $this->arena->removeTask($this->getTaskId());
        }
        $this->ticks++;
        for($i = 0; $i < 10; $i++){
            $x = $this->random->nextRange(-100,100)/100;
            $y = $this->random->nextRange(-100,100)/100;
            $z = $this->random->nextRange(-100,100)/100;
            $newpos = new Position($this->pos->getX()+$x, $this->pos->getY()+$y, $this->pos->getZ()+$z);
            $this->level->addParticle(new FlameParticle($newpos));
        }
    }

}