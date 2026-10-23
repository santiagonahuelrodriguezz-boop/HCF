<?php

namespace hcf\Mrd;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\entity\projectile\Snowball;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\utils\TextFormat as CL;

class SnowballListener implements Listener {

    public function onEntityDamage(EntityDamageEvent $event): void {
        if($event instanceof EntityDamageByChildEntityEvent) {
            $child = $event->getChild();
            $entity = $event->getEntity();
            $damager = $event->getDamager();
            
            if($child instanceof Snowball) {
                if ($damager->getSession()->getCooldown('snowball.cooldown') === null) {
                    if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                        return;
                    }

                    if ($damager->getCurrentClaim() === 'Spawn') {
                        $damager->sendMessage("§b» §cYou cannot use this §6SnowBall in the §aSpawn");
                        return;
                    }
                    
                    $damager->getSession()->addCooldown('snowball.cooldown', ' §c|§r §bSnowball: §7', 10);
                    
                    if ($entity instanceof Player) {
                        $entity->sendMessage(CL::colorize('&c[&7!&c]&r You have been debuffed by &a' . $damager->getName()));
                    }
                    
                    $damager->sendMessage(CL::colorize('&c[&7!&c]&r You used snowball debuff with &a' . $entity->getName()));
                    $entity->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 10 * 10, 2));
                    $entity->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 10 * 10, 2));
                    $entity->getEffects()->add(new EffectInstance(VanillaEffects::NAUSEA(), 10 * 10, 2));
                } else {
                    $damager->sendMessage(CL::colorize("§cYou can't use §bSnowBall§c for §f" . Timer::format($damager->getSession()->getCooldown("snowball.cooldown")->getTime())));
                }
            }
        }
    }
}