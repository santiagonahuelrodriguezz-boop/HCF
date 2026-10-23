<?php

declare(strict_types=1);

namespace hcf\Mrd;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\nbt\tag\IntTag;

class AutoFixListener implements Listener {

    public function onEntityDamage(EntityDamageEvent $event): void {
        $entity = $event->getEntity();
        if (!$entity instanceof Player) return;

        $armorInventory = $entity->getArmorInventory();
        foreach ($armorInventory->getContents() as $slot => $item) {
            if (!$item instanceof Armor || !$this->hasAutofixEnchantment($item)) {
                continue;
            }
            if ($item->getDamage() > 0) {
                $item->setDamage(0);
                $armorInventory->setItem($slot, $item);
            }
        }
    }

    private function hasAutofixEnchantment(Item $item): bool {
        $nbt = $item->getNamedTag();
        return $nbt->getTag("Autofix") instanceof IntTag && $nbt->getInt("Autofix") === 1;
    }
}