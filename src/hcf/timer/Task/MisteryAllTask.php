<?php

namespace hcf\timer\Task;

use hcf\timer\types\TimerMisteryAll;
//use Sakura\command\LootboxCommand;
use pocketmine\Server;
use hcf\HCFLoader;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\task;

class MisteryAllTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerMisteryAll::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerMisteryAll::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if(TimerMisteryAll::getTime() === 15){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
              $player->sendMessage(TextFormat::colorize(" "));
              $player->sendMessage(TextFormat::colorize("§r§9(§5!§9) §aEl §bMysteryAll §ase da en §b10 segundos§a, Libera Espacio"));
              $player->sendMessage(TextFormat::colorize(" "));
            }
        }
        if(TimerMisteryAll::getTime() === 0){
          //  foreach (Server::getInstance()->getOnlinePlayers() as $player) {

                HCFLoader::getInstance()->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(HCFLoader::getInstance()->getServer(), HCFLoader::getInstance()->getServer()->getLanguage()), "mysterycrate giveall 5");
            
            TimerMisteryAll::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerMisteryAll::setTime(TimerMisteryAll::getTime() - 1);
        }
    }
}

?>