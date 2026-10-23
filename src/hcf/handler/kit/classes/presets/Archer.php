<?php

declare(strict_types=1);

namespace hcf\handler\kit\classes\presets;

use hcf\handler\kit\classes\HCFClass;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class Archer extends HCFClass
{
    
    /** @var array */
    public array $archerMark = [];
    
    /**
     * Archer construct.
     */
    public function __construct()
    {
        parent::__construct(self::ARCHER);
    }
    
    /**
     * @return Item[]
     */
    public function getArmorItems(): array
    {
        return [
            VanillaItems::LEATHER_CAP(),
            VanillaItems::LEATHER_TUNIC(),
            VanillaItems::LEATHER_PANTS(),
            VanillaItems::LEATHER_BOOTS()
        ];
    }
    
    /**
     * @return EffectInstance[]
     */
    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 2),
            new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 15, 1),
            new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20 * 15, 0)
        ];
    }
    
    /**
     * @param EntityDamageEvent $event
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        $player = $event->getEntity();
        
        if ($event->isCancelled())
            return;

        if ($player instanceof Player) {
            if (isset($this->archerMark[$player->getName()]) && $this->archerMark[$player->getName()] > time()) {
            // if (HCFLoader::getInstance()->inTag($tag, $player->getName())) {
                $baseDamage = $event->getBaseDamage();
                $event->setBaseDamage($baseDamage + 0.3);
            }
        }
    }
    
    /**
     * @param EntityDamageByChildEntityEvent $event
     */
    public function handleDamageByChildEntity(EntityDamageByChildEntityEvent $event): void
{
    $child = $event->getChild();
    $damager = $event->getDamager();
    $entity = $event->getEntity();

    if ($entity instanceof Player && $damager instanceof Player) {
        // Solo checamos que sea flecha
        if (!$child instanceof Arrow) return;

        // Verificar que el atacante no esté en cooldown de PvP o starting
        if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
            return;
        }

        // No permitir marcar a alguien con la misma clase que el atacante
        if ($damager->getClass()?->getTypeId() !== null && $damager->getClass()->getTypeId() === $entity->getClass()?->getTypeId()) {
            $damager->sendMessage(TextFormat::colorize('&cYou can\'t mark someone with the same class!'));
            return;
        }

        // Aplicar Archer Mark
        $entity->sendMessage(TextFormat::colorize('&c&lMarked! &r&eAn archer shot you (+10% damage) for 5 seconds.'));
        $damager->sendMessage(TextFormat::colorize('&e[&9Archer Range &e(&c' . intval($entity->getPosition()->distance($damager->getPosition())) . '&e)] &6Marked player.'));

        $this->archerMark[$entity->getName()] = time() + 5;

        // Cambiar temporalmente NameTag
        $entity->setNameTag(TextFormat::LIGHT_PURPLE . '[ArcherMarked] ' . $entity->getName());

        HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($entity): void {
            if ($entity->isOnline()) {
                $entity->setNameTag(TextFormat::RED . $entity->getName());
            }
        }), 20 * 5);
    }
    }
    
    /**
     * @param PlayerItemUseEvent $event
     */
    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if ($player instanceof Player) {
            if ($player->getClass() === null)
                return;
                
            if ($player->getClass()->getTypeId() === HCFClass::ARCHER) {
                if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null)
                    return;
            
                if ($player->getCurrentClaim() === 'Spawn')
                    return;
                    
                if ($item->getTypeId() === VanillaItems::SUGAR()->getTypeId()) {
                    if ($player->getSession()->getCooldown('speed.cooldown') !== null)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 7, 3));
                    $player->getSession()->addCooldown('speed.cooldown', '&l&4Speed&r&7: &r&c', 60);
                    $player->sendMessage(TextFormat::colorize('&eYou have used your &4Speed IV Buff'));
                    
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                } elseif ($item->getTypeId() === VanillaItems::FEATHER()->getTypeId()) {
                    if ($player->getSession()->getCooldown('jump.cooldown') !== null)
                        return;
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::JUMP_BOOST(), 20 * 7, 7));
                    $player->getSession()->addCooldown('jump.cooldown', '&l&4Jump Boost&r&7: &r&c', 60);
                    $player->sendMessage(TextFormat::colorize('&eYou have used your &4Jump Boost VIII Buff'));
                    
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                }
            }
        }
    }
}