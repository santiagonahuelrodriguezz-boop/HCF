<?php

namespace hcf\vcs\vouchers;

use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use hcf\vcs\data\VoucherBase;
use hcf\vcs\data\Vouchers;

class RogueVoucher extends VoucherBase
{
    public function __construct()
    {
        $identifier = new ItemIdentifier(ItemTypeIds::CHAINMAIL_HELMET, 0); 
        parent::__construct($identifier, "§r§fRogue §7Voucher", ["", "§r§7• Item exclusivo de la crate §dVoucher", "§r§7• Este Voucher hara entrega de", "§r§7• x1 kit §fRogue§7 protección II.", "", "§r§e•> Clic para reclamar. <•"]);
    }

    protected function onVoucherUse(Player $player): void
    {
        Vouchers::giveVoucherItems($player, "Rogue");
        $player->sendMessage("§aYou have received the Rogue items!");
    }
}