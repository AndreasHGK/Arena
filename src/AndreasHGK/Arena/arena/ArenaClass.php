<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use pocketmine\level\Position;
use pocketmine\Player;

abstract class ArenaClass{

    protected $arena;
    protected $active;

    protected $pos1;
    protected $pos2;
    protected $name;
    protected $creator;
    protected $type;

    protected $players = [];
    protected $ffa;
    protected $spawns = [];

    public function __construct(Arena $arena, string $name, Player $creator){
        $this->active = false;
        $this->arena = $arena;
        $this->name = $name;
        $this->creator = $creator;
        $this->type = "default";
        $this->ffa = true;
    }

    public function setPos1(Position $pos1) : void{
        $this->pos1 = $pos1;
    }

    public function setPos2(Position $pos2) : void{
        $this->pos2 = $pos2;
    }

    public function getPos1() : Position{
       return $this->pos1;
    }

    public function getPos2() : Position{
        return $this->pos2;
    }

    public function activate() : void{
       if(isset($this->pos1) && isset($this->pos2) && !empty($this->spawns)){
           $this->active = true;
       }
    }

    public function deactivate() : void{
       $this->active = false;
    }

    public function isActive() : bool{
       return $this->active;
    }

    public function getName() : string{
       return $this->name;
    }

    public function getType(): string{
        return $this->type;
    }

    public function getPlayers() : array{
       return $this->players;
    }

    public function addPlayer(Player $player) : void{
       $this->players[$player->getName()] = $player;
    }

    public function removePlayer(Player $player) : void{
        unset($this->players[$player->getName()]);
    }

    abstract public function onKill(Player $killer, Player $killed) : void;

    public function getSpawns(){
        return $this->spawns;
    }

    public function hasPlayer(Player $player) : bool{
        if(in_array($player, $this->players)){
            return true;
        }
        return false;
    }
}