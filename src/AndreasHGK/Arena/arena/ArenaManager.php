<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\modes\Elimination;
use pocketmine\level\Level;
use pocketmine\Player;

class ArenaManager{

    private $plugin;
    private $arenas = [];

    public function __construct(Arena $plugin){
        $this->plugin = $plugin;
    }

    public function create(string $name, Player $creator, string $type) : void{
        switch(strtolower($type)){
            case "elimination":
                $arena = new Elimination($this->plugin, $name, $creator);
                break;
            default:
                $arena = new Elimination($this->plugin, $name, $creator);
                break;
        }
        array_push($this->arenas, $arena);
    }

    public function delete() : void{

    }

    public function getAll(){
        return $this->arenas;
    }
}