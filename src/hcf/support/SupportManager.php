<?php

namespace hcf\support;

use hcf\HCFLoader;
use hcf\support\entity\SupportEntity;
use hcf\support\provider\ProviderSupport;
use hcf\support\command\SupportCommand;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class SupportManager
{
  public function __construct()
  {
    $hcf = HCFLoader::getInstance();
    
    #=====REGISTER COMMANDS=====
    $hcf->getServer()->getCommandMap()->register('support', new SupportCommand());
    
    #=====REGISTER ENTITIES=====
    EntityFactory::getInstance()->register(SupportEntity::class, function (World $world, CompoundTag $nbt): SupportEntity{
      return new SupportEntity(EntityDataHelper::parseLocation($nbt, $world), SupportEntity::parseSkinNBT($nbt), $nbt);
    }, ["SupportEntity"]);
    
    #=====INIT PROVIDER=====
    ProviderSupport::getInstance()->loadData();
  }
}