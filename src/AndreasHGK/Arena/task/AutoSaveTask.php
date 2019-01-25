<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\task;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\claims\ClaimManager;
use AndreasHGK\Arena\module\ClaimsModule;
use pocketmine\level\Level;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

class AutoSaveTask extends Task {

    public $arena;
    public $manager;
    public $cm;

    public function __construct(Arena $arena, ArenaManager $manager, ClaimsModule $cm){
        $this->arena = $arena;
        $this->manager = $manager;
        $this->cm = $cm;
    }

    public function onRun(int $currentTick){
        $this->arena->getLogger()->info("autosaving...");
        $this->arena->save();
        $this->cm->save();
    }

}