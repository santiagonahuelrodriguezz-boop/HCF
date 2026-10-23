<?php

declare(strict_types=1);

namespace hcf\abilities\task;

use hcf\session\extras\SessionFreeze;
use hcf\HCFLoader;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class ImmobilizerTask extends Task {

    private int $time = 5;
    private Player $player;

    public function __construct(Player $player) {
        $this->player = $player;
        HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask($this, 20);
    }

    public function onRun(): void {
        // ✅ Primero, verificar si el jugador sigue conectado
        if (!$this->player->isOnline()) {
            $this->cancelAndUnfreeze();
            return;
        }

        // ✅ Enviar subtítulo solo si sigue en línea
        if ($this->time > 0) {
            $this->player->sendSubTitle("§ffor§c {$this->time} §fseconds");
            $this->time--;
        } else {
            $this->cancelAndUnfreeze();
        }
    }

    private function cancelAndUnfreeze(): void {
        SessionFreeze::removeFreeze($this->player);
        $this->getHandler()?->cancel();
    }
}