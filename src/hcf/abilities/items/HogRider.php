<?php

namespace hcf\abilities\items;

use pocketmine\event\player\PlayerInteractEvent;
use JetBrains\PhpStorm\Pure;
use hcf\HCFLoader;
use hcf\abilities\entity\HogRiderEntityOne;
use hcf\abilities\entity\HogRiderEntityTwo;
use hcf\abilities\entity\HogRiderEntityThree;
use hcf\player\Player;
use hcf\utils\Utils;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class HogRider implements Listener
{
    
    public function interactPortable(PlayerInteractEvent $event)
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
        if ($item->getNamedTag()->getTag("Abilities") !== null) {
            if ($item->getNamedTag()->getString("Abilities") === "HogRider") {
                $event->cancel();
            }
        }
    }
    
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "HogRider") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.HogRider') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $bard1 = new HogRiderEntityOne($event->getPlayer()->getLocation());
                            //$bard1 = new HogRiderEntityOne(Location::fromObject($player->getLocation()->add(rand(0, 3), 0, rand(0, 3)), $player->getWorld(), $player->getLocation()->yaw, $player->getLocation()->pitch), null, $player);
                            $bard1->setOwner($event->getPlayer());
                            $bard1->setScale(0.45);
                            
                            $bard2 = new HogRiderEntityTwo(Location::fromObject($player->getLocation()->add(rand(0, 3), 0, rand(0, 3)), $player->getWorld(), $player->getLocation()->yaw, $player->getLocation()->pitch), null, $player);
                            $bard2->setOwner($event->getPlayer());
                            $bard2->setScale(0.45);
                            

                            $bard3 = new HogRiderEntityThree(Location::fromObject($player->getLocation()->add(rand(0, 3), 0, rand(0, 3)), $player->getWorld(), $player->getLocation()->yaw, $player->getLocation()->pitch), null, $player);
                            $bard3->setOwner($event->getPlayer());
                            $bard3->setScale(0.45);        

                            $bard1->spawnToAll();
                            $bard2->spawnToAll();
                            $bard3->spawnToAll();
                            
                            HCFLoader::$bard_allow[$event->getPlayer()->getName()] = true;
                            Utils::PlaySound($player, "random.orb", 1, 1);
                            $player->sendMessage("§6You have activated §bHogRider");
                            $player->getSession()->addCooldown('ability.HogRider', '§bHogRider§7: §f', 120);
                            $player->getSession()->addCooldown('ability.global', '§5Partner Item§7: §f', 5);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't HogRider in the last " . Timer::format($player->getSession()->getCooldown("ability.HogRider")->getTime()));
                    }
                }
            }
    }
    
    public function Interact(PlayerInteractEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($item->getNamedTag()->getTag("Abilities") !== null) {
            if ($item->getNamedTag()->getString("Abilities") === "HogRider") {
                $event->cancel();
            }
        }
    }

    #[Pure] public static function isAllow(Player $player): bool
    { if(!isset(HCFLoader::$bard_allow[$player->getName()])){
            return false;
        }
        return true;
    }

    public static function setAllow(Player $player){
 HCFLoader::$bard_allow[$player->getName()] = true;
    }

    public static function removeAllow(Player $player){
 unset(HCFLoader::$bard_allow[$player->getName()]);
    }
}