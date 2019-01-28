<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena;

use AndreasHGK\Arena\Arena;

use AndreasHGK\Arena\task\DeathParticleTask;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\level\Position;
use pocketmine\level\sound\AnvilFallSound;
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
    protected $level;
    protected $status;

    protected $running;

    protected $players = [];
    protected $ffa;
    protected $spawns = [];

    protected $edit;
    protected $dt = [];
    protected $timed;
    protected $max;
    protected $minplayers;


    public function __construct(Arena $arena, string $name, string $creator, string $level){
        $this->active = false;
        $this->arena = $arena;
        $this->name = $name;
        $this->creator = $creator;
        $this->type = "default";
        $this->ffa = true;
        $this->edit = false;
        $this->level = $level;
        $this->timed = false;
        $this->max = 10;
        $this->running = true;
        $this->minplayers = 1;
        $this->status = 0;
    }

    public function allowSkip() : bool{
        return false;
    }

    public function getMinPlayers() : int{
        return $this->minplayers;
    }

    public function getStatus() : int{
        return $this->status;
    }

    public function getStatusString() : string {
        if($this->status == 0){
            return "community";
        }elseif($this->status == 1){
            return "featured";
        }elseif($this->status == 2){
            return "official";
        }
    }

    public function setStatus(int $status) : void{
        $this->status = $status;
    }

    public function setRunning() : void{
        $this->running = true;
    }

    public function unsetRunning() : void{
        $this->running = false;
    }

    public function isTimed() : bool{
        return $this->timed;
    }

    public function getMaxPlayers() : int{
        return $this->max;
    }

    public function getPlayerCount() : int{
        return count($this->players);
    }

    public function isEditable() : bool{
        return $this->edit;
    }

    public function getLevel() : string {
        return $this->level;
    }

    public function getPlugin() : Arena{
        return $this->arena;
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

    public function pos1Isset() : bool{
        return isset($this->pos1);
    }

    public function pos2Isset() : bool{
        return isset($this->pos2);
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

    public function getCreator() : string{
        return $this->creator;
    }

    public function getPlayers() : array{
       return $this->players;
    }

    public function addPlayer(Player $player) : void{
       $this->players[$player->getName()] = $player;
       $this->onJoin($player);
    }

    public function removePlayer(Player $player, bool $silent = false, bool $no = false) : void{
        unset($this->players[$player->getName()]);
        $this->onLeave($player, $silent, $no);
    }

    public function isRunning() : bool{
        return $this->running;
    }

    public function canJoin() : bool{
        if($this->getPlayerCount() < $this->getMaxPlayers()){
            return true;
        }else{
            return false;
        }
    }

    public function isFull() : bool{
        if($this->getMaxPlayers() == $this->getPlayerCount()){
            return true;
        }else{
            return false;
        }
    }

    public function onKill(Player $killer, Player $killed) : void
    {
        $killer->addActionBarMessage(TextFormat::colorize("&7Killed player &4".$killed->getName()));
        $distance = round(sqrt(pow($killed->getX() - $killer->getX(), 2) + pow($killed->getY() - $killer->getY(), 2) + pow($killed->getZ() - $killer->getZ(), 2)), 1);
        $this->broadcast("&l&8[&c!&8]&r&7 player &c".$killer->getName()."&7 killed &c".$killed->getName()."&8 (".$distance."m)");
    }

    public function onDeath(Player $player) : void{
        $level = $player->getLevel();
        $pos = new Vector3($player->getX(), $player->getY()+0.5, $player->getZ());

        $task = new DeathParticleTask($this, $pos, $player->getLevel());
        $handler = $this->arena->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handler);
        $this->dt[$task->getTaskId()] = true;

        $level->addSound(new AnvilFallSound($pos));
        $player->setHealth(20);
        $player->setFood(20);
        $player->removeAllEffects();
        $player->getInventory()->clearAll();
        $this->respawn($player);
    }

    public function removeTask($id) : void{
        unset($this->dt[$id]);
        $this->arena->getScheduler()->cancelTask($id);
    }

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
        $this->spawns[$name] = new ArenaSpawn($this, $pos, $name);
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
        $bow = ItemFactory::get(ItemIds::BOW, 0, 1);
        $ench1 = Enchantment::getEnchantment(19);
        $enchInstance = new EnchantmentInstance($ench1, 3);
        $enchInstance->setLevel(2);
        $bow->addEnchantment($enchInstance);
        $inv->setItem(1, $bow);
        $inv->setItem(2, ItemFactory::get(ItemIds::ARROW, 0, 64));
    }

    public function onJoin(Player $player) : void{
        if(!$this->canJoin()){
            $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 This arena is full"));
            return;
        }
        $player->getInventory()->clearAll();
        $this->respawn($player);
        $player->addTitle(TextFormat::colorize("&l&8[&c!&8]"));
        $player->addSubTitle(TextFormat::colorize("&7Joined arena &c".$this->name));
        $player->setHealth(20);
        $player->setFood(20);
        $player->removeAllEffects();
        $player->setGamemode(0);
        $this->broadcast("&l&8[&c!&8]&r&7 Player &c".$player->getName()."&7 joined the arena &8(".$this->getPlayerCount()."/".$this->getMaxPlayers().")");
    }

    public function broadcast(string $message) : void{
        foreach($this->players as $player){
            $player->sendMessage(TextFormat::colorize($message));
        }
    }

    public function broadcastTitle(string $message) : void{
        foreach($this->players as $player){
            $player->addTitle(TextFormat::colorize($message));
        }
    }

    public function broadcastSubTitle(string $message) : void{
        foreach($this->players as $player){
            $player->addSubTitle(TextFormat::colorize($message));
        }
    }

    public function onLeave(Player $player, bool $silent = false, bool $noshowpop = false) : void{
        $player->teleport($this->arena->getServer()->getLevelByName($this->arena->cfg["spawnworld"])->getSafeSpawn());
        $player->setHealth(20);
        $player->setFood(20);
        $player->removeAllEffects();
        $player->getArmorInventory()->clearAll();
        $player->getInventory()->clearAll();
        $player->setGamemode(1);
        if(!$noshowpop){
            $player->addTitle(TextFormat::colorize("&l&8[&c!&8]"));
            $player->addSubTitle(TextFormat::colorize("&7Left arena &c".$this->name));
        }
        if(!$silent){

            $this->broadcast("&l&8[&c!&8]&r&7 Player &c".$player->getName()."&7 left the arena &8(".$this->getPlayerCount()."/".$this->getMaxPlayers().")");
        }
    }

    public function kickAll(bool $silent = true, bool $no = false) : void{
        foreach($this->getPlayers() as $player){
            $this->removePlayer($player, $silent, $no);
        }
    }

    public function isInArena(Position $pos) : bool{
        if($pos->getLevel()->getName() == $this->level && $pos->getX() >= min($this->pos1->getX(), $this->pos2->getX()) && $pos->getX() <= max($this->pos1->getX(), $this->pos2->getX()) && $pos->getY() >= min($this->pos1->getY(), $this->pos2->getY()) && $pos->getY() <= max($this->pos1->getY(), $this->pos2->getY()) && $pos->getZ() >= min($this->pos1->getZ(), $this->pos2->getZ()) && $pos->getZ() <= max($this->pos1->getZ(), $this->pos2->getZ())){
            return true;
        }
        return false;
    }

    public function isInBottomLayer(Position $pos) : bool{
        if($pos->getLevel()->getName() == $this->level && $pos->getY() <= min($this->pos1->getY(), $this->pos2->getY())){
            return true;
        }
        return false;
    }
}