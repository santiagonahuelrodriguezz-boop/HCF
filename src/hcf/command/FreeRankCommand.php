<?php

namespace hcf\command;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class FreeRankCommand extends Command 

{

    public function __construct(){
        parent::__construct('freerank', '§aCommand for freerank');
        $this->setPermission("player.use");

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)

    {
        if (!$sender instanceof Player) {
            return;
        }
        
        if ($sender->getSession() == null) {
            return;
        }
        
        if (!$sender->getSession()->getCooldown("freerank.cooldown") == null) {
            $sender->sendMessage(TextFormat::colorize("&cYou already used this command"));
            return;
        }

        $senderName = $sender->getName();

        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), 'ranks setrank "'.$senderName.'" Belcebu 2d');
        $sender->getSession()->addCooldown('freerank.cooldown', '', 604800, false, false);
        $sender->sendMessage(TextFormat::colorize("&7You have the §BBelcebu §7rank for §c48h hours"));

    }

}