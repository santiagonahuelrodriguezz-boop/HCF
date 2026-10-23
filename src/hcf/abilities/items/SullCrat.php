<?php

namespace hcf\abilities\items;

use hcf\abilities\entity\SullCratEntity;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\VanillaItems;
use pocketmine\world\particle\BlockBreakParticle;

class SullCrat implements Listener {

    public function handleUse(PlayerItemUseEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if(!$player instanceof Player){
            return;
        }

        if($item->getTypeId() === ItemTypeIds::FIRE_CHARGE){
            if(($item->getNamedTag()->getTag("Abilities")) !== null){
                if($item->getNamedTag()->getString("Abilities") === "SullCrat"){
                    $event->cancel();

                    if($player->getSession()->getCooldown('ability.SullCrat') === null){
                        if($player->getSession()->getCooldown('ability.global') === null){
                            if($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null){
                                return;
                            }

                            if($player->getCurrentClaim() === 'Spawn'){
                                $player->sendMessage("§cNo puedes usar §4SullCrat §cen el §eSpawn§c.");
                                return;
                            }

                            // Spawnear entidad correctamente
                            $entity = new SullCratEntity(
                                Location::fromObject(
                                    $player->getEyePos(),
                                    $player->getWorld(),
                                    $player->getLocation()->getYaw(),
                                    $player->getLocation()->getPitch()
                                ),
                                $player
                            );
                            $entity->setMotion($event->getDirectionVector()->multiply(1.5));
                            $entity->spawnToAll();

                            $player->sendMessage("§6Has activado §4SullCrat");
                            $player->getSession()->addCooldown('ability.SullCrat', '§4SullCrat&r§7: &c', 300);
                            $player->getSession()->addCooldown('ability.global', '§5Partner Item&r§7: &c', 5);

                            // Quitar 1 item de la mano
                            if($item->getCount() > 1){
                                $item->setCount($item->getCount() - 1);
                            } else {
                                $item = VanillaItems::AIR();
                            }
                            $player->getInventory()->setItemInHand($item);
                        }
                    }
                }
            }
        }
    }

    public function handleProjectileHit(ProjectileHitBlockEvent $event) : void {
        $block = $event->getBlockHit();
        $entity = $event->getEntity();

        if($entity instanceof SullCratEntity){
            // Obtener el claim donde impacta
            $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

            // Si es en Spawn, no hacer nada
            if($claim !== null && $claim->getName() === 'Spawn'){
                return;
            }

            // Solo destruir si es una puerta de valla
            if($block->getTypeId() === BlockTypeIds::OAK_FENCE_GATE){
                $world = $block->getPosition()->getWorld();
                $world->setBlock($block->getPosition(), VanillaBlocks::AIR());
                $world->addParticle($block->getPosition(), new BlockBreakParticle(VanillaBlocks::OAK_FENCE_GATE()));
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event) : void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if(!$player instanceof Player){
            return;
        }

        if($item->getTypeId() === ItemTypeIds::FIRE_CHARGE){
            if(($item->getNamedTag()->getTag("Abilities")) !== null){
                if($item->getNamedTag()->getString("Abilities") === "SullCrat"){
                    // Cancelar cualquier tipo de interacción para que no prenda fuego
                    $event->cancel();
                }
            }
        }
    }
}