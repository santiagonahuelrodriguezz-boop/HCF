<?php

namespace hcf\bank\command;

use hcf\bank\manager\BankAssets;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use hcf\bank\BankManager;
use hcf\HCFLoader;

class ResetBankLootCommand extends Command {

    public function __construct() {
        parent::__construct("resetbankloot", "Restablece los cofres del banco con loot", "/resetbankloot", []);
        $this->setPermission("bank.resetloot");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage(TextFormat::RED . "No tienes permiso para usar este comando.");
            return false;
        }

        $sender->sendMessage(TextFormat::GREEN . "Restableciendo los cofres del banco...");
        BankAssets::getInstance()->lootReset();
        $sender->sendMessage(TextFormat::GREEN . "Los cofres del banco han sido restablecidos con loot.");

        return true;
    }
}