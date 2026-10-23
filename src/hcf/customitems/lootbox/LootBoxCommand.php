<?php

namespace hcf\customitems\lootbox;

use hcf\HCFLoader;
use hcf\customitems\lootbox\Loot;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\item\enchantment\EnchantmentInstance;

use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class LootBoxCommand extends Command {
    
    public function __construct() {
        parent::__construct('lootbox', 'Use command to LootBox');
        $this->setPermission('op.permission');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$this->testPermission($sender)) {
            return;
        }
        
        if (count($args) < 1) {
            $sender->sendMessage('§cUse /lootbox give | giveall | edit');
            return;
        }

        switch (strtolower($args[0])) {
            case 'give':
                if (count($args) < 3) {
                    $sender->sendMessage('§cUse /lootbox give [player] [count]');
                    return;
                }
                $player = Server::getInstance()->getPlayerByPrefix($args[1]);
                
                if (!$player instanceof Player) {
                    $sender->sendMessage('§cInvalid player.');
                    return;
                }
                
                if (!is_numeric($args[2])) {
                    $sender->sendMessage('§cCant is invalid.');
                    return;
                }
                $ppackageItem = VanillaBlocks::CRAFTING_TABLE()->asItem();
                $ppackageItem->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
                $ppackageItem->setCustomName('§r§l§bLOOT BOX');
                $ppackageItem->setCount((int) $args[2]);
                
                 $lore = [
            '',
            '§r§l§b• CONTENT:§r',
            '§r§dStore: §g•> dsc.gg/godrayosmcpe <•'
        ];
                $colors = [
            TextFormat::DARK_GREEN,
            TextFormat::GREEN,
            TextFormat::YELLOW,
            TextFormat::GOLD,
            TextFormat::RED,
            TextFormat::DARK_RED,
            TextFormat::AQUA,
            TextFormat::DARK_AQUA,
            TextFormat::BLUE,
            TextFormat::DARK_BLUE,
            TextFormat::LIGHT_PURPLE,
            TextFormat::DARK_PURPLE,
        ];
                
                $iColor = 0;
                $countItem = 0;
                foreach(Loot::getInstance()->getItems() as $item) {
                    $countItem++;
                    $count = $item->getCount() > 1 ? $item->getCount() . ' ' : '';
                    $lore[] = TextFormat::GRAY . '* ' . $colors[$iColor] . $count . TextFormat::clean($item->getCustomName() !== '' ? $item->getCustomName() : $item->getName());
                    if ($countItem % 3 == 0) {
                $iColor = ($iColor + 1) % count($colors);
                   }
                }
                $l = [];
                
                foreach ($lore as $line) {
            $l[] = TextFormat::RESET . TextFormat::colorize($line);
        }
                $ppackageItem->setLore($l);
                $ppackageItem->getNamedTag()->setInt('lootbox', 1);
                
                if ($player->getInventory()->canAddItem($ppackageItem)) {
                    $player->getInventory()->addItem($ppackageItem);
                } else {
                    $player->dropItem($ppackageItem);
                }
                break;
                
            case 'giveall':
                if (count($args) < 2) {
                    $sender->sendMessage('§cUse /lootbox giveall [cant]');
                    return;
                }

                if (!is_numeric($args[1])) {
                    $sender->sendMessage("§cCant is invalid.");
                    return;
                }
                $ppackageItem = VanillaBlocks::CRAFTING_TABLE()->asItem();
                $ppackageItem->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
                $ppackageItem->setCustomName('§r§l§bLOOT BOX');
                $ppackageItem->setCount((int) $args[1]);
                
                $lore = [
            '',
            '§r§l§b• CONTENT:§r',
            '§r§dStore: §g•> dsc.gg/godrayosmcpe <•'
        ];
                $colors = [
            TextFormat::DARK_GREEN,
            TextFormat::GREEN,
            TextFormat::YELLOW,
            TextFormat::GOLD,
            TextFormat::RED,
            TextFormat::DARK_RED,
            TextFormat::AQUA,
            TextFormat::DARK_AQUA,
            TextFormat::BLUE,
            TextFormat::DARK_BLUE,
            TextFormat::LIGHT_PURPLE,
            TextFormat::DARK_PURPLE,
        ];              

                $iColor = 0;
                $countItem = 0;
                foreach(Loot::getInstance()->getItems() as $item) {
                    $countItem++;
                    $count = $item->getCount() > 1 ? $item->getCount() . ' ' : '';
                    $lore[] = TextFormat::GRAY . '* ' . $colors[$iColor] . $count . TextFormat::clean($item->getCustomName() !== '' ? $item->getCustomName() : $item->getName());
                    if ($countItem % 3 == 0) {
                $iColor = ($iColor + 1) % count($colors);
                   }
                }

                $l = [];              

                foreach ($lore as $line) {
            $l[] = TextFormat::RESET.TextFormat::colorize($line);
        }

                $ppackageItem->setLore($l);
                $ppackageItem->getNamedTag()->setInt('lootbox', 1);
                
                foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                    if ($player->getInventory()->canAddItem($ppackageItem)) {
                        $player->getInventory()->addItem($ppackageItem);
                    } else {
                        $player->dropItem($ppackageItem);
                    }
                }
                break;
                
            case 'edit':
                if (!$sender instanceof Player) {
                    return;
                }
                Loot::getInstance()->sendMenu($sender);
                break;
        }
    }
}