<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\Arena;
use AndreasHGK\Arena\arena\ArenaManager;
use AndreasHGK\Arena\module\ClaimsModule;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CHereCommand extends SubCommand {

    private $manager;
    private $module;

    public function __construct(Arena $arena, CommandSender $sender, array $args, ArenaManager $manager, ClaimsModule $module){
        parent::__construct($arena, $sender, $args);
        $this->manager = $manager;
        $this->module = $module;
    }

    public function execute() : void{
        $cm = $this->module->claimManager;
        if($cm->isClaimed($this->sender->getPosition(), $this->sender->getLevel()->getName())){
            $claim = $cm->getClaim($this->sender->getPosition(), $this->sender->getLevel()->getName());
            $owner = $claim->getOwner();
            $edit = $cm->canEdit($this->sender->getPosition(), $this->sender->getLevel()->getName(), $this->sender);
            if($edit){
                $canedit = "yes";
            }else{
                $canedit = "no";
            }
            $claim->displayCorners();
            $dim = $claim->getDimensions();
            $size = $claim->getSize();
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 This area is claimed\n&l&8>&r&7 Owner: &c".$owner."\n&l&8>&r&7 Can edit: &c".$canedit."\n&l&8>&r&7 Width: &c".$dim[0]."\n&l&8>&r&7 Length: &c".$dim[1]."\n&l&8>&r&7 Size: &c".$size));
        }else{
            $this->sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 This area is not yet claimed"));
        }
    }

}