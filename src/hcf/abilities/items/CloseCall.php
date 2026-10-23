<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

class CloseCall implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($player instanceof Player) {
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "CloseCall") {
                    $event->cancel();

                    if ($player->getSession()->getCooldown('ability.CloseCall') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            $duration = 20 * 7;
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), $duration, 1));   
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), $duration, 1)); 
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), $duration, 0));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), $duration, 2)); 

                            $player->sendMessage("§6You have activated §cApple Legend §7(Strength II, Resistance II, Regeneration I, Speed III for 7s)");

                            
                            $player->getSession()->addCooldown('ability.CloseCall', '&l§cApple Legend&r&7: &c', 150); 
                            $player->getSession()->addCooldown('ability.global', '&l&5Partner Item&r&7: &c', 5);

                  
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6You have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Apple Legend in the last " . Timer::format($player->getSession()->getCooldown("ability.CloseCall")->getTime()));
                    }
                }
            }
        }
    }
}