<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;
use pocketmine\block\SnowLayer;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Position;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

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
       $this->onJoin($player);
    }

    public function removePlayer(Player $player) : void{
        unset($this->players[$player->getName()]);
        $this->onLeave($player);
    }

    abstract public function onKill(Player $killer, Player $killed) : void;
    abstract public function onDeath(Player $player) : void;

    public function getSpawns(){
        return $this->spawns;
    }

    public function hasPlayer(Player $player) : bool{
        if(in_array($player, $this->players)){
            return true;
        }
        return false;
    }

    public function addSpawn(string $name, Position $pos) : void{
        $this->spawns[$name] = new ArenaSpawn($this, $pos);
    }

    public function delSpawn(string $name) : void{
        if(isset($this->spawns[$name])){
            unset($this->spawns[$name]);
        }
    }

    public function getSpawn(string $name) : array{
        return $this->spawns[$name];
    }

    public function spawnExists(string $name) : bool{
        return array_key_exists($name, $this->spawns);
    }

    public function respawn(Player $player) : void{
        $spawn = array_rand($this->spawns);
        $this->spawns[$spawn]->spawnPlayer($player);
        $player->getArmorInventory()->setHelmet(ItemFactory::get(ItemIds::IRON_HELMET, 0, 1));
        $player->getArmorInventory()->setChestplate(ItemFactory::get(ItemIds::IRON_CHESTPLATE, 0, 1));
        $player->getArmorInventory()->setLeggings(ItemFactory::get(ItemIds::IRON_LEGGINGS, 0, 1));
        $player->getArmorInventory()->setBoots(ItemFactory::get(ItemIds::IRON_BOOTS, 0, 1));
        $inv = $player->getInventory();
        $inv->setItem(0, ItemFactory::get(ItemIds::DIAMOND_SWORD, 0, 1));
        $inv->setItem(1, ItemFactory::get(ItemIds::BOW, 0, 1));
        $inv->setItem(2, ItemFactory::get(ItemIds::ARROW, 0, 64));
        $inv->setItem(8, ItemFactory::get(ItemIds::STEAK, 0, 64));
    }

    public function onJoin(Player $player) : void{
        $player->getInventory()->clearAll();
        $this->respawn($player);
        $player->addTitle(TextFormat::colorize("&l&8[&c!&8]"));
        $player->addSubTitle(TextFormat::colorize("&7Joined arena &c".$this->name));
        $player->getLevel()->addSound(new EndermanTeleportSound($player), $player->getViewers());
        $player->setHealth(20);
        $player->setFood(20);
        $player->removeAllEffects();
        $player->setGamemode(0);
    }

    public function onLeave(Player $player) : void{
        $player->teleport($player->getLevel()->getSafeSpawn());
        $player->addTitle(TextFormat::colorize("&l&8[&c!&8]"));
        $player->addSubTitle(TextFormat::colorize("&7Left arena &c".$this->name));
        $player->setHealth(20);
        $player->setFood(20);
        $player->removeAllEffects();
        $player->getInventory()->clearAll();
        $player->setGamemode(1);
    }

    public function isInArena(Position $pos) : bool{
        if($pos->getX() > min($this->pos1->getX(), $this->pos2->getX()) && $pos->getX() < max($this->pos1->getX(), $this->pos2->getX()) && $pos->getY() > min($this->pos1->getY(), $this->pos2->getY()) && $pos->getY() < max($this->pos1->getY(), $this->pos2->getY()) && $pos->getZ() > min($this->pos1->getZ(), $this->pos2->getZ()) && $pos->getZ() < max($this->pos1->getZ(), $this->pos2->getZ())){
            return true;
        }
        return false;
    }
}