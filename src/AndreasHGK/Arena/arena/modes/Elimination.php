<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena\modes;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaClass;
use AndreasHGK\Arena\arena\TimedArena;
use AndreasHGK\Arena\task\DeathParticleTask;
use pocketmine\level\particle\EnchantParticle;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\SporeParticle;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Elimination extends TimedArena {

    public function __construct(Arena $plugin, string $name, string $creator, string $level){
        parent::__construct($plugin, $name, $creator, $level);
        $this->type = "Elimination";
        $this->ffa = true;
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
            $player->addSubTitle(TextFormat::colorize("&7You got &celiminated"));
        }
        if(!$silent){

            $this->broadcast("&l&8[&c!&8]&r&7 Player &c".$player->getName()."&7 left the arena &8(".$this->getPlayerCount()."/".$this->getMaxPlayers().")");
        }
        if($this->getPlayerCount() <= 1 && ($this->isRunning() || $this->isWaiting())){
            $this->stop();
        }
    }

    public function onStop() : void{
        $this->broadcastTitle(TextFormat::colorize("&l&8[&c!&8]"));
        $this->broadcastSubTitle(TextFormat::colorize("&7You are &avictorious"));
    }

    public function onDeath(Player $player) : void{
        $level = $player->getLevel();
        $pos = new Vector3($player->getX(), $player->getY()+0.5, $player->getZ());

        $task = new DeathParticleTask($this, $pos, $player->getLevel());
        $handler = $this->arena->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handler);
        $this->dt[$task->getTaskId()] = true;

        $level->addSound(new AnvilFallSound($pos));
        $this->removePlayer($player, true);
    }

}