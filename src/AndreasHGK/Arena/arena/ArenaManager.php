<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;

class ArenaManager{

    private $plugin;
    private $arenas = [];

    public function __construct(Arena $plugin){
        $this->plugin = $plugin;
    }

    public function create() : void{

    }

    public function delete() : void{

    }
}