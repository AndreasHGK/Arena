<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\modes\Elimination;
use AndreasHGK\Arena\arena\modes\PersistantFFA;
use AndreasHGK\Arena\arena\TimedArena;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\LabTablePacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ArenaManager{

    private $plugin;
    private $arenas = [];

    public function __construct(Arena $plugin){
        $this->plugin = $plugin;
    }

    public function create(string $name, string $creator, string $type, string $level) : void{
        switch(strtolower($type)){
            case "freeforall":
            case "ffa":
                $arena = new PersistantFFA($this->plugin, $name, $creator, $level);
                break;
            case "elimination":
                $arena = new Elimination($this->plugin, $name, $creator, $level);
                break;
            default:
                $arena = new PersistantFFA($this->plugin, $name, $creator, $level);
                break;
        }
        $this->arenas[$arena->getName()] = $arena;
    }

    public function delete(string $name) : void{
        if(isset($this->arenas[$name])){
            unset($this->arenas[$name]);
        }
    }

    public function getAll() : array {
        return $this->arenas;
    }

    public function getAllSorted() : array{
        $arenas = [];
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 2){
                array_push($arenas, $arena);
            }
        }
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 1){
                array_push($arenas, $arena);
            }
        }
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 0){
                array_push($arenas, $arena);
            }
        }
        return $arenas;
    }

    public function getAllOwned(string $player) : array{
        $arenas = [];
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 2 && $arena->getCreator() == $player){
                array_push($arenas, $arena);
            }
        }
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 1 && $arena->getCreator() == $player){
                array_push($arenas, $arena);
            }
        }
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 0 && $arena->getCreator() == $player){
                array_push($arenas, $arena);
            }
        }
        return $arenas;
    }

    public function getAllActive() : array{
        $arenas = [];
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 2 && $arena->isActive()){
                array_push($arenas, $arena);
            }
        }
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 1 && $arena->isActive()){
                array_push($arenas, $arena);
            }
        }
        foreach($this->arenas as $arena){
            if($arena->getStatus() == 0 && $arena->isActive()){
                array_push($arenas, $arena);
            }
        }
        return $arenas;
    }

    public function getArena(string $name){
        if(isset($this->arenas[$name])){
            return $this->arenas[$name];
        }
    }

    public function arenaExists(string $name) : bool{
        return array_key_exists($name, $this->arenas);
    }

    public function playerIsInArena($player) : bool{
        foreach($this->arenas as $arena){
            if($arena->hasPlayer($player)){
                return true;
            }
        }
        return false;
    }

    public function getPlayerArena($player){
        foreach($this->arenas as $arena){
            if($arena->hasPlayer($player)){
                return $arena;
            }
        }
        return NULL;
    }

    public function playerJoin($player, string $arena) : void{
        $arena = $this->getArena($arena);
        if($arena->isActive() == false){
            $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 That arena is not activated"));
            return;
        }
        if(!$this->playerIsInArena($player)){
            if(isset($arena)){
                $player->teleport($this->plugin->getServer()->getLevelByName($arena->getLevel())->getSafeSpawn());
                $arena->addPlayer($player);
            }
        }
    }

    public function playerLeave($player) : void{
        if($this->playerIsInArena($player)){
            $arena = $this->getPlayerArena($player);
            $arena->removePlayer($player);
        }
    }
}