<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\module;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;

abstract class ModuleBase {

    /** @var Arena */
    public $arena;
    /** @var ArenaManager  */
    protected $manager;

    public function __construct(Arena $arena, ArenaManager $manager){
        $this->arena = $arena;
        $this->manager = $manager;
    }

    abstract public function execute() : void;
}