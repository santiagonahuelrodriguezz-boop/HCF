<?php

namespace hcf\handler\shop\menu;

use hcf\HCFLoader;
use pocketmine\block\utils\DyeColor;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\TextFormat;
use hcf\player\Player;

class Build {

    private static function createBlockItem(string $name, string $color, int $price, \pocketmine\block\Block $block): \pocketmine\item\Item {
        $item = $block->asItem()->setCount(16);
        $item->setCustomName(TextFormat::colorize("§l{$color}{$name}§r"));
        $item->setLore([TextFormat::colorize("&6Price&f:&e $price")]);
        return $item;
    }

    private static function processTransaction(Player $player, \pocketmine\block\Block $block, int $price, string $blockName): bool {
        $session = HCFLoader::getInstance()->getSessionManager()->getSession((string)$player->getUniqueId());
        $balance = $session->getBalance();

        if ($balance < $price) {
            $player->sendMessage(TextFormat::colorize("&cNo tienes suficiente dinero."));
            return false;
        }

       
        $session->setBalance($balance - $price);
        $player->getInventory()->addItem($block->asItem()->setCount(16)); 
        $player->sendMessage("§a» §7Has comprado §b{$blockName} §7por §c{$price} §7de balance.");
        return true;
    }

    public static function MenuBuild(Player $player): void {

        $blocksData = [
            ['name' => 'Wood Planks', 'color' => '§6', 'block' => VanillaBlocks::OAK_PLANKS(), 'price' => 800],
            ['name' => 'Logs', 'color' => '§7', 'block' => VanillaBlocks::OAK_LOG(), 'price' => 1000],
            ['name' => 'Stone', 'color' => '§8', 'block' => VanillaBlocks::STONE(), 'price' => 800],
            ['name' => 'Stone Bricks', 'color' => '§b', 'block' => VanillaBlocks::STONE_BRICKS(), 'price' => 800],
            ['name' => 'Glass', 'color' => '§f', 'block' => VanillaBlocks::GLASS(), 'price' => 800],
            ['name' => 'White Wool', 'color' => '§f', 'block' => VanillaBlocks::WOOL()->setColor(DyeColor::WHITE()), 'price' => 800],
            ['name' => 'Black Wool', 'color' => '§0', 'block' => VanillaBlocks::WOOL()->setColor(DyeColor::BLACK()), 'price' => 800], 
            ['name' => 'Red Wool', 'color' => '§c', 'block' => VanillaBlocks::WOOL()->setColor(DyeColor::RED()), 'price' => 800], 
            ['name' => 'Bricks', 'color' => '§c', 'block' => VanillaBlocks::BRICKS(), 'price' => 800],
            ['name' => 'Cobblestone', 'color' => '§7', 'block' => VanillaBlocks::COBBLESTONE(), 'price' => 800]
        ];

        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName(TextFormat::colorize("§l§bTienda de Bloques§r"));
        
        $enc = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(1));
        $glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIGHT_BLUE())->asItem();
        $glass->setCustomName(" ");
        $glass->addEnchantment($enc);
        for ($i = 0; $i < 53; $i++) {
            $menu->getInventory()->setItem($i, $glass);
        }

        $slot = 11;
        foreach ($blocksData as $block) {
            $menu->getInventory()->setItem($slot, self::createBlockItem($block['name'], $block['color'], $block['price'], $block['block']));
            $slot++;
        }

        $menu->setListener(function (InvMenuTransaction $transaction) use ($blocksData): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $itemClicked = $transaction->getItemClicked();

            foreach ($blocksData as $block) {
            
                $blockNameFormatted = TextFormat::colorize("§l{$block['color']}{$block['name']}§r");
                if ($itemClicked->getCustomName() === $blockNameFormatted) {
                    if (self::processTransaction($player, $block['block'], $block['price'], $block['name'])) {
                        return $transaction->discard();
                    }
                }
            }

            return $transaction->discard();
        });

        $menu->send($player);
    }
}