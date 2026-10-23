<?php

declare(strict_types=1);

namespace hcf\abilities\items;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class StromBreaker implements Listener
{
    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $player = $event->getDamager();

        if ($victim instanceof Player && $player instanceof Player) {
            $item = $player->getInventory()->getItemInHand();

            if ($item->getNamedTag()->getTag("Abilities") !== null &&
                $item->getNamedTag()->getString("Abilities") === "StromBreaker") {

                if ($player->getSession()->getCooldown('ability.strombreaker') !== null) {
                    $player->sendMessage("§cYou haven't used StromBreaker in the last " . Timer::format($player->getSession()->getCooldown("ability.strombreaker")->getTime()));
                    return;
                }

                if ($player->getSession()->getCooldown('ability.global') !== null) {
                    $player->sendMessage("§6You have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                    return;
                }

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

                $helmet = $victim->getArmorInventory()->getHelmet();
                if ($helmet->isNull()) {
                    $player->sendMessage("§cThe player does not have a helmet");
                    return;
                }

                $carvedPumpkin = VanillaBlocks::CARVED_PUMPKIN()->asItem();
                if ($helmet->getTypeId() === $carvedPumpkin->getTypeId()) {
                    $player->sendMessage("§cYou cannot use §6StromBreaker§c on a player wearing a Carved Pumpkin!");
                    return;
                }

                if ($player->getCurrentClaim() === 'Spawn') {
                    return;
                }

                $player->sendMessage("§6You have activated §aStromBreaker");
                $player->getSession()->addCooldown('ability.strombreaker', '&l&eStromBreaker&r&7: &c', 120);
                $player->getSession()->addCooldown('ability.global', '&l&5Partner Item&r&7: &c', 10);

                $victim->sendMessage("§cWarning! §eYour helmet will be swapped in §c3 seconds§e by §d" . $player->getName() . "§e using §6StromBreaker§e!");

                HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($victim): void {
                    $helmet = $victim->getArmorInventory()->getHelmet();
                    if ($helmet->isNull()) {
                        return;
                    }

                    $item = $victim->getInventory()->getItemInHand();
                    $victim->getArmorInventory()->setHelmet($item);
                    $victim->getInventory()->setItemInHand($helmet);

                    $victim->sendMessage("§7Your helmet has been swapped!");
                }), 60);

                $usesLeft = $item->getNamedTag()->getInt("uses", 3);
                $usesLeft--;

                if ($usesLeft > 0) {
                    $item->getNamedTag()->setInt("uses", $usesLeft);
                    $item->setLore([
                        "§f",
                        "§7Swap enemy helmet with their hand item.",
                        "§7Cooldown: §f120s",
                        "§ePurchase at §3" . HCFLoader::getInstance()->getConfig()->get('tebex'),
                        "§7Uses left: §f" . $usesLeft
                    ]);
                    $player->getInventory()->setItemInHand($item);
                } else {
                    $player->getInventory()->removeItem($item);
                }
            }
        }
    }
}