<?php

namespace hcf\abilities\items;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\item\Items;
use hcf\utils\time\Timer;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\item\VanillaItems;

class Swapper implements Listener
{
    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $player = $event->getDamager();

        if ($victim instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();

            if ($item->getNamedTag()->getTag("Abilities") !== null && $item->getNamedTag()->getString("Abilities") === "Swapper") {

                if ($player->getSession()->getCooldown('ability.Swapper') === null) {
                    if ($player->getSession()->getCooldown('ability.global') === null) {

                        if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                            return;
                        }

                        if ($victim->getSession()->getCooldown('starting.timer') !== null || $victim->getSession()->getCooldown('pvp.timer') !== null) {
                            return;
                        }

                        // 🔒 Bloqueo: si el victim ya tiene un lock de casco, no permitir otra ability
                        if ($victim->getSession()->getCooldown("helmet.lock") !== null) {
                            $player->sendMessage("§cThis player already has a helmet ability active, wait until it ends.");
                            $event->cancel();
                            return;
                        }

                        if ($victim->getSession()->getFaction() !== null && $player->getSession()->getFaction() !== null) {
                            if ($victim->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                $player->sendMessage(TextFormat::colorize("§eYou cannot hurt §2" . $victim->getSession()->getName() . "§e."));
                                $event->cancel();
                                return;
                            }
                        }

                        $helmet = $victim->getArmorInventory()->getHelmet();

                        if ($helmet->isNull()) {
                            $player->sendMessage("§cThe player does not have a helmet");
                            return;
                        }

                        if ($player->getCurrentClaim() === 'Spawn') {
                            return;
                        }

                        $player->sendMessage("§6You have activated §aSwapper");
                        $player->getSession()->addCooldown('ability.Swapper', ' &l&f| &l&gSwapper&r&7: &c', 140);
                        $player->getSession()->addCooldown('ability.global', ' &l&f| &l&5Partner Item&r&7: &c', 10);

                        // 🔒 Agregar cooldown exclusivo para bloquear abilities de casco
                        $victim->getSession()->addCooldown("helmet.lock", " &l&f| &l&6Helmet Ability&r&7: &c", 7);

                        $usesLeft = $item->getNamedTag()->getInt("uses", 5);
                        $usesLeft--;

                        if ($usesLeft > 0) {
                            $item->getNamedTag()->setInt("uses", $usesLeft);
                            $item->setLore([
                                "§f",
                                "§7Hit an enemy with this item and",
                                "§7turn their helmet",
                                "§7into a gold helmet for 7 seconds.",
                                "§ePurchase at §3" . HCFLoader::getInstance()->getConfig()->get('tebex'),
                                "§7Uses left: §f" . $usesLeft
                            ]);
                            $player->getInventory()->setItemInHand($item);
                        } else {
                            $player->getInventory()->removeItem($item);
                        }

                        $victim->getArmorInventory()->setHelmet(VanillaItems::GOLDEN_HELMET());

                        HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $helmet): void {
                            if ($victim->isOnline()) {
                                $victim->getArmorInventory()->setHelmet($helmet);

                                // 🔓 Al acabar los 7 segundos, se borra el lock automáticamente
                                $victim->getSession()->removeCooldown("helmet.lock");

                                $victim->sendMessage(" ");
                                $victim->sendMessage("§l§cYour helmet is back to normal!");
                                $victim->sendMessage(" ");
                            }
                        }), 20 * 7);
                    } else {
                        $player->sendMessage("§6You have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                    }
                } else {
                    $player->sendMessage("§cYou haven't used Swapper in the last " . Timer::format($player->getSession()->getCooldown("ability.Swapper")->getTime()));
                }
            }
        }
    }
}