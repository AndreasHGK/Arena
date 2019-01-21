<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\module;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\task\HealthTagTask;

class HealthTagModule extends ModuleBase {

    private $task;

    public function execute() : void{
        $task = new HealthTagTask($this->arena, $this->manager);
        $handler = $this->arena->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handler);
        $this->task = $task;
        $this->arena->getLogger()->debug("enabled module HealthTags");
    }

}