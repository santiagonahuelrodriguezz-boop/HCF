<?php

namespace hcf\faction\subclaim;

use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\block\tile\Chest as TileChest;
use pocketmine\block\tile\Sign;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;

class Chest implements Listener
{

    public function Interact(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if (!$player instanceof Player) return;
        if (!$block instanceof \pocketmine\block\Chest) {
            return;
        }
        if (HCFLoader::getInstance()->getTimerManager()->getPurge()->isActive()) return;
        if ($this->isSubClaim($player, $block) && !$this->isAllowSubClaim($player, $block)) {
            $player->sendPopup(TextFormat::colorize("&cYou don`t have permission to open this chest"));
            $event->cancel();
        }
    }

    public function isSubClaim(Player $player, \pocketmine\block\Chest $chest): bool {
        $mgr = HCFLoader::getInstance()->getClaimManager();
        $world = $chest->getPosition()->getWorld();
        $pos = $chest->getPosition();
        $tile = $world->getTileAt($pos->x, $pos->y, $pos->z);
        if (HCFLoader::getInstance()->getTimerManager()->getPurge()->isActive()) return false;
        if (!$tile instanceof TileChest) {
            return false;
        }
        if ($mgr->insideClaim($chest->getPosition())) {

            $sings = [];
            $sings[] = $world->getTileAt($pos->x + 1, $pos->y, $pos->z);
            $sings[] = $world->getTileAt($pos->x - 1, $pos->y, $pos->z);
            $sings[] = $world->getTileAt($pos->x, $pos->y, $pos->z + 1);
            $sings[] = $world->getTileAt($pos->x, $pos->y, $pos->z - 1);
            if($tile->isPaired() && $tile->getPair() instanceof TileChest){
                $sings[] = $world->getTileAt($tile->getPair()->getPosition()->x + 1, $pos->y, $tile->getPair()->getPosition()->z);
                $sings[] = $world->getTileAt($tile->getPair()->getPosition()->x - 1, $pos->y, $tile->getPair()->getPosition()->z);
                $sings[] = $world->getTileAt($tile->getPair()->getPosition()->x, $pos->y, $tile->getPair()->getPosition()->z + 1);
                $sings[] = $world->getTileAt($tile->getPair()->getPosition()->x, $pos->y, $tile->getPair()->getPosition()->z - 1);
            }

            foreach ($sings as $sing) {
                if ($sing instanceof Sign) {
                    $chests = [];
                    $chests[] = $world->getBlockAt($sing->getPosition()->x, $sing->getPosition()->y, $sing->getPosition()->z - 1);
                    $chests[] = $world->getBlockAt($sing->getPosition()->x, $sing->getPosition()->y, $sing->getPosition()->z + 1);
                    $chests[] = $world->getBlockAt($sing->getPosition()->x - 1, $sing->getPosition()->y, $sing->getPosition()->z);
                    $chests[] = $world->getBlockAt($sing->getPosition()->x + 1, $sing->getPosition()->y, $sing->getPosition()->z);
                    foreach ($chests as $chest) {
                        if ($chest instanceof \pocketmine\block\Chest) {
                            if ($chest->getPosition()->x === $pos->x && $chest->getPosition()->y === $pos->y && $chest->getPosition()->z === $pos->z) {
                                if ($sing->getText()->getLine(0) === "§6[§eSubClaim§6]") {
                                    return true;
                                }
                            }
                            if ($tile->isPaired() && $tile->getPair() instanceof TileChest && $chest->getPosition()->x === $tile->getPair()->getPosition()->x && $chest->getPosition()->y === $tile->getPair()->getPosition()->y && $chest->getPosition()->z === $tile->getPair()->getPosition()->z){
                                if($sing->getText()->getLine(0) === "§6[§eSubClaim§6]") {
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    public function isAllowSubClaim(Player $player, \pocketmine\block\Chest $chest):bool{
        $world = $chest->getPosition()->getWorld();
        $pos = $chest->getPosition();
        $tile = $world->getTileAt($pos->x, $pos->y, $pos->z);
        if(!$tile instanceof TileChest) return false;
        if(!$this->isSubClaim($player, $chest)) return false;

        $sings = [];
        $chest = null;
        $sings[] = $world->getTileAt($pos->x + 1, $pos->y, $pos->z);
        $sings[] = $world->getTileAt($pos->x - 1, $pos->y, $pos->z);
        $sings[] = $world->getTileAt($pos->x, $pos->y, $pos->z + 1);
        $sings[] = $world->getTileAt($pos->x, $pos->y, $pos->z - 1);
        if($tile->getPair() instanceof TileChest){
            $sings[] = $world->getTileAt($tile->getPair()->getPosition()->x + 1, $pos->y, $tile->getPair()->getPosition()->z);
            $sings[] = $world->getTileAt($tile->getPair()->getPosition()->x - 1, $pos->y, $tile->getPair()->getPosition()->z);
            $sings[] = $world->getTileAt($tile->getPair()->getPosition()->x, $pos->y, $tile->getPair()->getPosition()->z + 1);
            $sings[] = $world->getTileAt($tile->getPair()->getPosition()->x, $pos->y, $tile->getPair()->getPosition()->z - 1);
        }
        foreach($sings as $sing){
            if($sing instanceof Sign){
                $chests = [];
                $chests[] = $world->getBlockAt($sing->getPosition()->x, $sing->getPosition()->y, $sing->getPosition()->z - 1);
                $chests[] = $world->getBlockAt($sing->getPosition()->x, $sing->getPosition()->y, $sing->getPosition()->z + 1);
                $chests[] = $world->getBlockAt($sing->getPosition()->x - 1, $sing->getPosition()->y, $sing->getPosition()->z);
                $chests[] = $world->getBlockAt($sing->getPosition()->x + 1, $sing->getPosition()->y, $sing->getPosition()->z);

                foreach ($chests as $chest) {
                    if ($chest instanceof \pocketmine\block\Chest) {
                        if ($chest->getPosition()->x === $pos->x && $chest->getPosition()->y === $pos->y && $chest->getPosition()->z === $pos->z) {
                            if($sing->getText()->getLine(1) === "'{$player->getName()}'") {
                                return true;
                            }
                        }
                        if ($tile->isPaired() && $tile->getPair() instanceof TileChest && $chest->getPosition()->x === $tile->getPair()->getPosition()->x && $chest->getPosition()->y === $tile->getPair()->getPosition()->y && $chest->getPosition()->z === $tile->getPair()->getPosition()->z) {
                            if($sing->getText()->getLine(1) === "'{$player->getName()}'") {
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}