<?php

declare(strict_types=1);

namespace hcf\koth\command\subcommand;

use hcf\koth\command\KothSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class ListSubCommand
 * @package hcf\koth\command\subcommand
 */
class ListSubCommand implements KothSubCommand
{
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        $kothManager = HCFLoader::getInstance()->getKothManager();
        $sender->sendMessage(TextFormat::colorize('&e§l× KOTH LIST ×§r'));

        foreach ($kothManager->getKoths() as $name => $koth) {
            
            $coords = $koth->getCoords() ?? 'None';
            
            $capzone = $koth->getCapzone();
            if($capzone === null){
                $world = 'None';
            } else {
                $world = $capzone->getWorld();
            }
            
            $sender->sendMessage(TextFormat::colorize('&g' . $name . '&f >> ' . $coords . ' &7(§b ' . $world . ' §7)'));
        }
    }
}
