<?php

declare(strict_types=1);

namespace hcf\listeners;

use hcf\handler\kit\classes\presets\Yeti;
use hcf\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\VanillaBlocks;;

class YetiListener implements Listener {

    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if(!$player instanceof Player) return;

        $class = $player->getClass();
        if($class === null) return;

        if($class instanceof Yeti){
           
            $iceItem = VanillaBlocks::PACKED_ICE();

            if($item->equals($iceItem, true, true)){
                if($event->getAction() === PlayerInteractEvent::LEFT_CLICK_AIR || $event->getAction() === PlayerInteractEvent::LEFT_CLICK_BLOCK){
                    $class->useAbility($player);
                    $event->cancel(); 
                }
            }
        }
    }
}