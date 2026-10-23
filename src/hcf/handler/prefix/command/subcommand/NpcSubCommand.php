<?php

declare(strict_types=1);

namespace hcf\handler\prefix\command\subcommand;

use hcf\handler\prefix\command\PrefixSubCommand;
use hcf\handler\prefix\entity\PrefixEntity;
use hcf\handler\prefix\utils\Utils;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class NpcSubCommand implements PrefixSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        $entity = new PrefixEntity($sender->getLocation(), $sender->getSkin(), Utils::createBasicNBT($sender));
        $entity->spawnToAll();
        $sender->sendMessage(TextFormat::colorize("&aHas colocado el Prefix Entity"));
        return;
    }
}
