<?php

namespace hcf\vcs\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use hcf\vcs\data\Vouchers;

class VoucherEditCommand extends Command
{
    public function __construct()
    {
        parent::__construct("voucheredit", "Edits the contents of a voucher");
        $this->setPermission("op.permission");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game.");
            return;
        }

        if (empty($args[0])) {
            $sender->sendMessage("§cPlease specify a voucher name to edit.");
            return;
        }

        $voucherName = $args[0];
        Vouchers::saveVoucherItems($sender, $voucherName);
        $sender->sendMessage("§aThe items in your inventory have been saved to voucher: §b$voucherName");
    }
}