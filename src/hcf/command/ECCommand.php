<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\HCFLoader;
use hcf\player\Player as HCFPlayer;
use hcf\utils\serialize\Serialize;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class ECCommand extends Command
{
    public function __construct()
    {
        parent::__construct('ec', 'Command for Ender Chest');
        $this->setPermission("player.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof HCFPlayer) return;
        
        $hasRank = $sender->hasPermission("ec.rank");

        $menuType = $hasRank ? InvMenuTypeIds::TYPE_DOUBLE_CHEST : InvMenuTypeIds::TYPE_CHEST;
        $menu = InvMenu::create($menuType);

        if ($hasRank && $this->hasEnderChest($sender)) {
            $enderChestData = $this->getEnderChest($sender);
            if (isset($enderChestData["items"]) && count($enderChestData["items"]) > 0) {
                $items = [];
                foreach ($enderChestData["items"] as $slot => $item) {
                    $items[$slot] = $item;
                }
                $menu->getInventory()->setContents($items);
            } else {
                $menu->getInventory()->setContents([]);
            }
        } else {
            $menu->getInventory()->setContents($sender->getEnderInventory()->getContents());
        }

        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($hasRank): void {
            if ($player instanceof HCFPlayer) {
                if ($hasRank) {
                    $items = [];
                    foreach ($inventory->getContents() as $slot => $item) {
                        $items["items"][$slot] = Serialize::serialize($item);
                    }

                    $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'privateVaults' . DIRECTORY_SEPARATOR . strtolower($player->getName()) . '.yml', Config::YAML);
                    $config->setAll($items);
                    $config->save();
                } else {
                    $player->getEnderInventory()->setContents($inventory->getContents());
                }
            }
        });
        $menu->send($sender, TextFormat::colorize('&4Ender Chest'));
    }

    public function getEnderChest(HCFPlayer $player): array
    {
        $enderChests = HCFLoader::getInstance()->getProvider()->getEnderChests();
        $name = strtolower($player->getName());

        if (isset($enderChests[$name]) && isset($enderChests[$name]['items'])) {
            foreach ($enderChests[$name]['items'] as $slot => $item) {
                $enderChests[$name]['items'][$slot] = Serialize::deserialize($item);
            }
            return $enderChests[$name];
        }
        
        if ($player->hasPermission("ec.rank")) {
            $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'privateVaults' . DIRECTORY_SEPARATOR . $name . '.yml', Config::YAML);
            $config->set("items", []);
            $config->save();
        }

        return [];
    }

    public function hasEnderChest(HCFPlayer $player): bool
    {
        $name = strtolower($player->getName());
        $enderChests = HCFLoader::getInstance()->getProvider()->getEnderChests();

        return isset($enderChests[$name]) || $player->hasPermission("ec.rank");
    }
}