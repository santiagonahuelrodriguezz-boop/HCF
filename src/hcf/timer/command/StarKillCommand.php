<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

class StarKillCommand extends Command implements Listener {

    private PluginBase $plugin;

    public function __construct(PluginBase $plugin) {
        parent::__construct('starkill', '§bCommand for StarKill Event');
        $this->setPermission('starkill.command');
        $this->plugin = $plugin;

        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$this->testPermission($sender)) return;

        $timer = HCFLoader::getInstance()->getTimerManager()->getStarKill();

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /starkill help'));
            return;
        }

        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&eStarKill Commands') . "\n" .
                    TextFormat::colorize('&7/starkill start [time] - &eStart StarKill Event') . "\n" .
                    TextFormat::colorize('&7/starkill stop - &eStop StarKill Event')
                );
                break;

            case 'start':
                if ($timer->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cStarKill is already active'));
                    return;
                }

                $time = isset($args[1]) ? Timer::time($args[1]) : 3600;
                $timer->setActive(true);
                $timer->setTime((int)$time);

                $broadcastMessage = [
                    TextFormat::colorize("&6§l████████"),
                    TextFormat::colorize("&6§l██§e█§e█§e█§6██"),
                    TextFormat::colorize("&6§l█§e████§e█§6█ &r&6[§l§eStarKill Event§r&6]"),
                    TextFormat::colorize("&6§l█§e████§e█§6█ &r&aThe Event Has Started!"),
                    TextFormat::colorize("&6§l█§e████§e█§6█ §fNo perderás inventario ni DTR y tendrás Fuerza II."),
                    TextFormat::colorize("&6§l██§e█§e█§e█§6██"),
                    TextFormat::colorize("&6§l████████")
                ];

                foreach ($broadcastMessage as $message) {
                    $sender->getServer()->broadcastMessage($message);
                }

                $sender->sendMessage(TextFormat::colorize('§aStarKill has started!'));
                break;

            case 'stop':
                if (!$timer->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cStarKill is not active'));
                    return;
                }

                $timer->setActive(false);

                foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
                    $player->getEffects()->remove(VanillaEffects::STRENGTH());
                }

                $sender->sendMessage(TextFormat::colorize('&cYou have stopped StarKill'));
                break;

            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /starkill help'));
                break;
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event): void {
        $timer = HCFLoader::getInstance()->getTimerManager()->getStarKill();
        if (!$timer->isActive()) return;

        $player = $event->getPlayer();

        // Mantener inventario
        $event->setKeepInventory(true);

        // Proteger DTR correctamente
        $factionManager = HCFLoader::getInstance()->getFactionManager();
        $faction = $factionManager->getFaction($player->getName());
        if ($faction !== null && $timer->isProtectingDTR()) {
            $faction->setDtr($faction->getDtr()); // evita que baje DTR
        }

        // Obtener killer correctamente en PM5
        $killer = null;
        $lastDamage = $player->getLastDamageCause();
        if ($lastDamage instanceof EntityDamageByEntityEvent) {
            $damager = $lastDamage->getDamager();
            if ($damager instanceof Player) {
                $killer = $damager;
            }
        }

        if ($killer instanceof Player) {
            $killer->getServer()->dispatchCommand($killer, "key give StarKill 1 " . $killer->getName());
            $killer->sendMessage("§6[StarKill] §aHas recibido una llave por tu kill!");
            // Broadcast global de kills eliminado
        }

        // Aplicar Fuerza II al jugador muerto
        $this->applyStrength($player);
    }

    private function applyStrength(Player $player): void {
        $effect = new EffectInstance(VanillaEffects::STRENGTH(), 20 * 60 * 60, 1, false);
        $player->getEffects()->add($effect);
    }
}