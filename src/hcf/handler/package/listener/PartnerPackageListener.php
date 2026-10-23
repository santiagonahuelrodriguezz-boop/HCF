<?php

namespace hcf\handler\package\listener;

use hcf\handler\package\util\Content;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;

class PartnerPackageListener implements Listener {

    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getNamedTag()->getTag("ppackage") !== null) {
            $event->cancel(); 

            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);

            $reward = Content::getInstance()->getRandomItem(); 
            if ($player->getInventory()->canAddItem($reward)) {
                $player->getInventory()->addItem($reward);
            } else {
                $player->dropItem($reward);
            }

            $player->sendMessage(TextFormat::colorize("&aHas abierto un Partner Package"));
        }
    }
}