<?php
    
namespace hcf\handler\shop;

use hcf\handler\shop\menu\Build;
use hcf\HCFLoader;
use muqsit\invmenu\InvMenu;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use hcf\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\XpLevelUpSound;

class ShopMenu {
    public static function MenuShop(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("§l§aSHOP§r");
        $enc = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(1));
        
        $glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem();
        $glass->setCustomName(" ");
        $glass->addEnchantment($enc);
        $build = VanillaBlocks::ACACIA_LOG()->asItem();
        $build->setCustomName("§l§gBuild§r");

        $sell = VanillaBlocks::EMERALD_ORE()->asItem();
        $sell->setCustomName("§l§aSell All§r");
         
        for ($i = 0; $i < 27; $i++) {
            $menu->getInventory()->setItem($i, $glass);
        }

        $menu->getInventory()->setItem(10, $sell);
        $menu->getInventory()->setItem(16, $build);
        
        $menu->setListener(function (InvMenuTransaction $transaction) : InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $itemClicked = $transaction->getItemClicked();

            if ($itemClicked->getCustomName() === TextFormat::colorize("§l§gBuild§r")) {
                $player->getWorld()->addSound($player->getPosition(), new XpLevelUpSound(100));
                Build::MenuBuild($player);
                $player->removeCurrentWindow();
            }
            
            if ($itemClicked->getCustomName() === TextFormat::colorize("§l§cPotions§r")) {
                $player->getWorld()->addSound($player->getPosition(), new XpLevelUpSound(100));
                Potions::MenuPots($player);
                $player->removeCurrentWindow();
            }
            
            if ($itemClicked->getCustomName() === TextFormat::colorize("§l§aSell All§r")) {
                $player->getWorld()->addSound($player->getPosition(), new XpLevelUpSound(100));

                foreach ($player->getInventory()->getContents() as $item) {
                    $price = 0;
                    if ($item->equals(VanillaBlocks::GOLD()->asItem())) {
                        $price = 20 * $item->getCount();
                    } elseif ($item->equals(VanillaBlocks::IRON()->asItem())) {
                        $price = 15 * $item->getCount();
                    } elseif ($item->equals(VanillaBlocks::DIAMOND()->asItem())) {
                        $price = 30 * $item->getCount();
                    } elseif ($item->equals(VanillaBlocks::EMERALD()->asItem())) {
                        $price = 35 * $item->getCount();
                    } elseif ($item->equals(VanillaBlocks::COAL()->asItem())) {
                        $price = 35 * $item->getCount();
                    } 
                    
                    if ($price > 0) {
                        $player->getInventory()->removeItem($item);
                        $session = HCFLoader::getInstance()->getSessionManager()->getSession((string)$player->getUniqueId());
                        $session->setBalance($session->getBalance() + $price);
                    }
                }

                $session = HCFLoader::getInstance()->getSessionManager()->getSession((string)$player->getUniqueId());
                $player->sendMessage('§a» §7You have sold all the minerals in your inventory, new balance: §b' . $session->getBalance()); 
            }

            return $transaction->discard();
        });

        $menu->send($player);
    }
}