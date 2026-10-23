<?php

namespace hcf\extras;

use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\item\Armor;
use pocketmine\item\Book;
use pocketmine\player\Player;

class BookMoveListener implements Listener {

    public function onInventoryTransaction(InventoryTransactionEvent $event): void {
        $transaction = $event->getTransaction();
        $player = $transaction->getSource();

        if (!$player instanceof Player) {
            return;
        }

        foreach ($transaction->getActions() as $action) {
            $sourceItem = $action->getSourceItem();
            $targetItem = $action->getTargetItem();

            if ($sourceItem instanceof Book && $targetItem instanceof Armor) {
                if ($targetItem->getLore() !== null && count($targetItem->getLore()) > 0) {
                    $player->sendMessage("Esta pieza de armadura ya tiene una descripción.");
                    return;
                }

                $originalItem = clone $targetItem;
                $customName = $targetItem->getCustomName();
                $enchants = $targetItem->getEnchantments();

                $bookLore = $sourceItem->getLore();

                if (!empty($bookLore)) {
                    $targetItem->setLore($bookLore); 
                } else {
                    return;
                }

                if ($customName !== null) {
                    $targetItem->setCustomName($customName);
                }
                if (!empty($enchants)) {
                    foreach ($enchants as $enchantment) {
                        $targetItem->addEnchantment($enchantment);
                    }
                }

                $playerInventory = $player->getInventory();
                $playerInventory->removeItem($originalItem);

                $playerInventory->removeItem($sourceItem);

                $action->getInventory()->setItem($action->getSlot(), $targetItem);

                $player->sendMessage("La descripción del libro ha sido transferida a la pieza de armadura.");

                $event->cancel();
                return;
            }
        }
    }
}