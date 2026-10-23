<?php

declare(strict_types=1);

namespace hcf\rollback;

use hcf\utils\serialize\Serialize;
use pocketmine\item\Item;
use pocketmine\player\Player;

class Rollback
{

    /** @var string */
    private string $name;

    /** @var Item[] */
    private array $items, $armor;

    /** @var Item|null */
    private ?Item $representativeItem;

    /**
     * Kit construct.
     * @param string $name
     * @param Item[] $items
     * @param Item[] $armor
     */
    public function __construct(string $name, array $items, array $armor)
    {
        $this->name = $name;
        $this->items = $items;
        $this->armor = $armor;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return Item[]
     */
    public function getArmor(): array
    {
        return $this->armor;
    }

    /**
     * @param Item[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @param Item[] $armor
     */
    public function setArmor(array $armor): void
    {
        $this->armor = $armor;
    }

    /**
     * @param Player $player
     */
    public function giveTo(Player $player): void
    {
        foreach ($this->getItems() as $slot => $item) {
            if ($player->getInventory()->canAddItem($item))
                $player->getInventory()->addItem($item);
            else
                $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
        }

        for ($i = 0; $i < 4; $i++) {
            if (isset($this->armor[$i])) {
                if ($player->getArmorInventory()->getItem($i)->isNull())
                    $player->getArmorInventory()->setItem($i, $this->armor[$i]);
                else {
                    if ($player->getInventory()->canAddItem($this->armor[$i]))
                        $player->getInventory()->addItem($this->armor[$i]);
                    else
                        $player->dropItem($this->armor[$i]);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [
            'player' => $this->getName(),
            'items' => [],
            'armor' => [],
        ];

        foreach ($this->getItems() as $slot => $item)
            $data['items'][$slot] = Serialize::serialize($item);

        foreach ($this->getArmor() as $slot => $armor)
            $data['armor'][$slot] = Serialize::serialize($armor);
        return $data;
    }
}