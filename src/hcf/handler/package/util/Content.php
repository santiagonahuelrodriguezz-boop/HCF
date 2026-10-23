<?php

declare(strict_types=1);

namespace hcf\handler\package\util;

use hcf\utils\serialize\Serialize;
use hcf\HCFLoader;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

final class Content {
    use SingletonTrait;

    /** @var Item[] */
    private array $items = [];

    public function getItems(): array {
        return $this->items;
    }

    public function load(): void {
        $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'content.json', Config::JSON);
        $raw = $config->getAll();

        $this->items = [];
        foreach($raw as $data){
            $item = Serialize::deserialize($data);
            if($item instanceof Item){
                $this->items[] = $item;
            }
        }
    }

    public function save(): void {
        $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'content.json', Config::JSON);
        $serialized = [];
        foreach($this->items as $item){
            if($item instanceof Item){
                $serialized[] = Serialize::serialize($item);
            }
        }
        $config->setAll($serialized);
        $config->save();
    }

    public function sendMenu(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_CHEST);
        $menu->getInventory()->setContents($this->items);

        $menu->setInventoryCloseListener(function(Player $player, Inventory $inventory): void {
            $this->items = [];
            foreach($inventory->getContents() as $item){
                if($item instanceof Item && !$item->isNull()){
                    $this->items[] = $item;
                }
            }
            $this->save();
            $player->sendMessage(TextFormat::colorize("&aHas editado el loot y se guardó correctamente."));
        });

        $menu->send($player, TextFormat::colorize("&ePartner Packages Loot"));
    }

    public function getRandomItem() : ?Item {
        if(empty($this->items)){
            return null; // Si no hay loot, no da nada
        }
        $index = array_rand($this->items);
        $item = $this->items[$index];
        return clone $item; // devolvemos una copia
    }
}