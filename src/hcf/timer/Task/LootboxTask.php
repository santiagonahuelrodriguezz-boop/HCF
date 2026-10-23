<?php

namespace hcf\timer\Task;

use hcf\timer\types\TimerLootbox;
//use Sakura\command\LootboxCommand;
use pocketmine\Server;
use hcf\HCFLoader;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\task;

class LootboxTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerLootbox::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerLootbox::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if(TimerLootbox::getTime() === 10){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
              $player->sendMessage(TextFormat::colorize(" "));
              $player->sendMessage(TextFormat::colorize("§r§9(§5!§9) §aEl §bMysteryAll §ase da en §b10 segundos§a, Libera Espacio"));
              $player->sendMessage(TextFormat::colorize(" "));
            }
        }
        if(TimerLootbox::getTime() === 0){
           // foreach (Server::getInstance()->getOnlinePlayers() as $player) {

                HCFLoader::getInstance()->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(HCFLoader::getInstance()->getServer(), HCFLoader::getInstance()->getServer()->getLanguage()), "lootbox giveall 20");
            
            TimerLootbox::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerLootbox::setTime(TimerLootbox::getTime() - 1);
        }
    }
}

?>