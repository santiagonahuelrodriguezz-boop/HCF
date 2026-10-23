<?php

namespace hcf\vcs\data;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use hcf\HCFLoader;
use hcf\vcs\data\Serializer;
use pocketmine\utils\Filesystem;

class Vouchers
{
    private static Config $voucherConfig;

    public static function init(): void
    {
        $dataFolder = HCFLoader::getInstance()->getDataFolder();
        $othersFolder = $dataFolder . "others/";
        
        if (!is_dir($othersFolder)) {
            mkdir($othersFolder, 0777, true); 
        }
        
        self::$voucherConfig = new Config($othersFolder . "vouchers.yml", Config::YAML);
    }

    private static function itemToArray(Item $item): array
    {
        return [
            'id' => $item->getTypeId(),
            'count' => $item->getCount(),
            'nbt' => Serializer::encodeItem($item), 
        ];
    }

    private static function arrayToItem(array $data): Item
    {
        $item = Serializer::decodeItem($data['nbt']);
        $item->setCount($data['count']);
        return $item;
    }

    public static function saveVoucherItems(Player $player, string $voucherName): void
    {
        $items = [];
        foreach ($player->getInventory()->getContents() as $slot => $item) {
            $items[$slot] = self::itemToArray($item);
        }
        foreach ($player->getArmorInventory()->getContents() as $slot => $item) {
            $items["armor_" . $slot] = self::itemToArray($item);
        }
        self::$voucherConfig->set($voucherName, $items);
        self::$voucherConfig->save();
    }

    public static function giveVoucherItems(Player $player, string $voucherName): void
    {
        if (!self::$voucherConfig->exists($voucherName)) {
            $player->sendMessage("§cNo items configured for this voucher.");
            return;
        }

        $items = self::$voucherConfig->get($voucherName);
        foreach ($items as $slot => $itemData) {
            $item = self::arrayToItem($itemData);
            if (strpos($slot, "armor_") === 0) {
                $player->getArmorInventory()->addItem($item);
            } else {
                $player->getInventory()->addItem($item);
            }
        }
    }
}