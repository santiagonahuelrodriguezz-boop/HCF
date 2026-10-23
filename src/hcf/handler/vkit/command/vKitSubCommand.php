<?php

declare(strict_types=1);

namespace hcf\handler\vkit\command;

use pocketmine\command\CommandSender;

/**
 * Interface vKitSubCommand
 * @package hcf\handler\kit\command
 */
interface vKitSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void;
}