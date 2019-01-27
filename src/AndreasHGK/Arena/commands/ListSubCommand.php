<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ListSubCommand extends SubCommand {

    protected $manager;
    private $pagesize = 2;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
    }

    public function execute() : void{
        if(!isset($this->args[1])){
            $page = 1;
        }elseif($this->args[1] < 1){
            $page = 1;
        }else{
            $page = $this->args[1];
        }
        $str = "&l&8[&c!&8]&r&7 arenas (page ".$page."):";
        $start = $this->pagesize*($page)-$this->pagesize;
        $int = 0;
        $empty = true;
        foreach($this->manager->getAll() as $arena){
            $int++;
            if($int >= $start && $int <= $start+$this->pagesize){
                $str = $str."\n&8&l> &r&c".$arena->getName()."&7 by &c".$arena->getCreator();
                $empty = false;
            }
        }
        if($empty == true){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 There are no arenas to show on this page"));
            return;
        }
        $this->sender->sendMessage(TextFormat::colorize($str));
    }

}