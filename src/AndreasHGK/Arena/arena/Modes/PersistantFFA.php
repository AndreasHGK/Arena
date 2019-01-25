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

    protected $dt = [];

    public function __construct(Arena $plugin, string $name, string $creator, string $level){
        parent::__construct($plugin, $name, $creator, $level);
        $this->type = "PersistantFFA";
        $this->ffa = true;
        $this->edit = false;
    }

}