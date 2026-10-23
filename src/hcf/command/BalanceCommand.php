<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class BalanceCommand extends Command
{

    public function __construct()
    {
        parent::__construct('balance', 'Use command for balance');
        $this->setPermission("player.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) return;
        $sender->sendMessage('§b» §7Your Balance: §a$' . $sender->getSession()->getBalance());
    }
}