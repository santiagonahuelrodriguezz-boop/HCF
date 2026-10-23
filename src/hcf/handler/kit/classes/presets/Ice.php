<?php

declare(strict_types=1);

namespace hcf\handler\kit\classes\presets;

use hcf\handler\kit\classes\HCFClass;
use hcf\HCFLoader;
use hcf\player\Player;
use hcf\session\extras\SessionFreeze;
use hcf\abilities\task\ImmobilizerTask;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class Ice extends HCFClass
{
    private array $hitCounter = [];

    public function __construct()
    {
        parent::__construct(self::ICE);
    }

    public function getArmorItems(): array
    {
        return [
            VanillaItems::IRON_HELMET(),
            VanillaItems::IRON_CHESTPLATE(),
            VanillaItems::IRON_LEGGINGS(),
            VanillaItems::DIAMOND_BOOTS()
        ];
    }

    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::SPEED(), 20 * 9, 1),
            new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 9, 1),
            new EffectInstance(VanillaEffects::STRENGTH(), 20 * 9, 0),
        ];
    }

    public function onRemoveClass(Player $player): void
    {
        $player->getEffects()->clear();
    }

    public function handleDamageByChildEntity(EntityDamageByChildEntityEvent $event): void
    {
        $child = $event->getChild();
        $damager = $event->getDamager();
        $entity = $event->getEntity();

        if (!$child instanceof Arrow || !$damager instanceof Player || !$entity instanceof Player) return;
        if ($damager->getClass() === null || $damager->getClass()->getTypeId() !== HCFClass::ICE) return;

        if ($damager->getSession()->getCooldown("ice.arrow") === null) {
            if ($entity->getSession()->getFaction() !== null && $damager->getSession()->getFaction() !== null) {
                if ($entity->getSession()->getFaction() === $damager->getSession()->getFaction()) {
                    $damager->sendMessage(TextFormat::colorize("§eYou cannot freeze your teammate."));
                    return;
                }
            }
            SessionFreeze::addFreeze($entity);
            new ImmobilizerTask($entity);
            $damager->getSession()->addCooldown("ice.arrow", "&l&bIce Arrow&r&7: &r&c", 60);
            $damager->sendMessage(TextFormat::colorize("&b❄ You have used your Ice Arrow!"));
            $entity->sendTitle("§l§bYou were frozen!");
            return;
        }

        $entity->sendMessage(TextFormat::colorize('&c&lMarked! &r&eAn Ice arrow hit you (+10% damage for 10s)!'));
        $damager->sendMessage(TextFormat::colorize('&b[&fIce Mark&e] &6Marked &f' . $entity->getName() . ' &6for 10 seconds.'));
        $entity->setNameTag(TextFormat::colorize('&b' . $entity->getName()));
        $entity->getSession()->addCooldown('ice.mark', '&l&bIce Mark&r&7: &r&c', 10);
        HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($entity): void {
            if ($entity->isOnline()) {
                $entity->setNameTag(TextFormat::colorize('&f' . $entity->getName()));
            }
        }), 20 * 10);
    }

    public function handleDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        $entity = $event->getEntity();

        if (!$damager instanceof Player || !$entity instanceof Player) return;
        if ($damager->getClass() === null || $damager->getClass()->getTypeId() !== HCFClass::ICE) return;
        $item = $damager->getInventory()->getItemInHand();
        if ($item->getTypeId() !== VanillaItems::IRON_SWORD()->getTypeId()) return;

        $name = $damager->getName();
        $this->hitCounter[$name] = ($this->hitCounter[$name] ?? 0) + 1;

        if ($this->hitCounter[$name] >= 10) {
            $this->hitCounter[$name] = 0;
            $entity->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 10, 0));
            $damager->sendMessage(TextFormat::colorize('&b&lYou unleashed the Frost Curse!'));
            $entity->sendMessage(TextFormat::colorize('&cYou have been cursed with &7Wither&r&c by an Ice warrior!'));
        }
    }
}