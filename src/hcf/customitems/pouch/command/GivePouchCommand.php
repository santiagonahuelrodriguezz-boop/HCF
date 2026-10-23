<?php

namespace hcf\customitems\pouch\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use hcf\customitems\pouch\PouchTier; 

class GivePouchCommand extends Command
{
    public function __construct()
    {
        parent::__construct("givepouch", "§o§aGive Pouch of Wealth to a player");
        $this->setPermission('op.permission');
    }

    public function execute(CommandSender $sender, string $label, array $args)
    {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (count($args) < 2) {
            $sender->sendMessage("§eUsage: /givepouch [player] [amount]");
            return;
        }

        $targetName = $args[0];
        $amount = (int)$args[1];

        if ($amount <= 0) {
            $sender->sendMessage("§gAmount must be a positive number.");
            return;
        }

        $targetPlayer = $sender->getServer()->getPlayerExact($targetName);

        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage("§cPlayer $targetName not found!");
            return;
        }


            
        $pouchTier = new PouchTier();
        $pouchTier->givePouch($targetPlayer, $amount); 
        
        $sender->sendMessage("§cYou gave $amount Pouch(es) of Wealth to ".$targetName);
        $targetPlayer->sendMessage("§aYou have received $amount Pouch(es) of Wealth from ".$sender->getName());
    }
}