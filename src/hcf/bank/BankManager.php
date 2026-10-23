<?php

namespace hcf\bank;

use hcf\HCFLoader;
use hcf\bank\command\BankCommand;
use hcf\bank\command\ResetBankLootCommand;
use pocketmine\scheduler\ClosureTask;
use hcf\kohi\Kohi;
use hcf\bank\manager\BankAssets;

class BankManager
{
  public function __construct()
  {
    $hcf = HCFLoader::getInstance();
    
    #=====REGISTER COMMANDS=====
    $hcf->getServer()->getCommandMap()->register("bank", new BankCommand());
    $hcf->getServer()->getCommandMap()->register("bank", new ResetBankLootCommand());
    
    #=====REGISTER TASKS=====
    $hcf->getScheduler()->scheduleRepeatingTask(new ClosureTask(function():void{
      BankAssets::getInstance()->lootReset();
    }), Kohi::BANK_RESET_TIME * 60 * 60 * 20);
  }
    
  public function getWorld(string $worldName): ?\pocketmine\world\World {
    return HCFLoader::getInstance()->getServer()->getWorldManager()->getWorldByName($worldName);
  }
}