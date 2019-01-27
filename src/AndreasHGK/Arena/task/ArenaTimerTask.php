<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\task;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\arena\TimedArena;
use pocketmine\scheduler\Task;

class ArenaTimerTask extends Task {

    public $plugin;
    public $manager;
    public $arena;

    public $ticks;
    public $time;
    public $countdown;

    public function __construct(Arena $plugin, ArenaManager $manager, TimedArena $arena, int $time, int $countdown){
        $this->plugin = $plugin;
        $this->manager = $manager;
        $this->arena = $arena;
        $this->time = $time;
        $this->ticks = -$countdown;
    }

    public function onRun(int $currentTick)
    {
        if ($this->ticks >= $this->time) {
            $this->arena->stop();
            $this->arena->removeTimer($this->getTaskId());
            return;
        }
        $this->ticks++;
        if ($this->ticks < 0 && $this->ticks >= -100) {
            if ($this->ticks % 20 == 0) {
                $this->arena->onStartCountDown(abs($this->ticks / 20));
                return;
            }
        }
        if ($this->ticks < 0) {
            if ($this->ticks % 200 == 0) {
                $this->arena->onCountdownNotice(abs($this->ticks / 20));
                return;
            }
        }
        if ($this->ticks == 0) {
            $this->arena->onStart();
            return;
        }
        if ($this->ticks >= $this->time - 100) {
            if ($this->ticks % 20 == 0) {
                $this->arena->onStopCountDown($this->time / 20 - $this->ticks / 20);
                return;
            }
        }
        if ($this->ticks % 1000 == 0) {
            $this->arena->onMinuteNotice($this->time / 1000 - $this->ticks / 1000);
            return;
        }
    }

    public function skipWait() : void{
        $this->ticks = -100;
    }

    public function restartWait() : void{
        $this->ticks = -($this->countdown);
    }

}