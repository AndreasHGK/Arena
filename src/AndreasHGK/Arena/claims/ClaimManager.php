<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\claims;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\module\ClaimsModule;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\Random;
use AndreasHGK\Arena\claims\Claim;

class ClaimManager
{

    private $plugin;
    private $module;
    private $random;

    private $pos = [];
    public $claims = [];

    public function __construct(Arena $plugin, ClaimsModule $module)
    {
        $this->plugin = $plugin;
        $this->module = $module;
        $this->random = new Random();
    }

    public function getPlugin(): Arena
    {
        return $this->plugin;
    }

    public function addClaim(string $owner, Position $pos1, Position $pos2, string $level): void
    {
        $id = -1;
        for ($i = 0; $i < 999; $i++) {
            if (!isset($this->claims[$owner][$i])) {
                $id = $i;
                break;
            } else {
                continue;
            }
        }
        if ($id == -1) {
            return;
        } else {
            $this->claims[$owner][$id] = new Claim($this->module, $this, $owner, $pos1, $pos2, $level, $id);
        }
    }

    public function isClaimed(Position $pos, string $level): bool
    {
        foreach ($this->claims as $key) {
            foreach($key as $claim){
                if ($claim->inClaim($pos, $level)) {
                    return true;
                }
            }

        }
        return false;
    }

    public function ownsPos(Position $pos, string $level, Player $player): bool
    {
        foreach ($this->claims as $key) {
            foreach($key as $claim){
                if ($claim->inClaim($pos, $level)) {
                    if ($claim->getOwner() == $player->getName()) {
                        return true;
                    }
                }
            }

        }
        return false;
    }

    public function canEdit(Position $pos, string $level, Player $player): bool
    {
        foreach ($this->claims as $key) {
            foreach($key as $claim){
                if ($claim->inClaim($pos, $level)) {
                    if ($claim->getOwner() == $player->getName() || $claim->isTrusted($player->getName())) {
                        return true;
                    }
                }
            }

        }
        return false;
    }

    public function getClaim(Position $pos, string $level): Claim
    {
        foreach ($this->claims as $key) {
            foreach($key as $claim){
                if ($claim->inClaim($pos, $level)) {
                    return $claim;
                }
            }

        }
        return NULL;
    }

    public function setPos1(string $name, Position $pos): void
    {
        $this->pos[$name] = $pos;
    }

    public function unsetPos1(string $name): void
    {
        unset($this->pos[$name]);
    }

    public function getPos1(string $name): Position
    {
        if (isset($this->pos[$name])) {
            return ($this->pos[$name]);
        } else {
            return NULL;
        }
    }

    public function claim(string $name, Position $pos2): void
    {
        $pos1 = $this->getPos1($name);
        $this->unsetPos1($name);
        $this->addClaim($name, $pos1, $pos2, $pos2->getLevel()->getName());
    }
}
