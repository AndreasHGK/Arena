<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\arena\modes;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaClass;
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

class PersistantFFA extends ArenaClass {

    protected $type;
    protected $dt = [];

    public function __construct(Arena $plugin, string $name, string $creator){
        parent::__construct($plugin, $name, $creator);
        $this->type = "PersistantFFA";
        $this->ffa = true;
    }

    public function onKill(Player $killer, Player $killed) : void
    {
        $killer->addActionBarMessage(TextFormat::colorize("&7Killed player &4".$killed->getName()));
        $distance = round(sqrt(pow($killed->getX() - $killer->getX(), 2) + pow($killed->getY() - $killer->getY(), 2) + pow($killed->getZ() - $killer->getZ(), 2)), 2);
        foreach($this->getPlayers() as $player){
            $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 player &c".$killer->getName()."&7 killed &c".$killed->getName()."&8 (".$distance.")"));
        }
    }

    public function onDeath(Player $player) : void{
        $level = $player->getLevel();
        $pos = new Vector3($player->getX(), $player->getY()+0.5, $player->getZ());

        $task = new DeathParticleTask($this, $pos, $player->getLevel());
        $handler = $this->arena->getScheduler()->scheduleRepeatingTask($task, 1);
        $task->setHandler($handler);
        $this->dt[$task->getTaskId()] = true;

        $level->addSound(new AnvilFallSound($pos));
        $level->addParticle(new EnchantParticle($pos));
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

}