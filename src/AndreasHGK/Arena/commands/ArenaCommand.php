<?php

declare(strict_types=1);

namespace AndreasHGK\Arena\commands;

use AndreasHGK\Arena\arena\ArenaManager;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\math\Vector3;
use pocketmine\Player;

use AndreasHGK\Arena\Arena;
use pocketmine\utils\TextFormat;

class ArenaCommand implements CommandExecutor {

    private $plugin;
    private $manager;

    public function __construct(Arena $plugin, ArenaManager $manager){
        $this->plugin = $plugin;
        $this->manager = $manager;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(strtolower($command->getName()) == "arena"){
            if(!isset($args[0])){
                $cmd = new HelpSubCommand($this->plugin, $sender, $args);
                $cmd->execute();
                return true;
            }
            switch(strtolower($args[0])){
                case "join":
                    if(!$sender->hasPermission("arena.play")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }
                    $cmd = new JoinSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "leave":
                    if(!$sender->hasPermission("arena.play")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }
                    $cmd = new LeaveSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "list":
                    if(!$sender->hasPermission("arena.play")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }
                    $cmd = new ListSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "create":
                    if(!$sender->hasPermission("arena.create")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }elseif($this->manager->playerIsInArena($sender)){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
                        return true;
                    }
                    if(in_array($sender->getLevel()->getName(), $this->plugin->cfg["disabledworlds"])){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this in this world"));
                        return true;
                    }
                    $cmd = new CreateSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "delete":
                    if(!$sender->hasPermission("arena.create")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }elseif($this->manager->playerIsInArena($sender)){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
                        return true;
                    }elseif(isset($args[1])){
                        if($this->manager->arenaExists($args[1])){
                            if($sender->getName() != $this->manager->getArena($args[1])->getCreator() && !$this->plugin->isAdminMode($sender->getName())){
                                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to edit this arena"));
                                return true;
                            }
                        }
                    }
                    $cmd = new DeleteSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "addspawn":
                    if(!$sender->hasPermission("arena.create")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }elseif($this->manager->playerIsInArena($sender)){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
                        return true;
                    }elseif(isset($args[1])){
                        if($this->manager->arenaExists($args[1])){
                            if($sender->getName() != $this->manager->getArena($args[1])->getCreator() && !$this->plugin->isAdminMode($sender->getName())){
                                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to edit this arena"));
                                return true;
                            }
                        }
                    }
                    if(in_array($sender->getLevel()->getName(), $this->plugin->cfg["disabledworlds"])){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this in this world"));
                        return true;
                    }
                    $cmd = new AddspawnSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "delspawn":
                    if(!$sender->hasPermission("arena.create")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }elseif($this->manager->playerIsInArena($sender)){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
                        return true;
                    }elseif(isset($args[1])){
                        if($this->manager->arenaExists($args[1])){
                            if($sender->getName() != $this->manager->getArena($args[1])->getCreator() && !$this->plugin->isAdminMode($sender->getName())){
                                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to edit this arena"));
                                return true;
                            }
                        }
                    }

                    $cmd = new DelspawnSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "pos1":
                    if(!$sender->hasPermission("arena.create")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }elseif($this->manager->playerIsInArena($sender)){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
                        return true;
                    }elseif(isset($args[1])){
                        if($this->manager->arenaExists($args[1])){
                            if($sender->getName() != $this->manager->getArena($args[1])->getCreator() && !$this->plugin->isAdminMode($sender->getName())){
                                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to edit this arena"));
                                return true;
                            }
                        }
                    }
                    if(in_array($sender->getLevel()->getName(), $this->plugin->cfg["disabledworlds"])){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this in this world"));
                        return true;
                    }
                    $cmd = new Pos1SubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "pos2":
                    if(!$sender->hasPermission("arena.create")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }elseif($this->manager->playerIsInArena($sender)){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
                        return true;
                    }elseif(isset($args[1])){
                        if($this->manager->arenaExists($args[1])){
                            if($sender->getName() != $this->manager->getArena($args[1])->getCreator() && !$this->plugin->isAdminMode($sender->getName())){
                                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to edit this arena"));
                                return true;
                            }
                        }
                    }
                    if(in_array($sender->getLevel()->getName(), $this->plugin->cfg["disabledworlds"])){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this in this world"));
                        return true;
                    }
                    $cmd = new Pos2SubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "activate":
                    if(!$sender->hasPermission("arena.create")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }elseif($this->manager->playerIsInArena($sender)){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
                        return true;
                    }elseif(isset($args[1])){
                        if($this->manager->arenaExists($args[1])){
                            if($sender->getName() != $this->manager->getArena($args[1])->getCreator() && !$this->plugin->isAdminMode($sender->getName())){
                                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to edit this arena"));
                                return true;
                            }
                        }
                    }
                    $cmd = new ActivateSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "deactivate":
                    if(!$sender->hasPermission("arena.create")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }elseif($this->manager->playerIsInArena($sender)){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You can't do this while in an arena"));
                        return true;
                    }elseif(isset($args[1])){
                        if($this->manager->arenaExists($args[1])){
                            if($sender->getName() != $this->manager->getArena($args[1])->getCreator() && !$this->plugin->isAdminMode($sender->getName())){
                                $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to edit this arena"));
                                return true;
                            }
                        }
                    }
                    $cmd = new DeactivateSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "save":
                    if(!$sender->hasPermission("arena.admin")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }
                    $this->plugin->save();
                    return true;
                    break;
                case "status":
                    if(!$sender->hasPermission("arena.admin")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }
                    $cmd = new StatusSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                case "start":
                    if(!$sender->hasPermission("arena.skip")){
                        $sender->sendMessage(TextFormat::colorize("&l&8[&c!&8]&r&7 You don't have permission to execute this command"));
                        return true;
                    }
                    $cmd = new StartSubCommand($this->plugin, $sender, $args, $this->manager);
                    $cmd->execute();
                    return true;
                    break;
                default:
                    $cmd = new HelpSubCommand($this->plugin, $sender, $args);
                    $cmd->execute();
                    return true;
                    break;
            }
        }
        return false;
    }
}