<?php

namespace hcf\abilities\items;

use hcf\session\extras\SessionFreeze;
use hcf\abilities\task\ImmobilizerTask;
use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\Utils;
use hcf\utils\time\Timer;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

class Freeze implements Listener
{
    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        $player = $event->getDamager();

        if ($entity instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();
            $tag = $item->getNamedTag()->getTag("Abilities");

            if ($tag !== null && $item->getNamedTag()->getString("Abilities") === "Freeze") {

                if ($player->getSession()->getCooldown('ability.freeze') === null) {
                    if ($player->getSession()->getCooldown('ability.global') === null) {

                        if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                            return;
                        }

                        if ($entity->getSession()->getCooldown('starting.timer') !== null || $entity->getSession()->getCooldown('pvp.timer') !== null) {
                            return;
                        }

                        if ($entity->getSession()->getFaction() !== null && $player->getSession()->getFaction() !== null) {
                            if ($entity->getSession()->getFaction() === $player->getSession()->getFaction()) {
                                $player->sendMessage(TextFormat::colorize("§eYou cannot hurt §2" . $entity->getName() . "§e."));
                                $event->cancel();
                                return;
                            }
                        }

                        if ($player->getCurrentClaim() === 'Spawn') {
                            return;
                        }

                        Utils::PlaySound($player, "random.orb", 1, 1);
                        $player->sendMessage("§6You have activated §bFreeze");
                        $player->getSession()->addCooldown('ability.freeze', '§bFreeze§7: §f', 120);
                        $player->getSession()->addCooldown('ability.global', '§5Partner Item§7: §f', 5);

                        SessionFreeze::addFreeze($entity);
                        $entity->sendTitle("§l§bYou were frozen");
                        new ImmobilizerTask($entity);

                        if ($item->getCount() > 1) {
                            $item->setCount($item->getCount() - 1);
                        } else {
                            $item = VanillaItems::AIR();
                        }
                        $player->getInventory()->setItemInHand($item);

                    } else {
                        $player->sendMessage("§6You have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                    }
                } else {
                    $player->sendMessage("§cYou used §bFreeze §cin the last " . Timer::format($player->getSession()->getCooldown("ability.freeze")->getTime()));
                }
            }
        }
    }

    public function onMove(PlayerMoveEvent $event): void
{
    $player = $event->getPlayer();

    if (SessionFreeze::isFreeze($player)) {
        $from = $event->getFrom();
        $to = $event->getTo();

        // Bloquear movimiento pero permitir rotación (yaw/pitch)
        if (
            $from->getX() !== $to->getX() ||
            $from->getY() !== $to->getY() ||
            $from->getZ() !== $to->getZ()
        ) {
            $event->setTo(new \pocketmine\entity\Location(
                $from->getX(),
                $from->getY(),
                $from->getZ(),
                $to->getWorld(), // mantener el mismo mundo
                $to->getYaw(),   // permitir rotar cámara
                $to->getPitch()
            ));
        }
    }
    }

    public function onHit(EntityDamageByEntityEvent $event): void
    {
        $entity = $event->getEntity();
        if ($entity instanceof Player) {
            SessionFreeze::hitFreeze($entity);
        }
    }
}