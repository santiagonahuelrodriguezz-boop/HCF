<?php

namespace hcf\timer\Task;

use hcf\timer\types\TimerAirdropAll;
//use Sakura\command\LootboxCommand;
use pocketmine\Server;
use hcf\HCFLoader;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\task;

class AirdropAllTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerAirdropAll::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerAirdropAll::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if(TimerAirdropAll::getTime() === 10){
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
              $player->sendMessage(TextFormat::colorize(" "));
              $player->sendMessage(TextFormat::colorize("§r§9(§5!§9) §aEl §bAirdropAll §ase da en §b10 segundos§a, Libera Espacio"));
              $player->sendMessage(TextFormat::colorize(" "));
            }
        }
        if(TimerAirdropAll::getTime() === 0){
            //foreach (Server::getInstance()->getOnlinePlayers() as $player) {

                HCFLoader::getInstance()->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender(HCFLoader::getInstance()->getServer(), HCFLoader::getInstance()->getServer()->getLanguage()), "airdrops giveall 20");
            
            TimerAirdropAll::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerAirdropAll::setTime(TimerAirdropAll::getTime() - 1);
        }
    }
}

?>