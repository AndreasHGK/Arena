<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\claims;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\module\ClaimsModule;
use pocketmine\block\BlockIds;
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

    public function getPos2() : Position{
        return $this->pos2;
    }

    public function getOwner() : string {
        return $this->owner;
    }

    public function getID() : int{
        return $this->id;
    }

    public function getLevel() : string{
        return $this->level;
    }

    public function trust(string $player) : void{
        $this->trusted[$player] = $player;
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
        if($level == $this->level && $pos->getX() >= min($this->pos1->getX(), $this->pos2->getX()) && $pos->getX() <= max($this->pos1->getX(), $this->pos2->getX()) && $pos->getZ() >= min($this->pos1->getZ(), $this->pos2->getZ()) && $pos->getZ() <= max($this->pos1->getZ(), $this->pos2->getZ())){
            return true;
        }
        return false;
    }

    public function displayCorners() : void{
        $corners = $this->getCorners();
        foreach($corners as $corner){
            $this->module->sendBlocks(BlockIds::GLOWSTONE, $corner);
            $ca = [
                new Position($corner->getX()+1, $corner->getLevel()->getHighestBlockAt($corner->getX()+1, $corner->getZ()), $corner->getZ(), $corner->getLevel()),
                new Position($corner->getX()-1, $corner->getLevel()->getHighestBlockAt($corner->getX()-1, $corner->getZ()), $corner->getZ(), $corner->getLevel()),
                new Position($corner->getX(), $corner->getLevel()->getHighestBlockAt($corner->getX(), $corner->getZ()+1), $corner->getZ()+1, $corner->getLevel()),
                new Position($corner->getX(), $corner->getLevel()->getHighestBlockAt($corner->getX(), $corner->getZ()-1), $corner->getZ()-1, $corner->getLevel())
            ];
            foreach($ca as $c){
                if($this->inClaim($c, $c->getLevel()->getName())){
                    $this->module->sendBlocks(BlockIds::GOLD_BLOCK, $c);
                }else{
                    $this->module->sendBlocks($c->getLevel()->getBlockIdAt($c->getX(), $c->getY(), $c->getZ()), $c);
                }
            }
        }
    }

    public function getDimensions() : array{
        $dim = [];
        $dim[0] = abs($this->pos1->getX()-$this->pos2->getX());
        $dim[1] = abs($this->pos1->getZ()-$this->pos2->getZ());
        return $dim;
    }

    public function getSize() : int{
        $dim = $this->getDimensions();
        return $dim[0]*$dim[1];
    }

    public function getCorners() : array {
        $array = [];
        $array[1] = new Position($this->pos1->getX(), $this->pos1->getLevel()->getHighestBlockAt($this->pos1->getX(), $this->pos1->getZ()), $this->pos1->getZ(), $this->pos1->getLevel());
        $array[2] = new Position($this->pos1->getX(), $this->pos1->getLevel()->getHighestBlockAt($this->pos1->getX(), $this->pos2->getZ()), $this->pos2->getZ(), $this->pos1->getLevel());
        $array[3] = new Position($this->pos2->getX(), $this->pos2->getLevel()->getHighestBlockAt($this->pos2->getX(), $this->pos2->getZ()), $this->pos2->getZ(), $this->pos2->getLevel());
        $array[4] = new Position($this->pos2->getX(), $this->pos2->getLevel()->getHighestBlockAt($this->pos2->getX(), $this->pos1->getZ()), $this->pos1->getZ(), $this->pos2->getLevel());
        return $array;
    }

    public function getAllPositions() : array{
        $positions = [];
        for($x = min($this->getPos1()->getX(), $this->getPos2()->getX()); $x <= max($this->getPos1()->getX(), $this->getPos2()->getX()); $x++){
            for($z = min($this->getPos1()->getZ(), $this->getPos2()->getZ()); $z <= max($this->getPos1()->getZ(), $this->getPos2()->getZ()); $z++){
                $pos = new Position($x, 256, $z, $this->module->arena->getServer()->getLevelByName($this->getLevel()));
                array_push($positions, $pos);
            }
        }
        return $positions;
    }
}