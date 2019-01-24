<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\task;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\level\Level;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\utils\Random;
use pocketmine\utils\TextFormat;

class HealthTagTask extends Task {

    public $arena;
    public $manager;

    public function __construct(Arena $arena, ArenaManager $manager){
        $this->arena = $arena;
        $this->manager = $manager;
    }

    public function onRun(int $currentTick){
        foreach($this->arena->getServer()->getOnlinePlayers() as $player){
            if($this->manager->playerIsInArena($player)){
                $hp = $player->getHealth();
                $tag = "";
                for($i = 0; $i < $hp; $i++){
                    $tag = $tag."&2|";
                }
                for($i = 0; $i < 20-$hp; $i++){
                    $tag = $tag."&4|";
                }
                $player->setScoreTag(TextFormat::colorize("&l&7[".$tag."&7]"));
            }elseif($player->getScoreTag() != NULL){
                $player->setScoreTag("");
            }
        }
    }

}