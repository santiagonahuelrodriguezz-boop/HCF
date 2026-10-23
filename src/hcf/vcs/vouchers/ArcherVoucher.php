<?php

namespace hcf\vcs\vouchers;

use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use hcf\vcs\data\VoucherBase;
use hcf\vcs\data\Vouchers;

class ArcherVoucher extends VoucherBase
{
    public function __construct()
    {
        $identifier = new ItemIdentifier(ItemTypeIds::LEATHER_CAP, 0);
        parent::__construct($identifier, "§r§dArcher §7Voucher", ["", "§r§7• Item exclusivo de la crate §dVoucher", "§r§7• Este Voucher hara entrega de", "§r§7• x1 kit §dArcher§7 protección II.", "", "§r§e•> Clic para reclamar. <•"]);
    }

    protected function onVoucherUse(Player $player): void
    {
        Vouchers::giveVoucherItems($player, "Archer");
        $player->sendMessage("§aYou have received the Archer items!");
    }
}