<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;

class SotwCommand extends Command
{

    public function __construct()
    {
        parent::__construct('sotw', 'Command for SOTW');
        $this->setPermission('sotw.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /sotw help'));
            return;
        }

        switch (strtolower($args[0])) {

            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&eSOTW Commands') . "\n" .
                    TextFormat::colorize('&7/sotw start [time] - &eStart SOTW') . "\n" .
                    TextFormat::colorize('&7/sotw stop - &eStop SOTW')
                );
                break;

            case 'start':

                $sotw = HCFLoader::getInstance()->getTimerManager()->getSotw();

                if ($sotw->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe SOTW is already active.'));
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /sotw start [time]'));
                    return;
                }

                $time = Timer::time($args[1]);

                $sotw->setActive(true);
                $sotw->setTime((int)$time);

                $sender->sendMessage(TextFormat::colorize('&aThe SOTW has started!'));

                $server = HCFLoader::getInstance()->getServer();
                $commandMap = $server->getCommandMap();

                $consoleSender = new ConsoleCommandSender($server, $server->getLanguage());

                $commandsToRun = [
                    "keyall start 25m",
                    "keyallop start 30m",
                    "freekits start 2h",
                    "airdropevent start 1h"
                ];

                foreach ($commandsToRun as $cmd) {
                    $commandMap->dispatch($consoleSender, $cmd);
                }

                break;

            case 'stop':

                $sotw = HCFLoader::getInstance()->getTimerManager()->getSotw();

                if (!$sotw->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe SOTW is not active.'));
                    return;
                }

                $sotw->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the SOTW.'));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /sotw help'));
                break;
        }
    }
}