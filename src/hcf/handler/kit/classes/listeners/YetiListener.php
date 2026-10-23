<?php

declare(strict_types=1);

namespace hcf\handler\kit\classes\listeners;

use hcf\player\Player;
use hcf\handler\kit\classes\presets\Yeti;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class YetiListener implements Listener
{
    /**
     * @param BlockPlaceEvent $event
     * @priority LOWEST
     */
    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();

        if (!$player instanceof Player) {
            return;
        }

        if ($player->getClass() === null || $player->getClass()->getTypeId() !== Yeti::YETI) {
            return;
        }

        $blockPlaced = $event->getBlock();

        if ($blockPlaced->getTypeId() === VanillaBlocks::PACKED_ICE()->getTypeId()) {
           
            $event->cancel();
        }
    }
}