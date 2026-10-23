<?php

namespace hcf\module;

//use hcf\module\announcement\AnnouncementManager;
//use hcf\module\coinshop\CoinShopManager;
use hcf\HCFLoader; 
use hcf\module\task\ActionBarTask; 
use hcf\module\task\BossBarTask; 

class ModuleManager {
    
    //public AnnouncementManager $announcementManager;
    //public CoinShopManager $coinShopManager;

    public function __construct(){
      //  $this->announcementManager = new AnnouncementManager;
        //$this->coinShopManager = new CoinShopManager;

        $bar = new ActionBarTask(1);
        $bossBar = new BossBarTask(HCFLoader::getInstance()); 
        
        HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask($bar, 20);
        HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask($bossBar, 20);
    }
    
   /* public function getAnnouncementManager(): AnnouncementManager {
        return $this->announcementManager;
    }
    
    public function getCoinShopManager(): CoinShopManager {
        return $this->coinShopManager;
    }*/
}