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
        #todo: LIST PAGES
        if(isset($this->args[1])){
            $page = $this->args[1];
        }elseif($this->args[1] < 1){
            $page = 1;
        }else{
            $page = 1;
        }
        $str = "&l&8[&c!&8]&r&7 arenas (page ".$page."): &c";
        $start = $this->pagesize*($page);
        $int = 0;
        foreach($this->manager->getAll() as $arena){
            $int++;
            if($int >= $start && $int <= $start+$this->pagesize){
                $str = $str.$arena->getName()."&r&7, &c";
            }
        }
        $this->sender->sendMessage(TextFormat::colorize($str));
    }

}