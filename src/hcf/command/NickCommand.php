<?php

namespace hcf\command;

use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class NickCommand extends Command
{

    public function __construct()
    {
        parent::__construct("nick", "Use this command to change or reset your nick", "", ["changenick"]);
        $this->setPermission("nick.command");
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage(TextFormat::colorize("&cYou dont have permissions to use this command: nick"));
            return;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize("&cUse this command in the game."));
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize("&cUse /nick [string: newNick|reset]"));
            return;
        }

        $nick = $args[0];
        if ($nick === $sender->getName()) {
            $sender->sendMessage(TextFormat::colorize("&cYou cannot use your own nickname"));
            return;
        }

        if ($args[0] === "reset") {
            $sender->getSession()->setName($sender->getName());
            $sender->sendMessage(TextFormat::colorize("&aYour nick has been reseted"));
            return;
        }

        foreach (HCFLoader::getInstance()->getSessionManager()->getSessions() as $session) {
            if ($session->getName() === $nick) {
                $sender->sendMessage(TextFormat::colorize("&cThis nickname is already being used by another player"));
                return;
            }
        }

        $sender->getSession()->setName($nick);
        $sender->sendMessage(TextFormat::colorize("&aYour nick has been changed to: &c$nick"));
    }
}