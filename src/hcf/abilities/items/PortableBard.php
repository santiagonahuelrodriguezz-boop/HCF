<?php

namespace hcf\abilities\items;

use JetBrains\PhpStorm\Pure;
use hcf\abilities\entity\PortableBardEntity;
use hcf\HCFLoader;
use hcf\item\EnderpearlItem;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\world\Position;

class PortableBard implements Listener
{

    public function interactPortable(PlayerInteractEvent $event)
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player)
        if ($item->getNamedTag()->getTag("Abilities") !== null) {
            if ($item->getNamedTag()->getString("Abilities") === "PortableBard") {
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
                if ($item->getNamedTag()->getString("Abilities") === "PortableBard") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.portablebard') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $bard = new PortableBardEntity($event->getPlayer()->getLocation());
                            $bard->setOwner($event->getPlayer());
                            $bard->setPos($event->getPlayer()->getLocation());
                            $bard->spawnToAll();
                            HCFLoader::$bard_allow[$event->getPlayer()->getName()] = true;
                            $player->sendMessage("§6You have activated §dPortable Bard");
                            $player->getSession()->addCooldown('ability.portablebard', '&f| &dPortable Bard&r&7: &c', 240);
                            $player->getSession()->addCooldown('ability.global', '&f| &5Partner Item&r&7: &c', 5);

                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't Ninja Star in the last " . Timer::format($player->getSession()->getCooldown("ability.portablebard")->getTime()));
                    }
                }
            }
    }

    public function Interact(PlayerInteractEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($item->getNamedTag()->getTag("Abilities") !== null) {
            if ($item->getNamedTag()->getString("Abilities") === "PortableBard") {
                $event->cancel();
            }
        }
    }
    #[Pure] public static function isAllow(Player $player): bool
    {
        if(!isset(HCFLoader::$bard_allow[$player->getName()])){
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