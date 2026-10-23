<?php

namespace hcf\timer\Task;

use hcf\timer\types\TimerKeyOP;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\task;
use itoozh\crates\Main;

class KeyOPTask extends task {
    
    /**
     * KEYALLtask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerKeyOP::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerKeyOP::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if(TimerKeyOP::getTime() === 15){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
              $player->sendMessage(TextFormat::colorize("§r§9(§6!§9) §aThe §9KeyAllOP §ais give in §b15 secounds, freeing up space"));
              $player->sendMessage(TextFormat::colorize("§r§9(§6!§9) §aThe §9KeyAllOP §ais give in §b15 secounds, freeing up space"));
            }
        }
        if(TimerKeyOP::getTime() === 0){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {

                $keys = [
                    Main::getCrate("Starter")->getKeyItem(40),
                    Main::getCrate("Ability")->getKeyItem(40),
                    Main::getCrate("Leviatan")->getKeyItem(30),
                    Main::getCrate("Amon")->getKeyItem(20),
                    Main::getCrate("Koth")->getKeyItem(7),
                    Main::getCrate("Belcebu")->getKeyItem(20),
                    Main::getCrate("Lucifer")->getKeyItem(15),
                    Main::getCrate("2025")->getKeyItem(2),
       
                ];

                foreach ($keys as $items) {
                    if ($player->getInventory()->canAddItem($items)) {
                        $player->getInventory()->addItem($items);
                    } else {
                        $player->getWorld()->dropItem($player->getDirectionVector(), $items);
                    }
                }

            }
            TimerKeyOP::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerKeyOP::setTime(TimerKeyOP::getTime() - 1);
        }
    }
  }
?>