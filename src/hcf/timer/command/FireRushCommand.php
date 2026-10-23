<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\entity\effect\VanillaEffects;

class FireRushCommand extends Command implements Listener
{
    private PluginBase $plugin;

    public function __construct(PluginBase $plugin)
    {
        parent::__construct('firerush', '§bCommand for FireRush Event');
        $this->setPermission('firerush.command');
        $this->plugin = $plugin;

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) return;

        $timer = HCFLoader::getInstance()->getTimerManager()->getFireRush();

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /firerush help'));
            return;
        }

        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&eFireRush Commands') . "\n" .
                    TextFormat::colorize('&7/firerush start [time] - &eStart FireRush Event') . "\n" .
                    TextFormat::colorize('&7/firerush stop - &eStop FireRush Event')
                );
                break;

            case 'start':
                if ($timer->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cFireRush is already active'));
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /firerush start [time]'));
                    return;
                }

                $time = Timer::time($args[1]);
                $timer->setActive(true);
                $timer->setTime((int)$time);

                $broadcastMessage = [
                    TextFormat::colorize("&c§l████████"),
                    TextFormat::colorize("&c§l██&6█&6█&6█&c██"),
                    TextFormat::colorize("&c§l█&6████&6█&c█ &r&c[§l§6FireRush Event§r&c]"),
                    TextFormat::colorize("&c§l█&6████&6█&c█ &r&eThe Event Has Started!"),
                    TextFormat::colorize("&c§l█&6████&6█&c█ No Perderas El Inventario Y tendrán Fuerza 2!"),
                    TextFormat::colorize("&c§l██&6█&6█&6█&c██"),
                    TextFormat::colorize("&c§l████████")
                ];

                foreach ($broadcastMessage as $message) {
                    $sender->getServer()->broadcastMessage($message);
                }

                $sender->sendMessage(TextFormat::colorize('§aFireRush has started!'));
                break;

            case 'stop':
                if (!$timer->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cFireRush is not active'));
                    return;
                }

                $timer->setActive(false);

                foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
                $player->getEffects()->remove(VanillaEffects::STRENGTH());
                }

                $sender->sendMessage(TextFormat::colorize('&cYou have stopped FireRush'));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /firerush help'));
                break;
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void
    {
        $timer = HCFLoader::getInstance()->getTimerManager()->getFireRush();
        if ($timer->isActive()) {
            $event->setKeepInventory(true);
        }
    }
}