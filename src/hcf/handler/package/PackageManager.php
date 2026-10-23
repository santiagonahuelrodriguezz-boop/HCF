<?php

namespace hcf\handler\package;

use hcf\HCFLoader;
use hcf\handler\package\command\PartnerPackagesCommand;
use hcf\handler\package\PackageListener;

class PackageManager {

    public function __construct(){
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("/pkg", new PartnerPackagesCommand());
        
         HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new PackageListener(), HCFLoader::getInstance());
    }
    
}

