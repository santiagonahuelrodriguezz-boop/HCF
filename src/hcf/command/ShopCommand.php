<?php

namespace hcf\command;

use hcf\handler\shop\ShopMenu; 
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use hcf\player\Player;
use pocketmine\plugin\PluginOwned;

class ShopCommand extends Command
{
    public function __construct()
    {
        parent::__construct('shop', 'Opens the shop menu');
        $this->setPermission("player.use");
    }
    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return;
        }

        if (!$sender instanceof Player) {
            return;
        }

        $this->openShopMenu($sender);
    }

    private function openShopMenu(Player $player): void
    {
        ShopMenu::MenuShop($player);
         
    }
}