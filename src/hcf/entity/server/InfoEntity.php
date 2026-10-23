<?php

declare(strict_types=1);

namespace hcf\entity\server;

use hcf\HCFLoader;
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
use hcf\kohi\Kohi;

class InfoEntity extends Entity
{
    public bool $canCollide = false;
    protected bool $immobile = true;

    protected function getInitialDragMultiplier(): float{ return 0.00; }

    protected function getInitialGravity(): float{ return 0.00; }

    public static function create(Player $player): self
    {
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($player->getLocation()->x),
                new DoubleTag($player->getLocation()->y + 1.5),
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

        return new self(Location::fromObject($player->getPosition(), $player->getWorld()), $nbt);
    }

    public function canBeMovedByCurrents(): bool
    {
        return false;
    }

    public function onUpdate(int $currentTick): bool
    {
        $this->setNameTagAlwaysVisible();
        $this->setNameTag("• ".Kohi::SERVER." •\n".
"\n
§c HCF §f| §fMAP §cV:\n Protection IV | Sharpness IV \n
\n
§f Top Factions Rewards: \n
§c 1.  §f25$ §cGiftcard §fDemon
§c 2. §f15$ §cGiftcard §fDemon
§c 3. §f10$ §cGiftcard §fDemon
\n
§f Top §c1 §f(Kills) Reward: \n
§c 5$ §fGiftcard\n
\n".
"§g> ".Kohi::IP." §g<");
        $this->setScale(0.001);
        return parent::onUpdate($currentTick);
    }

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
               
            }
        }
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::XP_ORB;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.0, 0.0);
    }

    public function canBePushed(): bool
    {
        return false; 
    }
}