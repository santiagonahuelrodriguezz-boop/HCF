<?php

declare(strict_types=1);

namespace hcf\faction\command\subcommand;

use hcf\faction\command\FactionSubCommand;
use hcf\player\Player;
use hcf\utils\upgrades\UpgradeMenu;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class UpgradesSubCommand implements FactionSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have a faction'));
            return;
        }

        UpgradeMenu::create($sender);
    }
}