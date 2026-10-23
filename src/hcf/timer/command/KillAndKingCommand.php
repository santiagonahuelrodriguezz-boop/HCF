<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class KillAndKingCommand extends Command {

    public function __construct() {
        parent::__construct("killandking", "§6Evento Kill&King");
        $this->setPermission("event.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$this->testPermission($sender)) return;

        $timer = HCFLoader::getInstance()->getTimerManager()->getKillAndKing();

        if (count($args) < 1) {
            $sender->sendMessage("§cUsa: /killandking <start|stop>");
            return;
        }

        switch (strtolower($args[0])) {
            case "start":
                if ($timer->isActive()) {
                    $sender->sendMessage("§cYa hay un evento activo.");
                    return;
                }

                if (count($args) < 3) {
                    $sender->sendMessage("§cUso: /killandking start <jugador> <tiempoSegundos>");
                    return;
                }

                $target = $sender->getServer()->getPlayerExact($args[1]);
                if (!$target instanceof Player) {
                    $sender->sendMessage("§cEse jugador no está online.");
                    return;
                }

                $time = (int) $args[2];
                $timer->start($target, $time);
                $sender->sendMessage("§aEvento Kill&King iniciado con §e{$target->getName()} §adurando §c{$time} segundos.");
                break;

            case "stop":
                if (!$timer->isActive()) {
                    $sender->sendMessage("§cNo hay evento activo.");
                    return;
                }
                $timer->stop();
                $sender->sendMessage("§cEvento Kill&King detenido.");
                break;

            default:
                $sender->sendMessage("§cUsa: /killandking <start|stop>");
                break;
        }
    }
}