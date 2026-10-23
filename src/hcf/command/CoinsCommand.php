<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;
use hcf\HCFLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class CoinsCommand extends Command
{

    public function __construct()
    {
        parent::__construct('coins', 'Use command for coins');
        $this->setPermission("player.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        
        $sender->sendMessage("§b» §7Your Coins: §e ". $sender->getSession()->getCrystals());
    }
}