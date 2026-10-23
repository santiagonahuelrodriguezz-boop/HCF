<?php

namespace hcf\timer\Task;

use hcf\HCFLoader;
use hcf\timer\types\TimerPackages;
use pocketmine\Server;
use pocketmine\console\ConsoleCommandSender;

use pocketmine\scheduler\task;

class PackagesTask extends task {
    
    /**
     * keytask Constructor.
     * @param Int $time
     */
    public function __construct(Int $time = 60){
        TimerPackages::setTime($time);
    }
    
    /**
     * @param Int $currentTick
     * @return void
     */
    public function onRun() : void {
        if(!TimerPackages::isEnable()){
            $this->getHandler()->cancel();
            return;
        }
        if(TimerPackages::getTime() === 0){
			$HCF = HCFLoader::getInstance();
            $HCF->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), 'pkg giveall 25');
            TimerPackages::setEnable(false);
            $this->getHandler()->cancel();
        }else{
            TimerPackages::setTime(TimerPackages::getTime() - 1);
        }
    }
}

?>