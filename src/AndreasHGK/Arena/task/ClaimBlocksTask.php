<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\task;

use _64FF00\PurePerms\PurePerms;
use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\claims\ClaimManager;
use AndreasHGK\Arena\module\ClaimsModule;
use pocketmine\scheduler\Task;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

class ClaimBlocksTask extends Task {

    public $arena;
    public $cm;

    public function __construct(Arena $arena, ClaimsModule $cm){
        $this->arena = $arena;
        $this->cm = $cm;
    }

    public function onRun(int $currentTick){
        $this->arena->getLogger()->debug("adding claim blocks");
        foreach($this->cm->players["players"] as $player){
            $blocks = $player["blocks"];
            $this->cm->players["players"][$player["name"]]["blocks"] = $blocks + $this->arena->cfg["claimincome"];
        }
    }

}