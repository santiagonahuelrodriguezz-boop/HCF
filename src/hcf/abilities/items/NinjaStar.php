<?php

namespace hcf\abilities\items;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use hcf\utils\Utils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class NinjaStar implements Listener
{

    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "NinjaStar") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Ninjastar') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if ($player->getSession()->getCooldown('lastDamage') === null) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                                return;
                            }

                            $cause = $player->getLastDamageCause();

                            
                            
                            if($cause === null) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                            }
                            if (!$cause instanceof EntityDamageByEntityEvent) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                                return;
                            }
                            $damager = $cause->getDamager();
                            if (!$damager instanceof Player) {
                                $player->sendMessage(TextFormat::colorize("&cNo one has attacked you in the past 15 seconds!"));
                            }
                            $player->getSession()->addCooldown('ability.Ninjastar', '§bNinja Star&r&7: &c', 160);
                            $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                            $player->sendMessage("§6You have activated §bNinja Star");
                            Utils::PlaySound($player, "random.orb", 1, 1);
                            $player->getSession()->addCooldown('teleporting.ninjastar', '§dTeleporting&r&7: &c', 5);
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $damager): void {
                                $player->teleport($damager->getPosition());

                                if ($damager instanceof Player && $player instanceof Player){
                                    Utils::PlaySound($player, "random.anvil_use", 1, 1);
                                    Utils::PlaySound($damager, "random.anvil_use", 1, 1);
                                    if ($player->getSession() !== null || $damager->getSession() !== null) return;
                                    $player->sendMessage(TextFormat::colorize("&7You've been &dteleported&7 to&f {$damager->getSession()->getName()}."));
                                    $damager->sendMessage(TextFormat::colorize("&f{$player->getSession()->getName()} &7has been &dteleported &7to you."));
                                };

                                foreach (Server::getInstance()->getOnlinePlayers() as $players){
                                    $players->getNetworkSession()->sendDataPacket(Utils::addParticle($damager->getPosition(), "minecraft:end_chest"));
                                }
                                
                            }), 20 * 5);
                                
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Ninja Star in the last " . Timer::format($player->getSession()->getCooldown("ability.Ninjastar")->getTime()));
                    }
                }
            }
    }
}

