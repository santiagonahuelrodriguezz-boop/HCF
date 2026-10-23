<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\item\PotionType;

class PotionRefill implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Potion") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Potion') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $potion = VanillaItems::SPLASH_POTION()->setType(PotionType::STRONG_HEALING);
                            $potion->setCount(36);
                            $player->sendMessage("§6You have activated §l§4Potion Refill");
                            $player->getInventory()->addItem($potion);
                            $player->getSession()->addCooldown('ability.Potion', '§4Potion Refill: &c', 60);
                            $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§c§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cNo puedes usar el §l§4Potion Refill§c por §l" . Timer::format($player->getSession()->getCooldown("ability.Potion")->getTime()));
                    }
                }
            }
    }

    public function getMaxStackSize(): int
    {
        return 1;
    }
}