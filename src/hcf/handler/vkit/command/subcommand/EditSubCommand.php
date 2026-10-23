<?php

declare(strict_types=1);

namespace hcf\handler\vkit\command\subcommand;

use hcf\handler\vkit\command\vKitSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\inventorie\Inventories;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class EditSubCommand
 * @package hcf\handler\vkit\command\subcommand
 */
class EditSubCommand implements vKitSubCommand
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
            Inventories::editvKitOrganization($sender);
            return;
        }
        
        $vKitName = $args[0];
        $vkit = HCFLoader::getInstance()->getHandlerManager()->getvKitManager()->getvKit($vKitName);
        
        if ($vkit === null) {
            $sender->sendMessage(TextFormat::colorize('&cThis vkit does not exist'));
            return;
        }
        $vkit->setItems($sender->getInventory()->getContents());
        $sender->sendMessage(TextFormat::colorize('&a You have successfully edited the ' . $vkit->getName() . ' vkit'));
    }
}