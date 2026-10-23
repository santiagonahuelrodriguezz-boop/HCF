<?php

namespace hcf\customitems\pouch;

use pocketmine\item\ItemFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\world\sound\EnderChestOpenSound;
use hcf\HCFLoader;
use hcf\kohi\Kohi;

class PouchTier implements Listener {
    private array $balanceValues = [50, 100, 300, 400, 500, 5, 700, 25, 10, 1000, 5, 1, 30, 2, 600];

    public function givePouch(Player $player, int $amount = 1): void {
        $pouch = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($amount);
        $pouch->setCustomName("§aPouch §7Wealth");
        $pouch->setLore([
   "§r§8--+-------------------------+--",
   "§r§7Use this pouch to receive random §awealth", 
   "§r§8--+-------------------------+--",
   "§r§dStore: §g•> ".Kohi::TEBEX." <•"]);
        $pouch->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
        $player->getInventory()->addItem($pouch);
        $player->sendMessage("§aYou have received a Pouch of Wealth!");
    }

    public function onItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getCustomName() !== "§aPouch §7Wealth") {
            return;
        }

        $item->setCount($item->getCount() - 1);
        $player->getInventory()->setItemInHand($item);
        
        $player->getWorld()->addSound($player->getPosition(), new EnderChestOpenSound());
        
        $randomBalance = $this->balanceValues[array_rand($this->balanceValues)];

        $session = HCFLoader::getInstance()->getSessionManager()->getSession($player->getUniqueId());
        if ($session !== null) {
            $currentBalance = $session->getBalance();
            $session->setBalance($currentBalance + $randomBalance);
            $player->sendTitle("§q$" . $randomBalance);
            
        }
    }

    public function onBlockInteract(PlayerInteractEvent $event): void {
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($item->getCustomName() !== "§aPouch §7Wealth") {
            return;
        }

        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $event->cancel();
        }
    }
}