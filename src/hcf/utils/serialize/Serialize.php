<?php

namespace hcf\utils\serialize;

use pocketmine\item\Item;

use pocketmine\item\StringToItemParser;

class Serialize {

    public static function getItem(string $item): Item {

        return StringToItemParser::getInstance()->parse($item);

    }

    public static function serialize(?Item $item): string {

        if ($item === null) return "";

        $itemToJson = self::itemToJson($item);

        return base64_encode(gzcompress($itemToJson));

    }

    public static function deserialize(string $item): ?Item {

        if (empty($item)) return null;

        try {

            $decoded = base64_decode($item, true);

            if ($decoded === false) return null;

            $itemFromJson = gzuncompress($decoded);

            return self::jsonToItem($itemFromJson);

        } catch (\Throwable) {

            return null;

        }

    }

    public static function itemToJson(Item $item): string {

        $cloneItem = clone $item;

        $itemNBT = $cloneItem->nbtSerialize();

        return base64_encode(serialize($itemNBT));

    }

    public static function jsonToItem(string $json): Item {

        $itemNBT = unserialize(base64_decode($json));

        return Item::nbtDeserialize($itemNBT);

    }

}