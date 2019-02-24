<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class OwnedSubCommand extends SubCommand {

    protected $manager;
    private $pagesize = 10;

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
        $pages = floor(count($this->manager->getAllOwned($this->sender->getName()))/10)+1;
        $str1 = "&l&8[&c!&8]&r&7 List of owned arenas (page ".$page." of ".$pages."):";
        $start = $this->pagesize*($page)-$this->pagesize;
        $int = 0;
        $empty = true;
        foreach($this->manager->getAllOwned($this->sender->getName()) as $arena){
            if($int >= $start && $int < $start+$this->pagesize){
                if($int == $start){
                    $str = "&8&l> ";
                }else{
                    $str = $str."\n&8&l> ";
                }
                $str = $str."&r&8[&e⭐⭐⭐⭐&0⭐&8] ";
                if($arena->getStatus() == 1){
                    $str = $str."&r&l&6[+] ";
                }elseif($arena->getStatus() == 2){
                    $str = $str."&r&l&9[O] ";
                }
                $str = $str."&r&c".$arena->getName()."&7 by &c".$arena->getCreator()."&8 (".$arena->getType().")";
                $empty = false;
            }
            $int++;
        }
        if($empty == true){
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 There are no arenas to show on this page"));
            return;
        }
        $this->sender->sendMessage(TextFormat::colorize($str1));
        $this->sender->sendMessage(TextFormat::colorize($str));
        return;
    }

}