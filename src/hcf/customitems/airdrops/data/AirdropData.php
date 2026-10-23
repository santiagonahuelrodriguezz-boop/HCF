<?php

namespace hcf\customitems\airdrops\data;

use pocketmine\item\Item;
use pocketmine\utils\Config;
use hcf\HCFLoader;
use hcf\utils\serialize\Serialize;

class AirdropData {
    private static Config $lootConfig;

    public static function init(): void {
        $dataFolder = HCFLoader::getInstance()->getDataFolder();
        self::$lootConfig = new Config($dataFolder . "others/airdrops.yml", Config::YAML);
    }

    public static function saveLoot(array $lootItems): void {
        $items = [];
        foreach ($lootItems as $item) {
            $items[] = Serialize::serialize($item);
        }
        self::$lootConfig->set("loot", $items);
        self::$lootConfig->save();
    }

    public static function getLoot(): array {
        $allItems = self::$lootConfig->get("loot", []);
        $items = [];

        foreach ($allItems as $itemData) {
            $items[] = Serialize::deserialize($itemData);
        }

        return $items;
    }

    public static function getRandomLoot(int $count = 5): array {
        $allItems = self::$lootConfig->get("loot", []);
        shuffle($allItems);
        $selectedItems = array_slice($allItems, 0, $count);

        return array_map(fn($itemData) => Serialize::deserialize($itemData), $selectedItems);
    }

    public static function saveData(): void {
        self::$lootConfig->save();
    }
}