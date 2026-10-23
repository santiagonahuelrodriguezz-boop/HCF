<?php

namespace hcf\command;

use hcf\player\Player;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\DyeColor;
use pocketmine\Server;

class TeleportCommand extends Command {

    public function __construct() {
        parent::__construct("portals", "Opens the teleport menu", null, []);
        $this->setPermission("portals.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cThis command can only be used in-game.");
            return;
        }

        if (!$sender->hasPermission("portals.command")) {
            $sender->sendMessage("§cYou don't have permission to use this command.");
            return;
        }

        $this->openMenu($sender);
    }

    private function openMenu(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("§l§aTeleport Menu§r");

        $glass = VanillaBlocks::STAINED_HARDENED_GLASS()->setColor(DyeColor::BLUE())->asItem();
        $glass->setCustomName(" ");
        $endItem = VanillaItems::PAPER();
        $endItem->setCustomName("§l§uEND");
        $netherItem = VanillaItems::PAPER();
        $netherItem->setCustomName("§l§cNETHER");

        for ($i = 0; $i < 27; $i++) {
            $menu->getInventory()->setItem($i, $glass);
        }
        $menu->getInventory()->setItem(12, $endItem);
        $menu->getInventory()->setItem(14, $netherItem);

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $itemClicked = $transaction->getItemClicked();

            if ($itemClicked->getCustomName() === "§l§uEND") {
                $endWorld = Server::getInstance()->getWorldManager()->getWorldByName("end");
                if ($endWorld !== null) {
                    $player->teleport($endWorld->getSafeSpawn());
                    $player->sendMessage("§aTeleported to the END world.");
                } else {
                    $player->sendMessage("§cThe END world is not available.");
                }
            }

            if ($itemClicked->getCustomName() === "§l§cNETHER") {
                $netherWorld = Server::getInstance()->getWorldManager()->getWorldByName("nether");
                if ($netherWorld !== null) {
                    $player->teleport($netherWorld->getSafeSpawn());
                    $player->sendMessage("§aTeleported to the Nether world.");
                } else {
                    $player->sendMessage("§cThe Nether world is not available.");
                }
            }

            $transaction->getPlayer()->removeCurrentWindow();

            return $transaction->discard();
        });

        $menu->send($player);
    }
}