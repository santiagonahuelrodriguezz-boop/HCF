<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerItemHeldEvent;

class Galleta implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Galleta") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.CloseCall') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if ($player->getHealth() > 8) {
                                $player->sendMessage("§cYou do not have 4 hearts §4! §cor less!");
                                return;
                            }

                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 6, 3));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 6, 4));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 6, 1));
                            $player->sendMessage("§6You have activated §cCloseCall");
                            $player->getSession()->addCooldown('ability.CloseCall', '&l§cCloseCall&r&7: &c', 60);
                            $player->getSession()->addCooldown('ability.global', '&l&5Partner Item&r&7: &c', 10);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't CloseCall in the last " . Timer::format($player->getSession()->getCooldown("ability.CloseCall")->getTime()));
                    }
                }
            }
    }
}