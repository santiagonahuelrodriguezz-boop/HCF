<?php

declare(strict_types=1);

namespace hcf\handler\vkit;

use hcf\HCFLoader;
use hcf\player\Player;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

/**
 * Class vKitListener
 * @package hcf\handler\vkit
 */
class vKitListener implements Listener
{
 
    /**
     * @param PlayerInteractEvent $event
     */   
    public function handleInteract(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        /** @var Player */
        $player = $event->getPlayer();
        
        $item = $player->getInventory()->getItemInHand();
        
        if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            
            if ($item->getNamedTag()->getTag('vkit_name') !== null) {
                $vkit = HCFLoader::getInstance()->getHandlerManager()->getvKitManager()->getvKit($item->getNamedTag()->getString('vkit_name'));
                
                if ($vkit !== null) {
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                    
                    $vkit->openvKit($player);
                }
            }
        }
    }
}