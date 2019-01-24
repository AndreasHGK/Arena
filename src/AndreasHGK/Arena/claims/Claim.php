<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\claims;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\module\ClaimsModule;
use pocketmine\level\Position;

class Claim {

    private $module;
    private $manager;

    private $owner;
    private $pos1;
    private $pos2;
    private $level;
    private $trusted = [];

    private $id;

    public function __construct(ClaimsModule $module, ClaimManager $manager, string $owner, Position $pos1, Position $pos2, string $level, int $id){
        $this->module = $module;
        $this->manager = $manager;
        $this->owner = $owner;
        $this->pos1 = $pos1;
        $this->pos2 = $pos2;
        $this->level = $level;
        $this->id = $id;
    }

    public function getPos1() : Position{
        return $this->pos1;
    }

    public function gePos2() : Position{
        return $this->pos2;
    }

    public function getOwner() : string {
        return $this->owner;
    }

    public function getLevel() : string{
        return $this->level;
    }

    public function trust(string $player) : void{
        $this->trust[$player] = true;
    }

    public function unTrust(string $player) : void{
        unset($this->trusted[$player]);
    }

    public function isTrusted(string $player) : bool{
        if(array_key_exists($player, $this->trusted)){
            return true;
        }else{
            return false;
        }
    }

    public function getTrusted() : array{
        return $this->trusted;
    }

    public function delete() : void{
        unset($this->manager->claims[$this->owner][$this->id]);
    }

    public function inClaim(Position $pos, string $level) : bool{
        if($level == $this->level && $pos->getX() >= min($this->pos1->getX(), $this->pos2->getX()) && $pos->getX() <= max($this->pos1->getX(), $this->pos2->getX()) && $pos->getY() >= min($this->pos1->getY(), $this->pos2->getY()) && $pos->getY() <= max($this->pos1->getY(), $this->pos2->getY()) && $pos->getZ() >= min($this->pos1->getZ(), $this->pos2->getZ()) && $pos->getZ() <= max($this->pos1->getZ(), $this->pos2->getZ())){
            return true;
        }
        return false;
    }

    public function getCorners() : array {
        $array = [];
        $array[1] = new Position($this->pos1->getX(), $this->pos1->getLevel()->getHighestBlockAt($this->pos1->getX(), $this->pos1->getZ()), $this->pos1->getZ(), $this->pos1->getLevel());
        $array[2] = new Position($this->pos1->getX(), $this->pos1->getLevel()->getHighestBlockAt($this->pos1->getX(), $this->pos2->getZ()), $this->pos2->getZ(), $this->pos1->getLevel());
        $array[3] = new Position($this->pos2->getX(), $this->pos2->getLevel()->getHighestBlockAt($this->pos2->getX(), $this->pos2->getZ()), $this->pos2->getZ(), $this->pos2->getLevel());
        $array[4] = new Position($this->pos2->getX(), $this->pos2->getLevel()->getHighestBlockAt($this->pos2->getX(), $this->pos1->getZ()), $this->pos1->getZ(), $this->pos2->getLevel());
        return $array;
    }
}