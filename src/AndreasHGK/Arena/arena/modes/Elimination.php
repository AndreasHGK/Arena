<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena\modes;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaClass;
use AndreasHGK\Arena\arena\TimedArena;
use AndreasHGK\Arena\task\ArenaTimerTask;
use AndreasHGK\Arena\task\DeathParticleTask;

use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Elimination extends TimedArena {

    public function __construct(Arena $plugin, string $name, string $creator, string $level){
        parent::__construct($plugin, $name, $creator, $level);
        $this->type = "Elimination";
        $this->ffa = true;
    }

    public function onStop() : void{
        $this->winnertitle("&l&8[&c!&8]");
        $this->winnersubtitle("&7You are &avictorious");
        foreach($this->getPlayers() as $player){
            $player->setGamemode(0);
        }
        if($this->canStart()){
            $this->startTimer();
        }
        if($this->isFull()){
            $this->timer->skipWait();
        }
    }

    public function stop() : void{
        $this->onStop();
        $this->timer->stop();
        $this->waiting = false;
    }

    public function getAlive() : array{
        $p = [];
        foreach($this->getPlayers() as $player){
            if($player->getGamemode() == 0){
                array_push($p, $player);
            }
        }
        return $p;
    }

    public function winnertitle(string $txt) : void{
        foreach($this->getAlive() as $winner){
            $winner->addTitle(TextFormat::colorize($txt));
        }
    }

    public function winnersubtitle(string $txt) : void{
        foreach($this->getAlive() as $winner){
            $winner->addSubTitle(TextFormat::colorize($txt));
        }
    }

    public function spectate(Player $player) : void{
        $player->setGamemode(3);
        $player->addTitle(TextFormat::colorize("&l&8[&c!&8]"));
        $player->addSubTitle(TextFormat::colorize("&7You got &celiminated"));
    }

    public function onDeath(Player $player) : void{
        $level = $player->getLevel();
        $pos = new Vector3($player->getX(), $player->getY()+0.5, $player->getZ());

        $task = new DeathParticleTask($this, $pos, $player->getLevel());
        $handler = $this->arena->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handler);
        $this->dt[$task->getTaskId()] = true;

        $level->addSound(new AnvilFallSound($pos));
        $this->spectate($player);
        if(count($this->getAlive()) < 2){
            $this->stop();
        }elseif(empty($this->getAlive())){
            $this->stop();
        }
    }

}