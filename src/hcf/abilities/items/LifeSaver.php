<?php

namespace hcf\abilities\items;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use hcf\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class LifeSaver implements Listener
{
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "LifeSaver") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.LifeSaver') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }

                            if ($player->getSession()->getFaction() !== null) {
                                $factionMembers = array_filter($player->getServer()->getOnlinePlayers(), function ($target) use ($player): bool {
                                    return $target instanceof Player && 
                                           $player->getPosition()->distance($target->getPosition()) <= 30 &&
                                           $player->getSession()->getFaction() === $target->getSession()->getFaction() &&
                                           $player !== $target;
                                });

                                if (empty($factionMembers)) {
                                    $player->sendMessage(TextFormat::colorize("&cNo faction members are within 30 blocks!"));
                                    return;
                                }

                                $nearbyMember = $this->getNearbyMember($player, $factionMembers);
                                if ($nearbyMember === null) {
                                    $player->sendMessage(TextFormat::colorize("&cNo faction members are within 30 blocks!"));
                                    return;
                                }

                                $player->getSession()->addCooldown('ability.LifeSaver', '§gF.Teleport&r&7: &c', 160);
                                $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                                $player->sendMessage("§6You have activated §bFaction Teleport");
                                Utils::PlaySound($player, "random.orb", 1, 1);
                                $player->getSession()->addCooldown('teleporting.LifeSaver', ' &l&f| §l§dTeleporting&r&7: &c', 5);

                                HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player, $nearbyMember): void {
                                    $player->teleport($nearbyMember->getPosition());

                                    if ($nearbyMember instanceof Player && $player instanceof Player) {
                                        Utils::PlaySound($player, "random.anvil_use", 1, 1);
                                        Utils::PlaySound($nearbyMember, "random.anvil_use", 1, 1);
                                        $player->sendMessage(TextFormat::colorize("&7You've been &dteleported&7 to&f {$nearbyMember->getName()}."));
                                        $nearbyMember->sendMessage(TextFormat::colorize("&f{$player->getName()} &7has been &dteleported &7to you."));
                                    }

                                    foreach (Server::getInstance()->getOnlinePlayers() as $players) {
                                        $players->getNetworkSession()->sendDataPacket(Utils::addParticle($nearbyMember->getPosition(), "minecraft:end_chest"));
                                    }
                                }), 20 * 5);

                                $item = $event->getItem();
                                $item->pop();
                                $player->getInventory()->setItemInHand($item);

                            } else {
                                $player->sendMessage(TextFormat::colorize("&cYou are not in a faction!"));
                            }
                        } else {
                            $player->sendMessage("§6You have a cooldown on §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't used LifeSaver in the last " . Timer::format($player->getSession()->getCooldown("ability.LifeSaver")->getTime()));
                    }
                }
            }
        }
    }

    private function getNearbyMember(Player $player, array $members): ?Player
    {
        $nearbyMember = null;
        $nearbyDistance = PHP_INT_MAX;
        $playerPosition = $player->getPosition();
        foreach ($members as $member) {
            $distance = $playerPosition->distance($member->getPosition());
            if ($distance < $nearbyDistance) {
                $nearbyDistance = $distance;
                $nearbyMember = $member;
            }
        }
        return $nearbyMember;
    }
}