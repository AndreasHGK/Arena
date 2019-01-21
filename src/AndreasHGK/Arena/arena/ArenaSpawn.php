<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\task\SpawnParticleTask;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\particle\SplashParticle;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

class ArenaSpawn{

    private $arena;
    private $pos;
    private $name;
    private $st;

    public function __construct(ArenaClass $arena, Vector3 $pos, string $name){
        $this->arena = $arena;
        $this->pos = $pos;
        $this->name = $name;
    }

    public function spawnPlayer(Player $player) : void{
        $player->teleport($this->pos);
        $player->getLevel()->addSound(new EndermanTeleportSound($this->pos));
        $pos = new Vector3($this->pos->getX(), $this->pos->getY()+0.5, $this->pos->getZ());
        $task = new SpawnParticleTask($this, $pos, $player->getLevel());
        $handler = $this->arena->getPlugin()->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handler);
        $this->st[$task->getTaskId()] = true;
    }

    public function getPos() : Vector3{
        return $this->pos;
    }

    public function getName() : string {
        return $this->name;
    }

    public function removeTask($id) : void{
        unset($this->st[$id]);
        $this->arena->getPlugin()->getScheduler()->cancelTask($id);
    }

}