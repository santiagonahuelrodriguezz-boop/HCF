<?php

namespace hcf\extras;

use hcf\utils\timer\Timer;
use hcf\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
//use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\world\World;
use pocketmine\math\Position;
use hcf\HCFLoader; 
use pocketmine\scheduler\Task;

class EndHeightListener implements Listener {

    private HCFLoader $plugin;

    public function __construct(HCFLoader $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $world = $player->getWorld();

        if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
            return;
        }

        if ($world->getFolderName() === "end") {
            $y = $player->getPosition()->getY();

            if ($y < -10) {
                $this->plugin->getScheduler()->scheduleDelayedTask(new class($player) extends Task {
                    private Player $player;

                    public function __construct(Player $player) {
                        $this->player = $player;
                    }

                    public function onRun(): void {
                        if ($this->player->getSession()->getCooldown('starting.timer') === null && $this->player->getSession()->getCooldown('pvp.timer') === null) {
                            $this->player->setHealth(0);
                        }
                    }
                }, 20);
            }
        }
    }
}