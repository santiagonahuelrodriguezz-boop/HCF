<?php

declare(strict_types=1);

namespace hcf\koth\task;

use hcf\HCFLoader;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AutoStartCarruselTask extends Task
{
    private bool $autoStarted = false;
    private int $cooldown = 0; // en segundos
    private string $kothName = "Carrusel";

    public function onRun(): void
    {
        $kothManager = HCFLoader::getInstance()->getKothManager();
        $onlinePlayers = count(Server::getInstance()->getOnlinePlayers());

        // Reset autoStarted si KoTH ya terminó
        if ($kothManager->getKothActive() === null) {
            $this->autoStarted = false;
        }

        // Reducir cooldown si está activo
        if ($this->cooldown > 0) {
            $this->cooldown--;
        }

        // Mensaje opcional de cooldown
        if ($this->cooldown > 0 && $onlinePlayers >= 6) {
            static $lastMessageTime = 0;
            $currentTime = time();
            if ($currentTime - $lastMessageTime >= 60) { // mensaje cada 1 minuto
                $minutesLeft = ceil($this->cooldown / 60);
                Server::getInstance()->broadcastMessage("§eCarrusel se podrá auto-prender en §6$minutesLeft minutos §edespués del cooldown.");
                $lastMessageTime = $currentTime;
            }
        }

        // Iniciar KoTH automáticamente
        if (!$this->autoStarted && $kothManager->getKothActive() === null && $this->cooldown === 0 && $onlinePlayers >= 6) {
            if ($kothManager->getKoth($this->kothName) !== null) {
                Server::getInstance()->dispatchCommand(
                    Server::getInstance()->getConsoleSender(),
                    "koth start {$this->kothName}"
                );
                $this->autoStarted = true;
                $this->cooldown = 20 * 60; // 20 minutos
            }
        }
    }
}