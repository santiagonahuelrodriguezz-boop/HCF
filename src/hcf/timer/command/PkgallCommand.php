<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Language;

class PkgallCommand extends Command
{
    public function __construct()
    {
        parent::__construct('pkgall', 'Command for pkgall');
        $this->setPermission('sotw.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) return;

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize("&cUse /$commandLabel help"));
            return;
        }

        $timerManager = HCFLoader::getInstance()->getTimerManager()->getPackages();
        $server = HCFLoader::getInstance()->getServer();

        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&ePackagesAll Commands') . "\n" .
                    TextFormat::colorize('&7/pkgall start [time] - &eUse this command to start the pkgall') . "\n" .
                    TextFormat::colorize('&7/pkgall stop - &eUse this command to stop pkgall')
                );
                break;

            case 'start':
                if ($timerManager->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe pkgall is already started'));
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /pkgall start [time]'));
                    return;
                }

                $time = Timer::time($args[1]);
                $timerManager->setActive(true);
                $timerManager->setTime((int)$time);
                $sender->sendMessage(TextFormat::colorize('&aThe pkgall has started!'));

                HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($server, $timerManager) {
                    $consoleSender = new ConsoleCommandSender($server, new Language("eng", "US"));
                    $server->dispatchCommand($consoleSender, "pkg giveall 20");
                    $timerManager->setActive(false);
                }), $time * 20);
                break;

            case 'stop':
                if (!$timerManager->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe pkgall has not started'));
                    return;
                }

                $timerManager->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the pkgall'));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /pkgall help'));
                break;
        }
    }
}