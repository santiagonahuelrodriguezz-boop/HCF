<?php

namespace hcf\listener;

use hcf\HCFLoader;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class PlayerJoinListener implements Listener {

    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $uuidString = (string)$player->getUniqueId();
        $session = HCFLoader::getInstance()->getSessionManager()->getSession($uuidString);

        if($session === null){
            return;
        }

        if(method_exists($session, 'isMobKilled') && $session->isMobKilled()){
            try {
                $world = $player->getWorld();
                $safeSpawn = $world->getSafeSpawn();
                $player->teleport($safeSpawn);
            } catch (\Throwable $e) {
                $defaultWorld = Server::getInstance()->getWorldManager()->getDefaultWorld();
                if($defaultWorld !== null){
                    $player->teleport($defaultWorld->getSafeSpawn());
                }
            }

            if(method_exists($session, 'setMobKilled')){
                $session->setMobKilled(false);
            }

            if(method_exists($session, 'removeCooldown')){
                $session->removeCooldown('spawn.tag');
            }

            $player->sendMessage('You were killed while offline. You have been teleported to spawn.');
        }
    }
}