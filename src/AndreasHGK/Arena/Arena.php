<?php

declare(strict_types=1);

namespace AndreasHGK\Arena;

use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\module\HealthTagModule;
use Ds\Vector;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\command\PluginCommand;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

use AndreasHGK\Arena\commands\ArenaCommand;
use pocketmine\utils\TextFormat;

class Arena extends PluginBase implements Listener {

    /** @var ArenaManager */
    public $manager;

    private $pos = [];
    private $posa = [];

    public $cfg;
    /** @var Config */
    public $arenas;
    private $save;

    private $hpmodule;

    private $format = [
        "name" => "default",
        "active" => false,
        "creator" => "player",
        "pos1x" => NULL,
        "pos1y" => NULL,
        "pos1z" => NULL,
        "pos2x" => NULL,
        "pos2y" => NULL,
        "pos2z" => NULL,
        "spawns" => [],
        "type" => "PersistantFFA"
    ];

	public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

	    $cmd = new PluginCommand("arena", $this);
	    $cmd->setExecutor(new ArenaCommand($this, $this->manager));
	    $cmd->setDescription("join or create arenas");
	    $cmd->setPermission("arena.command");
	    $this->getServer()->getCommandMap()->register("arena", $cmd, "arena");
	    if($this->cfg["healthtags"]){
	        $this->hpmodule = new HealthTagModule($this, $this->manager);
	        $this->hpmodule->execute();
        }
	}

	public function onLoad(){
        $this->manager = new ArenaManager($this);
        $this->saveResource("arenas.json");
        $config = new Config($this->getDataFolder()."arenas.json",Config::JSON,[
            "arenas" => []
        ]);
        $this->arenas = $config;
        $this->save = $config->getAll();
        $this->arenas->save();
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->cfg = $this->getConfig()->getAll();
        $this->load();
    }

    public function getArenaManager() : ArenaManager{
	    return $this->manager;
    }

    public function onHunger(PlayerExhaustEvent $event){
	    $event->setCancelled();
    }

    public function onPlayerCmd(PlayerCommandPreprocessEvent $event) {
	    $msg = explode(" ", $event->getMessage());
	    $player = $event->getPlayer();
	    if($this->manager->playerIsInArena($player) && in_array($msg[0], $this->cfg["blockcommands"])){
            $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
	        $event->setCancelled();
        }
    }

	public function onDeath(PlayerDeathEvent $event){
        $player = $event->getPlayer();
        if($event->getPlayer()->getLastDamageCause() instanceof EntityDamageByEntityEvent) {
            if($event->getPlayer()->getLastDamageCause()->getDamager() instanceof Player) {
                $killer = $player->getLastDamageCause()->getDamager();
                if($this->manager->playerIsInArena($player) && $this->manager->playerIsInArena($killer)){
                    $arena = $this->manager->getPlayerArena($player);
                    $arena->onKill($killer, $player);
                }
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event){
	    $player = $event->getPlayer();
	    if($this->manager->playerIsInArena($player)){
	        $this->manager->playerLeave($player);
        }
    }

    public function onDamage(EntityDamageEvent $event){
        if($event->getFinalDamage() >= $event->getEntity()->getHealth() && $event->getEntity() instanceof Player) {
            $player = $event->getEntity();
            if($this->manager->playerIsInArena($player)){
                $this->manager->getPlayerArena($player)->onDeath($player);
                if($event->getCause() instanceof Player){
                    $this->manager->getPlayerArena($player)->onDeath($player);
                }
                $event->setCancelled();
            }

        }
    }

    public function onEntityDamage(EntityDamageByEntityEvent $event){
        if($event->getFinalDamage() >= $event->getEntity()->getHealth() && $event->getEntity() instanceof Player && $event->getDamager() instanceof Player) {
            $player = $event->getEntity();
            if($this->manager->playerIsInArena($player)){
                $killer = $event->getDamager();
                $this->manager->getPlayerArena($player)->onKill($killer, $player);
                $event->setCancelled();
            }

        }
    }

    public function onMove(PlayerMoveEvent $event){
	    $player = $event->getPlayer();
	    if($this->manager->playerIsInArena($player)){
            $arena = $this->manager->getPlayerArena($player);
            if(!$arena->isInArena($player->getPosition())){
                $event->setCancelled();
            }
        }
    }

    public function onBreak(BlockBreakEvent $event){
	    $player = $event->getPlayer();
        foreach($this->manager->getAll() as $arena){
            if($arena->isActive()){
                if($arena->isInArena($event->getBlock())){
                    $event->setCancelled();
                    return;
                }
            }
        }
	    if(isset($this->pos[$player->getName()])){
            if($this->pos[$player->getName()] == 1){
                $pos = new Position($event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ());
                $this->manager->getArena($this->posa[$player->getName()])->setPos1($pos);
                unset($this->pos[$player->getName()]);
                unset($this->posa[$player->getName()]);
                $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Set first position"));
            }elseif($this->pos[$player->getName()] == 2){
                $pos = new Position($event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ());
                $this->manager->getArena($this->posa[$player->getName()])->setPos2($pos);
                unset($this->pos[$player->getName()]);
                unset($this->posa[$player->getName()]);
                $player->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 Set second position"));
            }
            $event->setCancelled();
        }
    }

    public function pos(string $name, int $pos, string $arena) : void{
	    if($pos == 1 || $pos == 2){
            $this->pos[$name] = $pos;
            $this->posa[$name] = $arena;
        }
    }

    public function save(){
        $this->getLogger()->debug("saving arenas...");
	    if(!empty($this->manager->getAll())){
            foreach($this->manager->getAll() as $arena){
                $arenacfg = $this->format;
                $arenacfg["name"] = $arena->getName();
                $arenacfg["active"] = $arena->isActive();
                $arenacfg["creator"] = $arena->getCreator();
                if($arena->pos1Isset()){
                    $arenacfg["pos1x"] = $arena->getPos1()->getX();
                    $arenacfg["pos1y"] = $arena->getPos1()->getY();
                    $arenacfg["pos1z"] = $arena->getPos1()->getZ();
                }
                if($arena->pos2Isset()){
                    $arenacfg["pos2x"] = $arena->getPos2()->getX();
                    $arenacfg["pos2y"] = $arena->getPos2()->getY();
                    $arenacfg["pos2z"] = $arena->getPos2()->getZ();
                }
                $spawns = $arena->getSpawns();
                if(!empty($spawns)){
                    foreach($spawns as $spawn){
                        $arenacfg["spawns"][$spawn->getName()]["name"] = $spawn->getName();
                        $arenacfg["spawns"][$spawn->getName()]["x"] = $spawn->getPos()->getX();
                        $arenacfg["spawns"][$spawn->getName()]["y"] = $spawn->getPos()->getY();
                        $arenacfg["spawns"][$spawn->getName()]["z"] = $spawn->getPos()->getZ();
                    }
                }
                $arenacfg["type"] = $arena->getType();
                $this->save["arenas"][$arena->getName()] = $arenacfg;
                $this->getLogger()->debug("saved arena ".$arena->getName());
                $this->arenas->setAll($this->save);
            }
        }else{
            $this->getLogger()->debug("there are no arenas to save!");
        }
        $this->arenas->save();
    }

    public function load(){
        $this->getLogger()->debug("loading arenas...");
        if(!empty($this->save["arenas"])){
            foreach($this->save["arenas"] as $arena){
                $this->manager->create($arena["name"], $arena["creator"], $arena["type"]);
                $arenaobj = $this->manager->getArena($arena["name"]);
                    $arenaobj->setPos1(new Position($arena["pos1x"], $arena["pos1y"], $arena["pos1z"]));
                    $arenaobj->setPos2(new Position($arena["pos2x"], $arena["pos2y"], $arena["pos2z"]));
                if(!empty($arena["spawns"])){
                    foreach($arena["spawns"] as $spawn){
                        $arenaobj->addSpawn($spawn["name"], new Position($spawn["x"], $spawn["y"], $spawn["z"]));
                    }
                }
                if($arena["active"] == true){
                    $arenaobj->activate();
                }
                $this->getLogger()->debug("loaded arena ".$arenaobj->getName());
            }
        }else{
            $this->getLogger()->debug("there are no arenas to load!");
        }
    }

	public function onDisable() : void{
	    $this->save();
	}
}