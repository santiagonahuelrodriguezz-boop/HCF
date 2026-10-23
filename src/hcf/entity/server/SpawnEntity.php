<?php

namespace hcf\entity\server;

use pocketmine\Server;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

class SpawnEntity extends Human {

    public static function create(Player $player): self {
        $nbt = CompoundTag::create()->setTag("Pos", new ListTag([
            new DoubleTag($player->getLocation()->x),
            new DoubleTag($player->getLocation()->y),
            new DoubleTag($player->getLocation()->z)
        ]))->setTag("Motion", new ListTag([
            new DoubleTag($player->getMotion()->x),
            new DoubleTag($player->getMotion()->y),
            new DoubleTag($player->getMotion()->z)
        ]))->setTag("Rotation", new ListTag([
            new FloatTag($player->getLocation()->yaw),
            new FloatTag($player->getLocation()->pitch)
        ]));
        return new self($player->getLocation(), $player->getSkin(), $nbt);
    }

    public function onUpdate(int $currentTick): bool {
        $this->setNameTag("§e>> §l§bSPAWN §r§e<< \n §7Click to teleport to a §aSPAWN");
        $this->setScale(1);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $event): void {
        $event->cancel();
        
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();

            if ($damager instanceof Player) {
             
                if ($damager->hasPermission('npc.command') && 
                    $damager->getInventory()->getItemInHand()->getCustomName() === "§eRemove NPC §r§7(Right Click)") {
                    $this->kill(); 
                    $damager->sendMessage("§b» §7The NPC has been removed.");
                    return;
                }
             
                $damager->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
                $damager->sendMessage("§b» §7Teleported to the world §aspawn!");
            }
        }
    }
}