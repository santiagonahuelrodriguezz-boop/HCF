<?php

namespace hcf\rollback;

use hcf\HCFLoader;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;

class RollbackListener implements Listener
{

    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        if (HCFLoader::getInstance()->getRollbackManager()->getRollback($player->getName()) === null) {
            HCFLoader::getInstance()->getRollbackManager()->addRollback($player->getName(), $player->getInventory()->getContents(), $player->getArmorInventory()->getContents());
        } else {
            HCFLoader::getInstance()->getRollbackManager()->removeRollback($player->getName());
            HCFLoader::getInstance()->getRollbackManager()->addRollback($player->getName(), $player->getInventory()->getContents(), $player->getArmorInventory()->getContents());
        }
    }

}