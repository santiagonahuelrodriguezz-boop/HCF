<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class SotwCommand
 * @package hcf\timer\command
 */
class LooboxCommand extends Command
{

    /**
     * SotwCommand construct.
     */
    public function __construct()
    {
        parent::__construct('lootboxall', 'Command for lootboxall');
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
                    TextFormat::colorize('&eLootboxAll Commands') . "\n" .
                    TextFormat::colorize('&7/lootboxall start [time] - &eUse this command to start the lootboxall') . "\n" .
                    TextFormat::colorize('&7/lootboxall stop - &eUse this command to stop lootboxall')
                );
                break;

            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getLoobox()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe lootboxall is already started'));
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /lootboxall start [time]'));
                    return;
                }
                $time = $args[1];

                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getLoobox()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getLoobox()->setTime((int) $time);
                $sender->sendMessage(TextFormat::colorize('&aThe lootboxall has started!'));
                break;

            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getLoobox()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe lootboxall has not started'));
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getLoobox()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the lootboxall'));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /lootboxall help'));
                break;
        }
    }
}