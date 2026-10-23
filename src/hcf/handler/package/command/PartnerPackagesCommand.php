<?php

namespace hcf\handler\package\command;

use hcf\HCFLoader;
use hcf\handler\package\PackageListener;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat as TE;
use pocketmine\player\Player;

class PartnerPackagesCommand extends Command
{

    /**
     * ParterPackagesCommand constructor.
     */
    public function __construct()
    {
        parent::__construct('pkg', 'packges commands');
        $this->setPermission('pkg.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (count($args) === 0) {
            $sender->sendMessage(
                TE::GRAY . ("\n") .
                TE::colorize("\n") .
                TE::BLUE . "/pkg - " . TE::WHITE . ("use this command to get plugin information\n") .
                TE::BLUE . "/pkg give [all/player] [amount] - " . TE::WHITE . ("uste this command to give pkg\n") .
                TE::colorize("\n") .
                TE::GRAY . ("\n")
            );
            return;
        }
        
        switch ($args[0]) {
            case "give":
                if (!$sender->hasPermission("pkg.command")) {
                    $sender->sendMessage(TE::RED . "You don't have permissions");
                    return;
                }
                if (empty($args[1])) {
                    $sender->sendMessage(TE::RED . "/pkg give [all/player] [amount]");
                    return;
                }
                if (empty($args[2])) {
                    $sender->sendMessage(TE::RED . "/pkg give [all/player] [amount]");
                    return;
                }
                $player = HCFLoader::getInstance()->getServer()->getPlayerExact($args[1]);
                if ($player !== null) {
                    PackageListener::givePartner($player, $args[2]);
                    return;
                }
                foreach (HCFLoader::getInstance()->getServer()->getOnlinePlayers() as $player) {
                    PackageListener::givePartner($player, $args[2]);
                }
                break;
        }
    }
}