<?php

declare(strict_types=1);

namespace hcf\prefix\command\subcommand;

use hcf\prefix\command\PrefixSubCommand;
use hcf\prefix\Prefix;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DeleteSubCommand implements PrefixSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        $prefixName = $args[0];
        
        if (HCFLoader::getInstance()->getPrefixManager()->getPrefix($prefixName) === null) {
            $sender->sendMessage(TextFormat::colorize("&cThe prefix $prefixName non exists"));
            return;
        }
        
        HCFLoader::getInstance()->getPrefixManager()->removePrefix($prefixName);
        $sender->sendMessage(TextFormat::colorize('&cThe prefix has been deleted'));
    }
}
