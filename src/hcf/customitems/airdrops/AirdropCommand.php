<?php
    
namespace hcf\customitems\airdrops;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;
use hcf\HCFLoader;
use hcf\customitems\airdrops\data\AirdropData;
use hcf\customitems\airdrops\Airdrop;

class AirdropCommand extends Command {

    public function __construct() {
        parent::__construct("airdrops", "Airdrop management commands");
        $this->setPermission("op.permission");
    }

    public function execute(CommandSender $sender, string $label, array $args): void {

        if (!isset($args[0])) {
            $sender->sendMessage("§cUsage: /airdrops <edit|give|giveall> [amount]");
            return;
        }

        switch ($args[0]) {
            case "edit":
                $this->openEditInventory($sender);
                break;

            case "give":
                if (isset($args[1]) && isset($args[2])) {
                    $player = Server::getInstance()->getPlayerByPrefix($args[1]);

                    if (!$player instanceof Player) {
                        $sender->sendMessage('§cInvalid player.');
                        return;
                    }

                    if (!is_numeric($args[2])) {
                        $sender->sendMessage('§cCant is invalid.');
                        return;
                    }

                    $amount = (int) $args[2];
                    $this->giveAirdropItem($player, $amount);
                    $sender->sendMessage("§aYou have given $amount Airdrops to " . $player->getName() . ".");
                } else {
                    $sender->sendMessage("§cUsage: /airdrops give <player> <amount>");
                }
                break;

            case "giveall":
                if (isset($args[1]) && is_numeric($args[1])) {
                    $amount = (int) $args[1];
                    $this->giveAirdropItemToAllPlayers($amount);
                    $sender->sendMessage("§aYou have given $amount Airdrops to all players.");
                } else {
                    $sender->sendMessage("§cUsage: /airdrops giveall <amount>");
                }
                break;

            default:
                $sender->sendMessage("§cUsage: /airdrops <edit|give|giveall>");
                break;
        }
    }

    private function openEditInventory(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $menu->setName("§gAirdrop Loot Editor");

        $loots = AirdropData::getLoot();
        if ($loots !== null) {
            foreach ($loots as $slot => $item) {
                $menu->getInventory()->setItem($slot, $item);
            }
        }

        $menu->setInventoryCloseListener(function (Player $player, Inventory $inventory): void {
            AirdropData::saveLoot($inventory->getContents());
            $player->sendMessage("§a» Airdrop loot guardado correctamente.");
        });

        $menu->send($player);
    }

    private function giveAirdropItem(Player $player, int $amount): void {
        for ($i = 0; $i < $amount; $i++) {
            $airdrop = new Airdrop();
            $airdrop->giveAirdrop($player);
        }
        $player->sendMessage("§aYou have received $amount Airdrops.");
    }

    private function giveAirdropItemToAllPlayers(int $amount): void {
        foreach (HCFLoader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            for ($i = 0; $i < $amount; $i++) {
                $airdrop = new Airdrop();
                $airdrop->giveAirdrop($player);
            }
            $player->sendMessage("§aYou have received $amount Airdrops.");
        }
    }
}