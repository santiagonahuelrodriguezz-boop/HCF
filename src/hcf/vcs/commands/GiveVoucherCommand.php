<?php

namespace hcf\vcs\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use hcf\vcs\vouchers\KothVoucher;
use hcf\vcs\vouchers\DiamondVoucher;
use hcf\vcs\vouchers\ArcherVoucher;
use hcf\vcs\vouchers\BardVoucher;
use hcf\vcs\vouchers\RogueVoucher;
use hcf\vcs\vouchers\RankVoucher;

class GiveVoucherCommand extends Command
{
    public function __construct()
    {
        parent::__construct("givevoucher", "Gives a voucher to the player");
        $this->setPermission("op.permission");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game.");
            return;
        }

        if (empty($args[0])) {
            $sender->sendMessage("§cPlease specify a voucher name.");
            return;
        }

        $voucherName = strtolower($args[0]);
        $voucherItem = null;

        // Crear el objeto del voucher según el nombre ingresado
        switch ($voucherName) {
            case "koth":
                $voucherItem = new KothVoucher();
                break;
            case "diamond":
                $voucherItem = new DiamondVoucher();
                break;
            case "archer":
                $voucherItem = new ArcherVoucher();
                break;
            case "bard":
                $voucherItem = new BardVoucher();
                break;
            case "rogue":
                $voucherItem = new RogueVoucher();
                break;
            case "rank":
                $voucherItem = new RankVoucher();
                break;
            default:
                $sender->sendMessage("§cVoucher not found.");
                return;
        }


        if ($voucherItem !== null) {
            $sender->getInventory()->addItem($voucherItem);
            $sender->sendMessage("§aYou have received the §b".$voucherName."§a voucher!");
        }
    }
}