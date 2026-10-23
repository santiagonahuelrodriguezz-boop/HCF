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

class EffectDisabler implements Listener
{
    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $player = $event->getDamager();
        if ($victim instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "EffectDisabler") {
                    if ($player->getSession()->getCooldown('ability.effectdisabler') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {

                            $effect = $victim->getEffects()->all();

                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($victim->getSession()->getCooldown('starting.timer') !== null || $victim->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($victim->getSession()->getFaction() !== null && $player->getSession()->getFaction() !== null) {
                                if ($victim->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                    $player->sendMessage(TextFormat::colorize("§eYou cannot hurt §2" . $victim->getSession()->getName() . "§e."));
                                    $event->cancel();
                                    return;
                                }
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if (count($effect) <= 0) {
                                $player->sendMessage("§cEl Jugador " . $victim->getName() . " no tienen ningun efecto.");
                                return;
                            }
                            $victim->getEffects()->clear();
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 0.5);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 1.5);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 2);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 2.5);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 3);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 3.5);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 4);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 4.5);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 5);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    $victim->getEffects()->clear();
                                }
                            }), 20 * 5.5);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim, $effect): void {
                                if ($victim->isOnline()) {
                                    foreach ($effect as $e) {
                                        $victim->getEffects()->add($e);
                                    }
                                }
                            }), 20 * 6);
                            $player->sendMessage("§6You have activated §aEffects Disabler in " . $victim->getName());
                            $player->getSession()->addCooldown('ability.effectdisabler', '§aEffects Disabler&r&7: &c', 120);
                            $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);

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
                        $player->sendMessage("§cYou can't use the Effects Disabler in the last " . Timer::format($player->getSession()->getCooldown("ability.effectdisabler")->getTime()));
                    }
                }
            }
        }
    }

    public function breakBocks(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if ($player->getSession()->getCooldown('ability.exoticbonetag') !== null) {
                $event->cancel();
            }
        }

    }

    public function placeBlocks(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if ($player->getSession()->getCooldown('ability.exoticbonetag') !== null) {
                $event->cancel();
            }
        }

    }
}
