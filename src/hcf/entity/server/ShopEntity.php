<?php

declare(strict_types=1);

namespace hcf\entity\server;

use hcf\handler\shop\ShopMenu;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ShopEntity extends Entity
{
    public bool $canCollide = false;
    protected bool $immobile = true;

    protected function getInitialDragMultiplier(): float{ return 0.00; }

    protected function getInitialGravity(): float{ return 0.00; }

    public static function create(Player $player): self
    {
        
        $location = $player->getLocation();
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($location->x),
                new DoubleTag($location->y + 1.5), 
                new DoubleTag($location->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($player->getMotion()->x),
                new DoubleTag($player->getMotion()->y),
                new DoubleTag($player->getMotion()->z)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($location->yaw),  
                new FloatTag($location->pitch) 
            ]));

      
        return new self(Location::fromObject($location, $player->getWorld()), $nbt);
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    public function onUpdate(int $currentTick): bool
    {
  
        $this->setNameTagAlwaysVisible();
        $this->setNameTag("§e>> §a§lSHOP§r §e<<"."\n"." §7Open the shop to sell and buy blocks ");
        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $source): void 
    {
        $source->cancel();

        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();

            if ($damager instanceof Player) {
                if ($damager->hasPermission('npc.command') && 
                    $damager->getInventory()->getItemInHand()->getCustomName() === "§eRemove NPC §r§7(Right Click)") {
                    $this->kill();
                    return;
                }
                ShopMenu::MenuShop($damager);
            }
        }
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::WANDERING_TRADER; 
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(1.8, 0.6);
    }

    public function canBePushed(): bool
    {
        return false; 
    }
}