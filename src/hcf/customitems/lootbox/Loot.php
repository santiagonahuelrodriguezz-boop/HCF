<?php

declare(strict_types=1);

namespace hcf\customitems\lootbox;

use hcf\utils\serialize\Serialize;
use hcf\HCFLoader;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

final class Loot {
    use SingletonTrait;
    
    /** @var Item[] */
    private array $items = [];

    /**
     * Devuelve solo ítems válidos, nunca null.
     *
     * @return Item[]
     */
    public function getItems(): array {
        return array_filter($this->items, fn($item) => $item instanceof Item);
    }

    /**
     * Carga los ítems desde el archivo JSON,
     * descartando cualquier valor corrupto o inválido.
     */
    public function load(): void {
        $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'others/lootbox.json', Config::JSON);

        $this->items = [];
        foreach ($config->getAll() as $data) {
            $item = Serialize::deserialize($data);
            if ($item instanceof Item) {
                $this->items[] = $item;
            }
        }
    }

    /**
     * Guarda solo ítems válidos en el archivo JSON.
     */
    public function save(): void {
        $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'others/lootbox.json', Config::JSON);

        $validItems = array_filter($this->items, fn($item) => $item instanceof Item);
        $config->setAll(array_map(fn(Item $item) => Serialize::serialize($item), $validItems));
        $config->save();
    }

    /**
     * Muestra el menú para editar el loot.
     */
    public function sendMenu(Player $player): void {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents($this->getItems());

        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            // Guardar solo ítems válidos
            $this->items = array_filter($inventory->getContents(), fn($item) => $item instanceof Item);
            $player->sendMessage("§aYou have edited the loot");
            $this->save();
        });

        $menu->send($player, "§bLoot Box");
    }
}