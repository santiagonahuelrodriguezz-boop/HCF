<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

class Cactus implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Cactus") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Cactus') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 10 * 20, 1));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 10 * 20, 2));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 10 * 20, 1));
                            $player->sendMessage("§6You have activated §2Cactus");
                            $player->getSession()->addCooldown('ability.Cactus', '§2Cactus&r&7: &c', 60);
                            $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Captus in the last " . Timer::format($player->getSession()->getCooldown("ability.Cactus")->getTime()));
                    }
                }
            }
    }
}