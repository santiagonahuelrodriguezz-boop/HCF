<?php

declare(strict_types=1);

namespace hcf\utils\inventorie;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\item\Items;
use hcf\utils\time\Timer;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;

/**
 * Class Inventories
 * @package hcf\utils
 */
final class Inventories
{

    public static function fillEmptySlotsWithGlass(Inventory $inventory): void
{
    $glass = VanillaBlocks::STAINED_GLASS_PANE();
    $fillItems = [
        $glass->setColor(DyeColor::GRAY())->asItem()->setCustomName(' ')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
        $glass->setColor(DyeColor::CYAN())->asItem()->setCustomName(' ')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
        $glass->setColor(DyeColor::YELLOW())->asItem()->setCustomName(' ')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
        $glass->setColor(DyeColor::RED())->asItem()->setCustomName(' ')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
        $glass->setColor(DyeColor::WHITE())->asItem()->setCustomName(' ')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1)),
        $glass->setColor(DyeColor::PINK())->asItem()->setCustomName(' ')->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1))
    ];
    
    for ($i = 0; $i < $inventory->getSize(); $i++) {
        if ($inventory->getItem($i)->isNull()) {
            $inventory->setItem($i, $fillItems[array_rand($fillItems)]);
        }
    }
}

public static function createKitOrganization(Player $player): void
{
    $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
    $organization = HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getOrganization();
    
    for ($i = 0; $i < 54; $i++) {
        if (isset($organization[$i])) {
            $kit = HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getKit($organization[$i]);
            
            if ($kit !== null) {
                $menu->getInventory()->setItem($i, Items::createItemKitOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
            } else {
                $menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName("§r")->setCount(1)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(),1)));
            }
        } else {
            $menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName("§r")->setCount(1)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(),1)));
        }
    }

    self::fillEmptySlotsWithGlass($menu->getInventory());

    $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
        $player = $transaction->getPlayer();
        $item = $transaction->getItemClicked();
        
        if ($item->getNamedTag()->getTag('kit_name') !== null) {
            $kit = HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getKit($item->getNamedTag()->getString('kit_name'));
            
            if ($kit !== null) {
                
                if ($kit->getPermission() !== null && !$player->hasPermission($kit->getPermission())) {
                    $player->sendMessage(TextFormat::colorize('&cYou do not have permission to use the kit'));
                    return $transaction->discard();
                }
                
                if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                    $player->sendMessage(TextFormat::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                    return $transaction->discard();
                }


                $kit->giveTo($player);

                if ($kit->getCooldown() !== 0)
                    $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                
                $player->removeCurrentWindow();
            }
        }
        return $transaction->discard();
    });
    $menu->send($player, TextFormat::colorize('§l§aKits Free'));
}
public static function createMenuKit(Player $player) {
        $glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem()->setCustomName(" ")->setCount(1)->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));

$menu = InvMenu::create(InvMenu::TYPE_CHEST);

$free = VanillaItems::IRON_SWORD()->setCustomName(TextFormat::colorize("§l§aFree Kits"));
$pay = VanillaItems::DIAMOND_SWORD()->setCustomName(TextFormat::colorize("§l§bPay Kits"));
$specials = VanillaItems::GOLDEN_SWORD()->setCustomName(TextFormat::colorize("§r§eSpecials §eKits"));
        $menu->getInventory()->setContents([
            0 => $glass,
            1 => $glass,
            2 => $glass,
            3 => $glass,
            4 => $glass,
            5 => $glass,
            6 => $glass,
            7 => $glass,
            8 => $glass,
            9 => $glass,
            10 => $glass,
            11 => $free,
            12 => $glass,
            13 => $specials,
            14 => $glass,
            15 => $pay,
            16 => $glass,
            17 => $glass,
            18 => $glass,
            19 => $glass,
            20 => $glass, 
            21 => $glass,
            22 => $glass,
            23 => $glass,
            24 => $glass,
            25 => $glass,
            26 => $glass,
            27 => $glass,
        ]);
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();

            if ($item->getCustomName() === TextFormat::colorize("§l§aFree Kits")) {
                self::createKitOrganization($player);
                $player->removeCurrentWindow();
                return $transaction->discard();
            }
            if ($item->getCustomName() === TextFormat::colorize("§l§bPay Kits")) {
                self::createKitPayOrganization($player);
                $player->removeCurrentWindow();
                return $transaction->discard();
            }
            if ($item->getCustomName() === TextFormat::colorize("§r§eSpecials §eKits")) {
    self::createKitSpecialsOrganization($player);
    $player->removeCurrentWindow();
    return $transaction->discard();
}
  return $transaction->discard();
});
    
        $menu->send($player, '§r§l§cKits Menu');
    }


    public static function createKitPayOrganization(Player $player): void
{
    $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
    $organization = HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->getOrganization();
    
    for ($i = 0; $i < 54; $i++) {
        if (isset($organization[$i])) {
            $kit = HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($organization[$i]);
            
            if ($kit !== null) {
                if (HCFLoader::getInstance()->getTimerManager()->getFreeKits() !== null &&
                    HCFLoader::getInstance()->getTimerManager()->getFreeKits()->isActive()) {
                    if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                        $item = VanillaBlocks::BARRIER()->asItem();
                        $item->setCustomName(TextFormat::colorize('&cLocked Kit'));
                        $item->setLore([
                            TextFormat::colorize('&c• You have kit cooldown.'),
                            TextFormat::colorize('&c• Time remaining: ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime()))
                        ]);
                    } else {
                        $item = Items::createItemKitPayOrganization($player, $kit->getRepresentativeItem(), $kit->getName());
                    }
                } else {
                    if ($kit->getPermission() !== null && !$player->hasPermission($kit->getPermission())) {
                        $item = VanillaBlocks::BARRIER()->asItem();
                        $item->setCustomName(TextFormat::colorize('&cLocked Kit'));
                        $item->setLore([
                            TextFormat::colorize('&c• You do not have permission'),
                            TextFormat::colorize('&c• to unlock this kit.')
                        ]);
                    } else {
                        $item = Items::createItemKitPayOrganization($player, $kit->getRepresentativeItem(), $kit->getName());
                    }
                }
            } else {
                $item = VanillaBlocks::STAINED_GLASS_PANE()
                    ->setColor(DyeColor::RED())
                    ->asItem()
                    ->setCustomName("§r")
                    ->setCount(1)
                    ->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
            }
        } else {
            $item = VanillaBlocks::STAINED_GLASS_PANE()
                ->setColor(DyeColor::RED())
                ->asItem()
                ->setCustomName("§r")
                ->setCount(1)
                ->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 1));
        }

        $menu->getInventory()->setItem($i, $item);
    }

    self::fillEmptySlotsWithGlass($menu->getInventory());

    $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
        $player = $transaction->getPlayer();
        $item = $transaction->getItemClicked();
        
        if ($item->getNamedTag()->getTag('kit_name') !== null) {
            $kit = HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($item->getNamedTag()->getString('kit_name'));

            if ($kit !== null) {
                if (HCFLoader::getInstance()->getTimerManager()->getFreeKits() !== null && 
                    HCFLoader::getInstance()->getTimerManager()->getFreeKits()->isActive()) {
                    if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                        $player->sendMessage(TextFormat::colorize('&cYou have kit cooldown. Time remaining: ' . 
                            Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                        return $transaction->discard();
                    }
                    $kit->giveTo($player);

                    if ($kit->getCooldown() !== 0) {
                        $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    }
                    
                } else {
                    if ($kit->getPermission() !== null && !$player->hasPermission($kit->getPermission())) {
                        $player->sendMessage(TextFormat::colorize('&cYou do not have permission to use this kit'));
                        return $transaction->discard();
                    }

                    if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                        $player->sendMessage(TextFormat::colorize('&cYou have kit cooldown. Time remaining: ' . 
                            Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                        return $transaction->discard();
                    }
                    
                    $kit->giveTo($player);

                    if ($kit->getCooldown() !== 0) {
                        $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    }
                }

                $player->removeCurrentWindow();
            }
        }

        return $transaction->discard();
    });

    $menu->send($player, '§l§bKits Pay');
}
    public static function createKitSpecialsOrganization(Player $player): void
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $organization = HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->getOrganization();
        
        for ($i = 0; $i < 54; $i++) {
            if (isset($organization[$i])) {
                $kit = HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->getKit($organization[$i]);
                
                if ($kit !== null)
                    $menu->getInventory()->setItem($i, Items::createItemKitSpecialsOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
                else $menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN())->asItem()->setCustomName("§r"));
            } else
                $menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN())->asItem()->setCustomName("§r"));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            
            if ($item->getNamedTag()->getTag('kit_name') !== null) {
                $kit = HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->getKit($item->getNamedTag()->getString('kit_name'));
                
                if ($kit !== null) {
                    
                    # Permission
                    if ($kit->getPermission() !== null && !$player->hasPermission($kit->getPermission())) {
                        $player->sendMessage(TextFormat::colorize('&cYou do not have permission to use the kit'));
                        return $transaction->discard();
                    }
                    
                    # Cooldown
                    if ($player->getSession()->getCooldown('kit.' . $kit->getName()) !== null) {
                        $player->sendMessage(TextFormat::colorize('&cYou have kit cooldown. Time remaining ' . Timer::convert($player->getSession()->getCooldown('kit.' . $kit->getName())->getTime())));
                        return $transaction->discard();
                    }

                    # Give kit
                    $kit->giveTo($player);
                    
                    # Add cooldown
                    if ($kit->getCooldown() !== 0)
                        $player->getSession()->addCooldown('kit.' . $kit->getName(), '', $kit->getCooldown(), false, false);
                    
                    $player->removeCurrentWindow();
                }
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&r&7Kits &eSpecials'));
    }
    
    public static function editKitOrganization(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        
        foreach (HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getOrganization() as $slot => $kitName) {
            $kit = HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getKit($kitName);
            
            if ($kit !== null) $menu->getInventory()->setItem($slot, Items::createItemKitOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClickedWith();
            
            if (!$item->isNull() && $item->getNamedTag()->getTag('kit_name') === null)
                return $transaction->discard();
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $data = [];
            $contents = $inventory->getContents();
            
            foreach ($contents as $slot => $item) {
                $kit = HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getKit($item->getNamedTag()->getString('kit_name'));
                
                if ($kit !== null) $data[$slot] = $kit->getName();
            }
            HCFLoader::getInstance()->getHandlerManager()->getKitManager()->setOrganization($data);
        });
        $menu->send($player, '§eEdit kit organization');
    }

    public static function editKitPayOrganization(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        
        foreach (HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->getOrganization() as $slot => $kitName) {
            $kit = HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($kitName);
            
            if ($kit !== null) $menu->getInventory()->setItem($slot, Items::createItemKitPayOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClickedWith();
            
            if (!$item->isNull() && $item->getNamedTag()->getTag('kit_name') === null)
                return $transaction->discard();
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $data = [];
            $contents = $inventory->getContents();
            
            foreach ($contents as $slot => $item) {
                $kit = HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($item->getNamedTag()->getString('kit_name'));
                
                if ($kit !== null) $data[$slot] = $kit->getName();
            }
            HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->setOrganization($data);
        });
        $menu->send($player, '§6Edit kit organization');
    }
 public static function editKitSpecialsOrganization(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        
        foreach (HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->getOrganization() as $slot => $kitName) {
            $kit = HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->getKit($kitName);
            
            if ($kit !== null) $menu->getInventory()->setItem($slot, Items::createItemKitSpecialsOrganization($player, $kit->getRepresentativeItem(), $kit->getName()));
        }
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClickedWith();
            
            if (!$item->isNull() && $item->getNamedTag()->getTag('kit_name') === null)
                return $transaction->discard();
            return $transaction->continue();
        });
        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            $data = [];
            $contents = $inventory->getContents();
            
            foreach ($contents as $slot => $item) {
                $kit = HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->getKit($item->getNamedTag()->getString('kit_name'));
                
                if ($kit !== null) $data[$slot] = $kit->getName();
            }
            HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->setOrganization($data);
        });
        $menu->send($player, TextFormat::colorize('&6Edit kit organization'));
 }
}