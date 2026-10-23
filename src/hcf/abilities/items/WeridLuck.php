<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\world\sound\XpCollectSound;

class WeridLuck implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "WeridLuck") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.lucky') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            
                            $rand = mt_rand(1, 100);

                            if ($rand <= 50) {
                                $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * 5, 3));
                                $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(), 20 * 5, 2));
                            } else {
                                $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 5, 2));
                                $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 5, 1));
                            }

                            $player->sendMessage("§6You have activated §aWeridLuck");
                            $player->getSession()->addCooldown('ability.lucky', ' &f| §aWeridLuck&r&7: &c', 30);
                            $player->getSession()->addCooldown('ability.global', ' &f| &5Partner Item&r&7: &c', 10);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);
                            $player->getWorld()->addSound($player->getPosition(), new XpCollectSound());
                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't WeridLuck in the last " . Timer::format($player->getSession()->getCooldown("ability.lucky")->getTime()));
                    }
                }
            }
    }
}