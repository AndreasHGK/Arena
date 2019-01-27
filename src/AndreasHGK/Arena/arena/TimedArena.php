<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\task\ArenaTimerTask;
use AndreasHGK\Arena\task\DeathParticleTask;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\GenericSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TimedArena extends ArenaClass {

    protected $dt = [];

    /** @var ArenaTimerTask */
    protected $timer;
    protected $time;
    protected $countdown;

    public function __construct(Arena $plugin, string $name, string $creator, string $level){
        parent::__construct($plugin, $name, $creator, $level);
        $this->type = "timed";
        $this->timed = true;
        $this->time = 5000;
        $this->countdown = 1200;
        $this->running = false;
        $this->max = 10;
        $this->minplayers = 2;
    }

    public function onJoin(Player $player) : void{
        if($this->isRunning()){
            $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 This arena is already running"));
            return;
        }
        parent::onJoin($player);
        if($this->canStart()){
            $this->startTimer();
        }
        if($this->isFull()){
            $this->timer->skipWait();
        }
    }

    public function canStart() : bool{
        if($this->getPlayerCount() >= $this->getMinPlayers() && !$this->isRunning()){
            return true;
        }else{
            return false;
        }
    }

    public function startTimer() : void{
        $this->broadcast("&l&8[&c!&8]&r&7 Starting timer...");
        $task = new ArenaTimerTask($this->getPlugin(), $this->getPlugin()->manager, $this, $this->time, $this->countdown);
        $handler = $this->arena->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handler);
        $this->timer = $task;
    }

    public function stop() : void{
        $this->kickAll(true, true);
        $this->unsetRunning();
        $this->onStop();
    }

    public function removeTimer($id) : void
    {
        unset($this->timer);
        $this->arena->getScheduler()->cancelTask($id);
    }

    public function broadcastCountSound() : void{
        foreach($this->players as $player){
            $player->getLevel()->addSound(new ClickSound($player, 3));
        }
    }

    public function broadcastStartSound() : void{
        foreach($this->players as $player){
            $player->getLevel()->addSound(new GenericSound($player, 1052, 3));
        }
    }

    public function onStartCountDown(int $seconds) : void{
        $this->broadcastTitle("&c".$seconds);
        $this->broadcastCountSound();
    }

    public function onStart() : void{
        $this->setRunning();
        $this->broadcast("&l&8[&c!&8]&r&7 The game has begun");
        $this->broadcastTitle("&cStart!");
        $this->broadcastStartSound();
    }

    public function onStop() : void{
        $this->broadcastTitle(TextFormat::colorize("&l&8[&c!&8]"));
        $this->broadcastSubTitle(TextFormat::colorize("&7You are &avictorious"));
    }

    public function onMinuteNotice(int $minutes) : void{
        $this->broadcast("&l&8[&c!&8]&r&7 There are &c".$minutes."&7 minutes left");
    }

    public function onStopCountDown(int $seconds) : void{
        $this->broadcast("&l&8[&c!&8]&r&7 There are &c".$seconds."&7 seconds left");
    }

    public function onCountdownNotice(int $seconds) : void{
        $this->broadcast("&l&8[&c!&8]&r&7 Starting in &c".$seconds."&7 seconds");
    }

    public function onDeath(Player $player) : void{
        $level = $player->getLevel();
        $pos = new Vector3($player->getX(), $player->getY()+0.5, $player->getZ());

        $task = new DeathParticleTask($this, $pos, $player->getLevel());
        $handler = $this->arena->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handler);
        $this->dt[$task->getTaskId()] = true;

        $level->addSound(new AnvilFallSound($pos));
        $this->removePlayer($player, true);
    }

    public function onLeave(Player $player, bool $silent = false, bool $noshowpop = false) : void{
        $player->teleport($this->arena->getServer()->getLevelByName($this->arena->cfg["spawnworld"])->getSafeSpawn());
        $player->setHealth(20);
        $player->setFood(20);
        $player->removeAllEffects();
        $player->getArmorInventory()->clearAll();
        $player->getInventory()->clearAll();
        $player->setGamemode(1);
        if(!$noshowpop){
            $player->addTitle(TextFormat::colorize("&l&8[&c!&8]"));
            $player->addSubTitle(TextFormat::colorize("&7You got &celiminated"));
        }
        if(!$silent){

            $this->broadcast("&l&8[&c!&8]&r&7 Player &c".$player->getName()."&7 left the arena &8(".$this->getPlayerCount()."/".$this->getMaxPlayers().")");
        }
        if($this->getPlayerCount() == 1){
            $this->stop();
        }
    }

}