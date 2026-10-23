<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;
use hcf\HCFLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetCoinsCommand extends Command
{

    public function __construct()
    {
        parent::__construct('setcoins', 'Use command for setcoins');
        $this->setPermission("op.permission");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }
        
        if (count($args) < 2) {
            $sender->sendMessage(TextFormat::colorize("&cUso: /setcoins {player} {coins}"));
            return;
        }
        
        $targetName = $args[0];
        $targetPlayer = HCFLoader::getInstance()->getServer()->getPlayerExact($targetName);
        
        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage(TextFormat::RED."Jugador $targetPlayer no encontrado!");
            return;  
        }
        
        $coins = (int)$args[1];
        $targetPlayer->getSession()->setCrystals($coins);
        
        $sender->sendMessage(TextFormat::colorize("&eCoins of &f". $targetName . " &eset &a$". $targetPlayer->getSession()->getCrystals()));
    }
}