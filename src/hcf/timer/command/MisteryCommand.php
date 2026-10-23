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
class MisteryCommand extends Command
{

    /**
     * SotwCommand construct.
     */
    public function __construct()
    {
        parent::__construct('misteryall', 'Command for misteryall');
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
                    TextFormat::colorize('&eMisteryAll Commands') . "\n" .
                    TextFormat::colorize('&7/misteryall start [time] - &eUse this command to start the misteryall') . "\n" .
                    TextFormat::colorize('&7/misteryall stop - &eUse this command to stop misteryall')
                );
                break;

            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getMisteryAll()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe misteryall is already started'));
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /misteryall start [time]'));
                    return;
                }
                $time = $args[1];

                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getMisteryAll()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getMisteryAll()->setTime((int) $time);
                $sender->sendMessage(TextFormat::colorize('&aThe misteryall has started!'));
                break;

            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getMisteryAll()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe misteryall has not started'));
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getMisteryAll()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the misteryall'));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /misteryall help'));
                break;
        }
    }
}