<?php

declare(strict_types=1);

namespace hcf\handler\vkit\command\subcommand;

use hcf\handler\vkit\command\vKitSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class DeleteSubCommand
 * @package hcf\handler\vkit\command\subcommand
 */
class DeleteSubCommand implements vKitSubCommand
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
            $sender->sendMessage(TextFormat::colorize('&c/vkit delete [string: vKitName]'));
            return;
        }
        $vKitName = $args[0];
        
        if (HCFLoader::getInstance()->getHandlerManager()->getvKitManager()->getvKit($vKitName) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis vkit does not exist'));
            return;
        }
        HCFLoader::getInstance()->getHandlerManager()->getvKitManager()->removevKit($vKitName);
        $sender->sendMessage(TextFormat::colorize('&cYou have successfully removed vkit ' . $vKitName));
    }
}