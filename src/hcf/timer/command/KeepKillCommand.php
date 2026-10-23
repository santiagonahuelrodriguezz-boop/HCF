<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class KeepKillCommand
 * @package hcf\timer\command
 */
class KeepKillCommand extends Command
{

    /**
     * KeepKillCommand construct.
     */
    public function __construct()
    {
        parent::__construct('keepkill', 'Command for keepkill event');
        $this->setPermission('sotw.command');
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender))
            return;

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize("&cUse /$commandLabel help"));
            return;
        }

        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&eAirdrop Commands') . "\n" .
                    TextFormat::colorize('&7/keepkill start [time] - &eUse this command to start the keepkill') . "\n" .
                    TextFormat::colorize('&7/keepkill stop - &eUse this command to stop keepkill')
                );
                break;

            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getKeepKill()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe airdrop is already started'));
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /keepkill start [time]'));
                    return;
                }
                $time = $args[1];

                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getKeepKill()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getKeepKill()->setTime((int) $time);
                $sender->sendMessage(TextFormat::colorize('&aThe keepkill has started!'));
                break;

            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getKeepKill()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe keepkill has not started'));
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getKeepKill()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the keepkill'));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /keepkill help'));
                break;
        }
    }
}