<?php

declare(strict_types=1);

namespace hcf\module\task;

use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use hcf\HCFLoader;

class ActionBarTask extends Task {

    private int $interval;

    public function __construct(int $interval = 1) {
        $this->interval = $interval;
    }

    public function onRun(): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            $session = HCFLoader::getInstance()->getSessionManager()->getSession($player->getXuid());

            if ($session !== null) {
                $cooldowns = [];
                
                if (($combatTag = $session->getCooldown('spawn.tag')) !== null) {
                    $cooldowns[] = "§cCombat: §f" . $combatTag->getRemainingTime()."s";
                }
                if (($deathbanCooldown = $session->getCooldown('deathban.cooldown')) !== null) {
                    $cooldowns[] = "§4Deathban: §f" . $deathbanCooldown->getRemainingTime()."s";
                }
                if (($pvpTimer = $session->getCooldown('starting.timer')) !== null) {
                    $cooldowns[] = "§aPvPTimer: §f" . $pvpTimer->getRemainingTime()."s";
                }
                if (($enderpearl = $session->getCooldown('enderpearl')) !== null) {
                    $cooldowns[] = "§sEnderpearl: §f" . $enderpearl->getRemainingTime()."s";
                }
                if (($apple = $session->getCooldown('apple')) !== null) {
                    $cooldowns[] = "§gApple: §f" . $apple->getRemainingTime()."s";
                }
                if (($ability = $session->getCooldown('ability.global')) !== null) {
                    $cooldowns[] = "§aAbility: §f" . $ability->getRemainingTime()."s";
                }

                if (count($cooldowns) > 0) {
                    $message = implode(" §0-- ", $cooldowns);

                    $player->sendTip($message);
                }
            }
        }
    }
}