<?php

declare(strict_types=1);

namespace hcf\timer\types;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\module\task\BossBarTask;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class TimerKillAndKing implements Listener {

    private ?Player $target = null;
    private int $time = 0;
    private bool $active = false;

    public function isActive(): bool {
        return $this->active;
    }

    public function getTarget(): ?Player {
        return $this->target;
    }

    public function getTime(): int {
        return $this->time;
    }

    public function start(Player $target, int $timeInSeconds): void {
        $this->target = $target;
        $this->time = $timeInSeconds;
        $this->active = true;

        $targetName = $target->getName();
        Server::getInstance()->broadcastMessage(TextFormat::colorize(
            "§6[Kill&King] §fEl jugador §e{$targetName} §ftiene §c{$timeInSeconds} segundos §fpara sobrevivir!"
        ));

        // BossBar
        $bossBarTask = new BossBarTask(HCFLoader::getInstance());
        HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask($bossBarTask, 20);

        // Cuenta regresiva
        HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            if ($this->target === null || !$this->target->isOnline()) {
                $this->stop();
                return;
            }

            $this->time--;

            if ($this->time <= 0) {
                $this->giveKeysSurvival();
                $this->stop();
            }
        }), 20);
    }

    public function stop(): void {
        $this->active = false;
        $this->target = null;
        $this->time = 0;
    }

    // Keys si sobrevive al tiempo
    public function giveKeysSurvival(): void {
        if ($this->target === null) return;

        $playerName = $this->target->getName();
        $keys = 3;

        $console = new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage());

        Server::getInstance()->getCommandMap()->dispatch(
            $console,
            "key give 2025 {$keys} {$playerName}"
        );

        $this->target->sendMessage(TextFormat::colorize(
            "§a¡Has sobrevivido al Kill&King! §fRecibiste §e{$keys} Keys§f."
        ));
    }

    // Keys si alguien mata al King
    public function giveKeysOnKill(Player $killer): void {
        $playerName = $killer->getName();
        $keys = 5;

        $console = new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage());

        Server::getInstance()->getCommandMap()->dispatch(
            $console,
            "key give 2025 {$keys} {$playerName}"
        );

        $killer->sendMessage(TextFormat::colorize(
            "§a¡Has matado al King! §fRecibiste §e{$keys} Keys§f."
        ));
    }

    // Listener integrado para muerte del King
    public function onKingDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();

        if ($this->target === $player && $this->active) {
            $cause = $player->getLastDamageCause();
            if ($cause instanceof EntityDamageByEntityEvent) {
                $damager = $cause->getDamager();
                if ($damager instanceof Player) {
                    $this->giveKeysOnKill($damager);
                }
            }

            $this->stop();
            $event->setDeathMessage(null); // Opcional: evita mensaje por defecto
        }
    }
}