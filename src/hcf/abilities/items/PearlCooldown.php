<?php

namespace hcf\abilities\items;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class PearlCooldown implements Listener
{
    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $player = $event->getDamager();
        if ($victim instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "PearlCooldown") {
                    if ($player->getSession()->getCooldown('ability.PearlCooldown') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {

                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($victim->getSession()->getCooldown('starting.timer') !== null || $victim->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            $victim->getSession()->addCooldown('enderpearl', '§3Enderpearl&7: &9', 15);

                            $player->sendMessage("§6You have activated §aEffects Disabler in " . $victim->getName());
                            $player->getSession()->addCooldown('ability.PearlCooldown', '&l§2EnderDisable&r&7: &c', 120);
                            $player->getSession()->addCooldown('ability.global', '&l&5Partner Item&r&7: &c', 10);

                            if($item->getCount() > 1){
                                $item->setCount($item->getCount() - 1);
                            } else {
                                $item = VanillaItems::AIR();
                            }
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou can't use the EnderPearl Disabler in the last " . Timer::format($player->getSession()->getCooldown("ability.PearlCooldown")->getTime()));
                    }
                }
            }
        }
    }
}
