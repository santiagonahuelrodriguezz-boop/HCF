<?php

namespace hcf\abilities\items;

use hcf\abilities\entity\BallOfRangeEntity;
use hcf\player\Player;
use hcf\utils\time\Timer;
use hcf\utils\Utils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\Server;

class BallOfRange implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "BallOfRange") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Ballofrange') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $entity = new BallOfRangeEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $player->getLocation()->getYaw(), $player->getLocation()->getPitch()), $player);
                            $entity->setMotion($event->getDirectionVector()->multiply(1.5));
                            $entity->spawnToAll();
                            $player->sendMessage("§6You have activated §cBall of Rage");
                            $world = $player->getWorld();
                            $world->addParticle($player->getPosition(), new HugeExplodeParticle(), [$player]);
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 5, 1));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 5, 2));
                            foreach (Server::getInstance()->getOnlinePlayers() as $online_player) {
                                if ($player->getPosition()->distance($online_player->getPosition()) <= 10) {
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::WEAKNESS(), 20 * 5, 1));
                                    $player->getEffects()->remove(VanillaEffects::WEAKNESS());
                                    $online_player->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 5, 1));
                                    $player->getEffects()->remove(VanillaEffects::WITHER());
                                }
                            }
                            
                            if ($player->getSession()->getFaction() !== null) {
                                $players = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                                     return $target instanceof Player && $player->getPosition()->distance($target->getPosition()) <= 10 && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                                });

                                if (count($players) !== 0) {
                                    foreach ($players as $target) {
                                    $target->getEffects()->remove(VanillaEffects::WEAKNESS());
                                    $target->getEffects()->remove(VanillaEffects::WITHER());
                            		$target->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 5, 1));
                            		$target->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 5, 2));
                                    }
                                }
                            }

                            $player->getSession()->addCooldown('ability.Ballofrange', '§3Ball Of Rage&r&7: &c', 60);
                            $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Ball Of Rage in the last " . Timer::format($player->getSession()->getCooldown("ability.Ballofrange")->getTime()));
                    }
                }
            }
    }

    /*public function onHitByProjectile(ProjectileHitEntityEvent $event) : void
    {
        $hit = $event->getEntityHit();
        if ($hit instanceof Player) {
            $entity = $event->getEntity();
            $player = $entity->getOwningEntity();
            $hitTraceResult = $event->getRayTraceResult();
            if ($player instanceof Player) {
                if ($entity instanceof BallOfRangeEntity) {
                    if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                        return;
                    }

                    if ($player->getCurrentClaim() === 'Spawn') {
                        return;
                    }
                    if ($hit->getSession()->getCooldown('starting.timer') !== null || $hit->getSession()->getCooldown('pvp.timer') !== null) {
                        return;
                    }

                    if ($hit->getCurrentClaim() === 'Spawn') {
                        return;
                    }
                    $pos1 = $player->getPosition();
                    $pos2 = $hit->getPosition();
                    $hit->getEffects()->add(new EffectInstance(VanillaEffects::WEAKNESS(), 5 * 20, 1));
                    $hit->getEffects()->add(new EffectInstance(VanillaEffects::WITHER(), 5 * 20, 1));
                    foreach (Server::getInstance()->getOnlinePlayers() as $players){
                        $players->getNetworkSession()->sendDataPacket(Utils::addParticle($hitTraceResult->getHitVector(), "minecraft:huge_explosion_emitter"));
                        $players->getNetworkSession()->sendDataPacket(Utils::addSound($hitTraceResult->getHitVector(), LevelSoundEvent::EXPLODE));
                    }
                }
            }
        }
    }*/
}