<?php

declare(strict_types=1);

namespace hcf\handler\prefix\command\subcommand;

use hcf\handler\prefix\command\PrefixSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RemoveSubCommand implements PrefixSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /prefix remove [string: player]'));
            return;
        }
        $playerName = $args[0];
        $targetPlayer = HCFLoader::getInstance()->getServer()->getPlayerByPrefix($playerName);
        
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage(TextFormat::RED."Jugador $targetName no encontrado!");
            return;  
        }
        
        $targetPlayer->getSession()->setPrefix(null);
        $sender->sendMessage(TextFormat::colorize("&aLe has quitar el Prefix a $playerName"));
    }
}