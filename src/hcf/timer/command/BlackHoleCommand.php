<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class BlackHoleCommand
 * @package hcf\timer\command
 */
class BlackHoleCommand extends Command
{
    
    /**
     * BlackHoleCommand construct.
     */
    public function __construct()
    {
        parent::__construct('blackhole', '§bCommand for event blackhole');
        $this->setPermission('eotw.command');
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender))
            return;
            
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /blackhole help'));
            return;
        }
        
        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&eblackhole Commands') . "\n" .
                    TextFormat::colorize('&7/blackhole start [time] - &eUse this command to start the blackhole Event') . "\n" .
                    TextFormat::colorize('&7/blackhole stop - &eUse this command to stop blackhole event')
                );
                break;
            
            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getBlackHole()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe blackhole is already started'));
                    return;
                }
                
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /blackhole start [time]'));
                    return;
                }

                $time = $args[1];               
                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getBlackHole()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getBlackHole()->setTime((int) $time);
                $broadcastMessage = [
                    TextFormat::colorize("&7███████"),
                    TextFormat::colorize("&7██&0█&0█&0█&7██"),
                    TextFormat::colorize("&7█&0████&0█&7█ &r&7[§l§0BlackHole Event§r§7]"),
                    TextFormat::colorize("&7█&0████&0█&7█ &r&eThe Event Has Started!"),
                    TextFormat::colorize("&7█&0████&0█&7█ Kill players to get a key"),
                    TextFormat::colorize("&7██&0█&0█&0█&7██"),
                    TextFormat::colorize("&7███████")
                ];
                foreach ($broadcastMessage as $message) {
                    $sender->getServer()->broadcastMessage($message);
                }

                $sender->sendMessage(TextFormat::colorize('§aBlackHole has started!'));
                break;
            
            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getBlackHole()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe blackhole has not started'));
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getBlackHole()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the blackhole'));
                break;
            
            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /blackhole help'));
                break;
        }
    }
}

?>