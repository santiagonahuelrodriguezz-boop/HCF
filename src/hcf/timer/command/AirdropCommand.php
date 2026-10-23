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
class AirdropCommand extends Command
{

    /**
     * SotwCommand construct.
     */
    public function __construct()
    {
        parent::__construct('airdropevent', 'Command for airdrop');
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
                    TextFormat::colorize('&7/airdropevent start [time] - &eUse this command to start the airdropevent') . "\n" .
                    TextFormat::colorize('&7/airdropevent stop - &eUse this command to stop airdropevent')
                );
                break;

            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getAirdropAll()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe airdrop is already started'));
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /airdropevent start [time]'));
                    return;
                }
                $time = $args[1];

                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getAirdropAll()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getAirdropAll()->setTime((int) $time);
                $sender->sendMessage(TextFormat::colorize('&aThe airdropevent has started!'));
                break;

            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getAirdropAll()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe airdropevent has not started'));
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getAirdropAll()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the airdropevent'));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /airdropevent help'));
                break;
        }
    }
}