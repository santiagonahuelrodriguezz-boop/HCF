<?php

declare(strict_types=1);

namespace hcf\entity\server;

use Himbeer\LibSkin\SkinConverter;
use JetBrains\PhpStorm\Pure;
use hcf\HCFLoader;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

class NetworksEntity extends Human
{
    
    public bool $canCollide = false;
    protected bool $immobile = true;

	protected function getInitialDragMultiplier() : float{ return 0.00; }

	protected function getInitialGravity() : float{ return 0.00; }
	
    public static function create(Player $player): self
    {
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($player->getLocation()->x),
                new DoubleTag($player->getLocation()->y),
                new DoubleTag($player->getLocation()->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($player->getMotion()->x),
                new DoubleTag($player->getMotion()->y),
                new DoubleTag($player->getMotion()->z)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($player->getLocation()->yaw),
                new FloatTag($player->getLocation()->pitch)
            ]));
        return new self($player->getLocation(), $player->getSkin(), $nbt);
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    public function onUpdate(int $currentTick): bool
    {
        
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTag("§l§f@§4DemonHCF §fMCPE§r"."\n"."§r \n"."§bDiscord: §gdsc.gg/demon"."\n"."§bStore: §gdsc.gg/@demon"."\n"."§bWebSite: §gdsc.gg/demon§r\n"."");
        $this->setScale(0.01);
        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $source): void {
        $source->cancel();
        if ($source instanceof EntityDamageByEntityEvent) {
            $damager = $source->getDamager();
            if ($damager instanceof Player) {
                if ($damager->hasPermission('npc.command') && $damager->getInventory()->getItemInHand()->getCustomName() === "§eRemove NPC §r§7(Right Click)") {
                    $this->kill();
                    return;
            }
         }
      }
   }
}