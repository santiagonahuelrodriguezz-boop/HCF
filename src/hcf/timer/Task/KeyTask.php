<?php

namespace hcf\timer\Task;

use hcf\timer\types\TimerKey;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\task;
use itoozh\crates\Main;

class KeyTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerKey::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerKey::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if(TimerKey::getTime() === 15){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
              $player->sendMessage(TextFormat::colorize("§r§9(§6!§9) §aThe §9KeyAll §ais given in §b10 secounds, FREES UP SPACE"));
              $player->sendMessage(TextFormat::colorize("§r§9(§6!§9) §aThe §9KeyAll §ais given in §b10 secounds, FREES UP SPACE"));
              $player->sendMessage(TextFormat::colorize("§r§9(§6!§9) §aThe §9KeyAll §ais given in §b10 secounds, FREES UP SPACE"));
            }
        }
        if(TimerKey::getTime() === 0){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {

                $keys = [
                    Main::getCrate("Starter")->getKeyItem(40),
                    Main::getCrate("Ability")->getKeyItem(40),
                    Main::getCrate("Leviatan")->getKeyItem(30),
                    Main::getCrate("Amon")->getKeyItem(20),
                    Main::getCrate("Koth")->getKeyItem(5),
                    Main::getCrate("Lucifer")->getKeyItem(15),
                    
                ];

                foreach ($keys as $items) {
                    if ($player->getInventory()->canAddItem($items)) {
                        $player->getInventory()->addItem($items);
                    } else {
                        $player->getWorld()->dropItem($player->getDirectionVector(), $items);
                    }
                }
            }
            TimerKey::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerKey::setTime(TimerKey::getTime() - 1);
        }
    }
}

?>