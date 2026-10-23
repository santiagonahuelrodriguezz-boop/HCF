<?php

namespace hcf\handler\package\commands;

use hcf\HCFLoader;
use hcf\handler\package\util\Content;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class PartnerPackagesCommand extends Command {
    
    public function __construct() {
        parent::__construct('pkg', 'Use command to partner packages.', 'ppk');
        $this->setPermission('ppackage.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$this->testPermission($sender)) {
            return;
        }
        
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&aUse /pkg give | giveall | edit'));
            return;
        }

        switch (strtolower($args[0])) {
            case 'give':
                if (count($args) < 3) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /pkg give [player] [count]'));
                    return;
                }
                $player = Server::getInstance()->getPlayerByPrefix($args[1]);
                
                if (!$player instanceof Player) {
                    $sender->sendMessage(TextFormat::colorize('&cInvalid player.'));
                    return;
                }
                
                if (!is_numeric($args[2])) {
                    $sender->sendMessage(TextFormat::colorize('&cCant is invalid.'));
                    return;
                }
                $ppackageItem = VanillaBlocks::ENDER_CHEST()->asItem();
                $ppackageItem->setCustomName(TextFormat::colorize('§r§k§55&r&l&bPartner Package§r§k§55§r'));
                $ppackageItem->setCount((int) $args[2]);

                $lore = [
            '&eUnlock at &gTicket',
            ' ',
            '&e&lCONTENT'
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
                foreach(Content::getInstance()->getItems() as $item) {
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
                $ppackageItem->getNamedTag()->setInt('ppackage', 1);
                
                if ($player->getInventory()->canAddItem($ppackageItem)) {
                    $player->getInventory()->addItem($ppackageItem);
                } else {
                    $player->dropItem($ppackageItem);
                }
                break;
                
            case 'giveall':
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /ppackage giveall [cant]'));
                    return;
                }

                if (!is_numeric($args[1])) {
                    $sender->sendMessage(TextFormat::colorize('&cCant is invalid.'));
                    return;
                }
                $ppackageItem = VanillaBlocks::ENDER_CHEST()->asItem();
                $ppackageItem->setCustomName(TextFormat::colorize('§r§k§55&r&l&bPartner Package§r§k§55§r'));
                $ppackageItem->setCount((int) $args[1]);
                
                $lore = [
            '&eUnlock at &gTicket',
            ' ',
            '&e&lCONTENT'
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
                foreach(Content::getInstance()->getItems() as $item) {
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
                $ppackageItem->getNamedTag()->setInt('ppackage', 1);
                
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
                Content::getInstance()->sendMenu($sender);
                break;
        }
    }
}