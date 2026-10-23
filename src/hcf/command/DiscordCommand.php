<?php

declare(strict_types=1);

namespace hcf\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DiscordCommand extends Command
{
    
    /**
     * ListCommand construct.
     */
    public function __construct()
    {
        parent::__construct('discord', 'Use the command for join to discord');
        $this->setPermission("player.use");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $sender->sendMessage(TextFormat::colorize('-------'));
        $sender->sendMessage(TextFormat::colorize('&7Discord Of Demon'));
        $sender->sendMessage(TextFormat::colorize('§9discord.demonhcf.com'));
        $sender->sendMessage(TextFormat::colorize('--------'));
    }
}