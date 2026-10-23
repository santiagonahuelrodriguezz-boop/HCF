<?php

namespace hcf\customitems\lootbox;

use hcf\customitems\lootbox\Loot;
use hcf\HCFLoader;
use hcf\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\sound\BarrelOpenSound;
use pocketmine\world\sound\PotionSplashSound;

class LootBoxListener implements Listener {
    private HCFLoader $plugin;

    public function __construct(HCFLoader $plugin) {
        $this->plugin = $plugin;
    }

    public function onItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();
        
        if ($item->getCustomName() !== "§r§l§bLOOT BOX") {
            return;
        }

        $item->pop();
        $player->getInventory()->setItemInHand($item);
        $player->getWorld()->addSound($player->getPosition(), new BarrelOpenSound());

        $items = Loot::getInstance()->getItems();
        if (empty($items)) {
            $player->sendMessage("§cError: No items found in the LootBox.");
            return;
        }

        $this->startRouletteAnimation($player, $items);
    }

    private function startRouletteAnimation(Player $player, array $items): void {
        $frames = ["§7▓ ▓ ▓ ▓ §b█", "§7▓ ▓ ▓ §b█ §7▓", "§7▓ ▓ §b█ §7▓ ▓", "§7▓ §b█ §7▓ ▓ ▓", "§b█ §7▓ ▓ ▓ ▓"];
        $totalFrames = count($frames);
        $rotationCount = 10;
        $currentFrame = 0;


        $taskHandler = null;
        $taskHandler = $this->plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() use ($player, $frames, $totalFrames, &$currentFrame, &$rotationCount, $items, &$taskHandler) {
            if ($rotationCount > 0) {
               
                $randomItem = $items[array_rand($items)];
                $itemName = $randomItem->getCustomName() ?? $randomItem->getName();

                
                $player->sendTitle($frames[$currentFrame], "$itemName", 0, 5, 0);
                Utils::PlaySound($player, "random.orb", 1, 1);       
                $currentFrame = ($currentFrame + 1) % $totalFrames;
                $rotationCount--;
            } else {
                
                $player->sendTitle("§b█ █ █ █ █", "", 0, 10, 0);
                $player->getWorld()->addSound($player->getPosition(), new PotionSplashSound());
                $this->giveRandomItem($player, $items);
                
                
                if ($taskHandler !== null) {
                    $taskHandler->cancel();
                }
            }
        }), 6);
    }

    private function giveRandomItem(Player $player, array $items): void {
        $randomItem = $items[array_rand($items)];
        $itemName = $randomItem->getCustomName() ?? $randomItem->getName();

        if ($player->getInventory()->canAddItem($randomItem)) {
            $player->getInventory()->addItem($randomItem);
            $player->sendMessage("§bYou have received §f" . $itemName);
        } else {
            $player->sendTitle("§cInventory full");
            $player->dropItem($randomItem);
        }
    }

    public function onBlockInteract(PlayerInteractEvent $event): void {
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($item->getCustomName() !== "§r§l§bLOOT BOX") {
            return;
        }

        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $event->cancel();
        }
    }
}