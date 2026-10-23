<?php

namespace hcf\command;

use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;

class PingCommand extends Command
{
    public function __construct()
    {
        parent::__construct('ping', 'Use this command to see your ping');
        $this->setPermission("player.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (isset($args[0])) {
            $target = $sender->getServer()->getPlayerExact($args[0]);
            if ($target instanceof Player) {
                $ping = $target->getNetworkSession()->getPing();
                $sender->sendMessage(TextFormat::colorize("§a» §7The ping of " . $target->getName() . " is: §g" . $ping . " ms"));
            } else {
                $sender->sendMessage(TextFormat::colorize("§cPlayer not found"));
            }
        } else {
            if ($sender instanceof Player) {
                $ping = $sender->getNetworkSession()->getPing();
                $sender->sendMessage(TextFormat::colorize("§a» §7Your ping is: §g" . $ping . " ms"));
            }
        }
    }
}
