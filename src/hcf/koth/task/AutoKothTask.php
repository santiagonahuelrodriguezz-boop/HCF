<?php

declare(strict_types=1);

namespace hcf\koth\task;

use pocketmine\scheduler\Task;

class AutoKothTask extends Task
{
    // Constructor y propiedades quedan, pero no se usan
    public function __construct(...$args) {
        // No hace nada
    }

    public function onRun(): void
    {
        // 🔴 AutoKoth deshabilitado: nada se ejecuta
        return;
    }
}
