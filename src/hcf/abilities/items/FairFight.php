<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\item\VanillaItems;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use hcf\HCFLoader;

class FairFight implements Listener
{
    private int $duration = 10; // segundos de duración del debuff
    private int $cooldown = 60; // segundos de cooldown
    private int $globalCooldown = 10; // cooldown global de partner items

    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if (!$player instanceof Player) return;

        // Verifica si el ítem tiene el tag de habilidad
        $tag = $item->getNamedTag()->getTag("Abilities");
        if ($tag === null || $item->getNamedTag()->getString("Abilities") !== "FairFight") {
            return;
        }

        $event->cancel();

        $session = $player->getSession();

        // Chequeo de cooldowns
        if ($session->getCooldown('ability.FairFight') !== null) {
            $player->sendMessage("§cYou haven't §4FairFight§c in the last " . Timer::format($session->getCooldown('ability.FairFight')->getTime()));
            return;
        }

        if ($session->getCooldown('ability.global') !== null) {
            $player->sendMessage("§6You have cooldown of §dPartner Item §f" . Timer::format($session->getCooldown('ability.global')->getTime()));
            return;
        }

        // Evita uso en spawn o con timers activos
        if ($session->getCooldown('starting.timer') !== null || $session->getCooldown('pvp.timer') !== null) {
            return;
        }
        if ($player->getCurrentClaim() === 'Spawn') {
            return;
        }

        // Encuentra el jugador más cercano en 5 bloques
        $target = $this->getNearestPlayer($player, 5);
        if ($target === null) {
            $player->sendMessage("§cNo players nearby to use §4FairFight§c!");
            return;
        }

        // Aplica el efecto "Fair Fight"
        $originalEnchantments = [];

        foreach ($target->getArmorInventory()->getContents() as $slot => $armor) {
            if (!in_array($armor->getTypeId(), [
                VanillaItems::DIAMOND_HELMET()->getTypeId(),
                VanillaItems::DIAMOND_CHESTPLATE()->getTypeId(),
                VanillaItems::DIAMOND_LEGGINGS()->getTypeId(),
                VanillaItems::DIAMOND_BOOTS()->getTypeId()
            ])) continue;

            $ench = $armor->getEnchantment(VanillaEnchantments::PROTECTION());
            if ($ench !== null && $ench->getLevel() > 1) {
                $originalEnchantments[$slot] = $ench->getLevel();
                $armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 1));
                $target->getArmorInventory()->setItem($slot, $armor);
            }
        }

        $target->sendMessage("§cYour armor protection has been reduced temporarily!");
        $player->sendMessage("§6You used §4FairFight§6 on §f" . $target->getName() . "§6, reducing their protection.");

        // Devuelve la protección después del tiempo
        HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($target, $originalEnchantments): void {
            foreach ($target->getArmorInventory()->getContents() as $slot => $armor) {
                if (isset($originalEnchantments[$slot])) {
                    $armor->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), $originalEnchantments[$slot]));
                    $target->getArmorInventory()->setItem($slot, $armor);
                }
            }
            $target->sendMessage("§aYour armor protection has been restored!");
        }), $this->duration * 20);

        // Cooldowns y consumo del ítem
        $session->addCooldown('ability.FairFight', '§cFairFight&r&7: &c', $this->cooldown);
        $session->addCooldown('ability.global', '&5Partner Item&r&7: &c', $this->globalCooldown);

        $item->pop();
        $player->getInventory()->setItemInHand($item);
    }

    private function getNearestPlayer(Player $player, float $radius): ?Player
    {
        $world = $player->getWorld();
        $nearby = null;
        $minDistance = $radius + 1;

        foreach ($world->getNearbyEntities($player->getBoundingBox()->expandedCopy($radius, $radius, $radius), $player) as $entity) {
            if ($entity instanceof Player && !$entity->isClosed() && $entity->isOnline()) {
                $distance = $player->getPosition()->distance($entity->getPosition());
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $nearby = $entity;
                }
            }
        }

        return $nearby;
    }
}