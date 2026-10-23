<?php

namespace hcf\extras;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use itoozh\crates\Main;

class KillKeyListener implements Listener {

    public function onPlayerKill(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $cause = $player->getLastDamageCause();

        if (!$cause instanceof EntityDamageByEntityEvent) {
            return;
        }

        $killer = $cause->getDamager();
        if (!$killer instanceof Player) {
            return;
        }

        $world = $player->getWorld();
        $key = null;

        if ($world->getFolderName() === "nether") {
            $key = Main::getCrate("Nether")->getKeyItem(1);
        } elseif ($world->getFolderName() === "end") {
            $key = Main::getCrate("End")->getKeyItem(1);
        }

        if ($key !== null) {
            if ($killer->getInventory()->canAddItem($key)) {
                $killer->getInventory()->addItem($key);
                $killer->sendMessage(TextFormat::colorize("§a¡Has recibido una llave de esta dimensión , as mas kills xD!"));
            } else {
                $killer->getWorld()->dropItem($killer->getPosition(), $key);
                $killer->sendMessage(TextFormat::colorize("§eTu inventario está lleno, la llave ha sido tirada al suelo."));
            }
        }
    }
}