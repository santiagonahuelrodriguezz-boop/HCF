<?php

declare(strict_types=1);

namespace hcf\handler\prefix\command\subcommand;

use hcf\handler\prefix\command\PrefixSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetSubCommand implements PrefixSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize('&cUse /prefix set [string: player] [string: prefix]'));
            return;
        }
        $playerName = $args[0];
        $targetPlayer = HCFLoader::getInstance()->getServer()->getPlayerExact($playerName);
        $prefixName = $args[1];
        
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage(TextFormat::RED."Jugador $targetPlayer no encontrado!");
            return;  
        }
        
        if (HCFLoader::getInstance()->getPrefixManager()->getPrefix($prefixName) === null) {
            $sender->sendMessage(TextFormat::colorize("&cPrefix $prefixName no existe"));
            return;
        }


        if (HCFLoader::getInstance()->getPrefixManager()->getPrefix($prefixName) !== null) {
            $targetPlayer->getSession()->setPrefix($prefixName);
            $sender->sendMessage(TextFormat::colorize("&aLe has dado el Prefix $prefixName a $playerName"));
        }
    }
}