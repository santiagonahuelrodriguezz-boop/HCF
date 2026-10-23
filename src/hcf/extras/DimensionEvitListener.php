<?php

namespace hcf\extras;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\utils\TextFormat;
use hcf\player\Player;
use hcf\HCFLoader;

class DimensionEvitListener implements Listener {

    private HCFLoader $plugin;

    public function __construct(HCFLoader $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerMove(PlayerMoveEvent $event) {
        $player = $event->getPlayer();
        $to = $event->getTo();

        if ($to->getWorld()->getDisplayName() === "end" || $to->getWorld()->getDisplayName() === "nether") {
            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                $event->cancel();

                $player->teleport($player->getPosition());
                $player->sendTip(TextFormat::colorize("&cQuitate el PvPTimer para que puedas moverte"));

                $player->sendMessage(TextFormat::colorize("&gNo puedes entrar a &d'End' &go &c'Nether' &gmientras tienes el PvPTimer."));
            } /*else {
                $player->sendMessage(TextFormat::colorize("&aHas dejado el timer, ahora puedes moverte a &d'End' &aó &c'Nether'."));
            }*/
        }
    }
}