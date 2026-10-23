<?php

namespace hcf\faction\command\subcommand;

use hcf\faction\command\FactionSubCommand;
use hcf\faction\Faction;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\XpCollectSound;

class AnnounceSubCommand implements FactionSubCommand
{

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        $session = HCFLoader::getInstance()->getSessionManager()->getSession((string)$sender->getUniqueId());

        if ($session === null) {
            return;
        }
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($session->getFaction());

        if ($faction === null) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have faction.'));
            return;
        }

        if ($faction->getRole((string)$sender->getUniqueId()) !== Faction::LEADER) {
            $sender->sendMessage(TextFormat::colorize('&cYou don\'t have faction rank for use this command.'));
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /faction announce [message]'));
            return;
        }

        $r = implode(" ", $args);
        $faction->announce(TextFormat::colorize('&4   ANNOUNCE FACTION '));
        $faction->announce(TextFormat::colorize('&c&f'));
        $faction->announce(TextFormat::colorize(' &7» '.$r));
        $faction->announce(TextFormat::colorize('&c'));
        $sender->getWorld()->addSound($sender->getPosition(), new XpCollectSound());
    }

    public function getAlias(): ?string
    {
        return null;
    }

    public function getPermission(): ?string
    {
        return null;
    }

    public function getName(): string
    {
        return "announce";
    }

    public function getDescription(): string
    {
        return "Use the command to give a message to all members of your Faction";
    }
}