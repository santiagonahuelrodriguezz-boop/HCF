<?php

namespace hcf\timer\command;

use hcf\timer\types\TimerLootbox;
use hcf\utils\time\Timer;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class LootboxCommand extends Command {
    
    /**
     * KeyallCommand Constructor.
     */
    public function __construct(){
        parent::__construct('lootboxevent', 'Use command for auto lootboxall');
        $this->setPermission("sotw.command");
    }
    
    /**
     * @param CommandSender $sender
     * @param String $label
     * @param Array $args
     * @return void
     */

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {

        if(count($args) === 0){
            $sender->sendMessage(TextFormat::colorize("&cUsar: /{$commandLabel} <start|stop>"));
            return;
        }
        if (!$sender->hasPermission("sotw.command")) {
            $sender->sendMessage(TextFormat::colorize('&cNo tienes permisos para usar este comando'));
            return;
        }

        switch($args[0]){
            case "start":
                if (!$sender->hasPermission("sotw.command")) {
                    $sender->sendMessage(TextFormat::colorize('&cNo tienes permisos para usar este comando'));
                    return;
                }
                if(empty($args[1])){
                    $sender->sendMessage(TextFormat::colorize("&cUsar: /{$commandLabel} {$args[0]} [Int: time]"));
                    return;
                }
                if(TimerLootbox::isEnable()){
                    $sender->sendMessage(TextFormat::colorize('&cEl evento se inició antes, ¡no puedes hacer esto!'));
                    return;
                }

                $time = $args[1];

                $time = Timer::time($time);

                TimerLootbox::start($time);
            break;
            case "stop":
                if (!$sender->hasPermission("sotw.command")) {
                    $sender->sendMessage(TextFormat::colorize('&cNo tienes permisos para usar este comando'));
                    return;
                }
                if(!TimerLootbox::isEnable()){
                    $sender->sendMessage(TextFormat::colorize('&cEl evento nunca se inició, ¡no puedes hacer esto!'));
                    return;
                }
                TimerLootbox::stop();
            break;
        }
    }
}

?>