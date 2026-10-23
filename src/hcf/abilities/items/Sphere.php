<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\scheduler\ClosureTask;

class Sphere implements Listener
{
    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        if ($damager instanceof Player && $entity instanceof Player) {
            $item = $damager->getInventory()->getItemInHand();
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Sphere") {
                    $event->cancel();
                    if ($damager->getSession()->getCooldown('ability.Sphere') === null) {
                        if ($damager->getSession()->getCooldown('ability.global') === null) {
                            if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($damager->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $damager->sendMessage("§6You have activated §bSphere");
                            $damager->sendMessage(TextFormat::colorize("&cCooldown active of &l&bSphere &r&cby 2 minute"));
                            $damager->getSession()->addCooldown('ability.Sphere', '§bSphere&r&7: &c', 120);
                            $damager->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                            $item = $damager->getInventory()->getItemInHand();
                            $item->pop();
                            $damager->getInventory()->setItemInHand($item);
                            $damager->sendMessage("§aYou have generated a sphere for the player");
                            $this->generateSphere($entity); // Pasa el jugador atacado a la función generateSphere
                        } else {
                            $damager->sendMessage("§c§6you have cooldown of §dPartner Item §f" . Timer::format($damager->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $damager->sendMessage("§cNo puedes volver a usar la §bSphere§c por §l" . Timer::format($damager->getSession()->getCooldown("ability.Sphere")->getTime()));
                    }
                }
            }
        }
    }
    //Para que sepan como funciona negros
    //Funcion De Generacion
    private function generateSphere(Player $player): void
    {
        $radius = 5; // Radio Para la Esfera
        $duration = 10; // Duracion en  segundos

        $position = $player->getPosition(); //obtiene la posicion del jugador
        $world = $player->getWorld(); //obtiene el mundo del jugador
        $originalBlocks = []; //guarda los bloques originales

        // Genera La Esfera
        for ($x = -$radius; $x <= $radius; $x++) {
            for ($y = -$radius; $y <= $radius; $y++) {
                for ($z = -$radius; $z <= $radius; $z++) {
                    if (sqrt($x * $x + $y * $y + $z * $z) <= $radius && sqrt($x * $x + $y * $y + $z * $z) >= $radius - 1) {
                        $blockPos = $position->add($x, $y, $z);
                        $originalBlock = $world->getBlock($blockPos);
                        $originalBlocks[] = $originalBlock;
                        $world->setBlock($blockPos, VanillaBlocks::GLOWSTONE());
                    }
                }
            }
        }

        // Restablece los Bloques
		HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($originalBlocks, $world): void {
            foreach ($originalBlocks as $block) {
                $world->setBlock($block->getPosition(), $block);
            }
        }), 20 * $duration);
    }
}
