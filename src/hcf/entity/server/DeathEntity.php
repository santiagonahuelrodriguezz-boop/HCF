<?php

namespace hcf\entity\server;

use pocketmine\entity\Human;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\Server;
use hcf\utils\time\Timer;
use hcf\player\Player;
use hcf\utils\Utils;
use pocketmine\item\ItemTypeIds;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\item\VanillaItems;
use hcf\deathban\provider\ProviderDeath;
use pocketmine\permission\DefaultPermissions;

class DeathEntity extends Human
{
    public static function create(Player $player): self
    {
        return self::deathEntity($player); 
    }

    public static function deathEntity(Player $player): self
    {
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

    public function onUpdate(int $currentTick): bool
    {
        $this->setNameTag("§l§aREVIVE\n§r§7Click to revive and continue playing");
        $this->setScale(1);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $event): void
    {
        $event->cancel();
        if (!$event instanceof EntityDamageByEntityEvent) {
            return;
        }
          
        $damager = $event->getDamager();
          
        if (!$damager instanceof Player) {
            return;
        }

        if ($damager->getInventory()->getItemInHand()->getTypeId() === ItemTypeIds::APPLE) {
            if ($damager->hasPermission('npc.command')) {
                $this->kill();
            }
            return;
        }
        
        $lives = ProviderDeath::getInstance()->getLives($damager);
        $cooldown = $damager->getSession()->getCooldown('deathban.cooldown');

        if ($lives > 0) {
            $damager->sendMessage("§aYou have been revived using 1 life.");
            $damager->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
            $damager->getSession()->addCooldown('starting.timer', ' §f|§r §cPvP Timer: §f', 60 * 60);
            ProviderDeath::getInstance()->removeLives($damager, 1);
            $damager->getInventory()->clearAll(); 
            $damager->getArmorInventory()->clearAll();

            if ($cooldown !== null) {
                $damager->getSession()->removeCooldown('deathban.cooldown') || $damager->getSession()->removeCooldown('spawn.tag');
            }
        } elseif ($cooldown === null && $lives <= 0) {
            $damager->sendMessage("§aYour DeathBan cooldown has ended. You have been revived without using lives.");
            $damager->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
            $damager->getSession()->addCooldown('starting.timer', ' §f|§r §aPvP Timer: §f', 60 * 60);
            $damager->getInventory()->clearAll(); 
            $damager->getArmorInventory()->clearAll(); 
        } else {
            $damager->sendMessage("§cYou are on DeathBan cooldown. Wait until your cooldown ends or use lives to revive.");
        }

        if ($damager->getInventory()->getItemInHand() === VanillaItems::DIAMOND_SWORD()) {
            if ($damager->hasPermission(DefaultPermissions::ROOT_OPERATOR)) {
                $this->kill();
            }
        }
    }
}