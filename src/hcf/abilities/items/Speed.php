<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

class Speed implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($player instanceof Player) {
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Speed") {
                    $event->cancel();

                    if ($player->getSession()->getCooldown('ability.Speed') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if ($player->getSession()->getFaction() !== null) {
                                $players = array_filter(
                                    $player->getServer()->getOnlinePlayers(),
                                    function ($target) use ($player): bool {
                                        return $target instanceof Player
                                            && $player->getPosition()->distance($target->getPosition()) <= 10
                                            && $player->getSession()->getFaction() === $target->getSession()->getFaction();
                                    }
                                );

                                if (count($players) !== 0) {
                                    foreach ($players as $target) {
                                        // Speed III por 12s
                                        $target->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 12, 2));
                                        // Strength I por 12s
                                        $target->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 12, 0));
                                    }
                                }
                            }

                            // Speed III por 12s
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 12, 2));
                            // Strength I por 12s
                            $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 12, 0));

                            $player->sendMessage("§6You have activated §6Speed & Strength");
                            $player->getSession()->addCooldown('ability.Speed', ' &f| §6Speed&r&7: &c', 60);
                            $player->getSession()->addCooldown('ability.global', ' &f| &5Partner Item&r&7: &c', 10);

                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);
                        } else {
                            $player->sendMessage("§6You have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cNo puedes usar el §6Speed§c por §l" . Timer::format($player->getSession()->getCooldown("ability.Speed")->getTime()));
                    }
                }
            }
        }
    }
}