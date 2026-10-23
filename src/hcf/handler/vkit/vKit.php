<?php

declare(strict_types=1);

namespace hcf\handler\vkit;

use hcf\handler\vkit\task\vKitTask;
use hcf\HCFLoader;
use hcf\pm5\ItemSerializer;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\item\VanillaItems;

/**
 * Class vKit
 * @package juqn\hcf\vkit
 */
class vKit
{
    
    /** @var string */
    private string $name;
    /** @var Item[] */
    private array $items;
    
    /**
     * vKit construct.
     * @param string $name
     * @param array $items
     */
    public function __construct(string $name, array $items)
    {
        $this->name = $name;
        $this->items = $items;
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
	 * @param Item[] $items
	 */
	public function setItems(array $items): void
    {
        $this->items = $items;
    }
    
    /**
     * @param Player $player
     */
    public function giveItems(Player $player): void
    {
        foreach ($this->getItems() as $item) {
            if ($player->getInventory()->canAddItem($item))
                $player->getInventory()->addItem($item);
            else
                $player->dropItem($item);
        }
    }
    
    /**
     * @param Player $player
     * @param int $count
     */
    public function givevKit(Player $player, int $count = 1): void
    {
        $item = VanillaItems::EMERALD()->setCount($count);
        $item->setCustomName(TextFormat::colorize('&r&l&6vKit Shard&r&7: &f' . $this->getName()));
        
        $lore = [
            '  ',
            '&r&fAll &6vKits Shards &fcome with the',
            '&r&fability to unlock the vKit for &a60 Days',
            ' ',
            '&r&l&7IF YOU UNLOCK THIS KIT:',
            '&r&fYou will be able to redeem',
            '&r&fyour vKit using &a/vKit',
            '  ',
            '&r&l&7IF YOU DO NOT UNLOCK THIS KIT:',
            '&r&fYou will be given the contents',
            '&r&fof the vKit one time',
            '   ',
            '&r&o&7Your chance to unlock the vKit will',
            '&r&o&7increase by 35% for each attempt.',
            '    ',
            '&r&ePurchase at &3https://' . HCFLoader::getInstance()->getConfig()->get('tebex')
        ];
        $item->setLore(array_map(function (mixed $text) {
            return TextFormat::colorize($text);
        }, $lore));
        $item->setNamedTag($item->getNamedTag()->setString('vkit_name', $this->getName()));
        $player->getInventory()->addItem($item);
    }
    
    /**
     * @param Player $player
     */
    public function openvKit(Player $player): void
    {
        HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new vKitTask($player, $this->getName()), 3);
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $data = [];
        
        foreach ($this->getItems() as $slot => $item)
            $data['items'][$slot] = ItemSerializer::encodeItem($item);
        return $data;
    }
}