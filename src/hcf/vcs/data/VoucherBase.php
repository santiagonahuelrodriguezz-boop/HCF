<?php

namespace hcf\vcs\data;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;

abstract class VoucherBase extends Item implements Listener
{
    protected string $voucherName;
    protected array $lore;

    public function __construct(ItemIdentifier $identifier, string $name, array $lore)
    {
        parent::__construct($identifier, $name);
        $this->setCustomName($name);
        $this->setLore($lore);
    }

    abstract protected function onVoucherUse(Player $player): void;

    public function itemUse(PlayerItemUseEvent $ev): void
    {
        $player = $ev->getPlayer();
        $item = $ev->getItem();

        if ($item->getCustomName() !== $this->getCustomName()) {
            return;
        } else {
            $item->pop();
            $player->getInventory()->setItemInHand($item);
            $this->onVoucherUse($player);
        }
    }
}