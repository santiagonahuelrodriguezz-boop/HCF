<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

class FlowerTank implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "FlowerTank") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.flowertank') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 3, 3));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 3, 2));
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::ABSORPTION(), 20 * 3, 1));
                            $player->sendMessage("§6You have activated §4Flower Tank");
                            $player->getSession()->addCooldown('ability.flowertank', '§4Flower Tank&r&7: &c', 45);
                            $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Flower Tank in the last " . Timer::format($player->getSession()->getCooldown("ability.flowertank")->getTime()));
                    }
                }
            }
    }
}