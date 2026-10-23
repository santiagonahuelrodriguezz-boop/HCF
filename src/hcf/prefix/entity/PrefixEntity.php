<?php

declare(strict_types=1);

namespace hcf\prefix\entity;

use hcf\player\Player;
use hcf\prefix\utils\Utils;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\TextFormat;

class PrefixEntity extends Human
{

    /**
     * @param CompoundTag $nbt
     */
    public function initEntity(CompoundTag $nbt): void
    {
        parent::initEntity($nbt);
        $this->setForceMovementUpdate(false);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag("§e>> §6§lPREFIXES§r §e<<"."\n"." §7Open the menu to reclaim prefix!");
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {

        $source->cancel();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                if ($damager->hasPermission('npc.command') && $damager->getInventory()->getItemInHand()->getCustomName() === "§eRemove NPC §r§7(Right Click)") {
                    $this->kill();
                    return;
                }
                Utils::openPrefixMenu($damager);
            }
        }
    }
}