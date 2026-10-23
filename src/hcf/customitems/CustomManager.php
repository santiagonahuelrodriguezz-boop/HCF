<?php

declare(strict_types=1);

namespace hcf\customitems;

use hcf\customitems\airdrops\data\AirdropData;
use hcf\customitems\airdrops\AirdropCommand;
use hcf\customitems\airdrops\Airdrop;
use hcf\customitems\lootbox\LootBoxCommand;
use hcf\customitems\lootbox\LootBoxListener;
use hcf\customitems\lootbox\Loot;
use hcf\customitems\airdrops\entity\AirdropEntity;
use hcf\customitems\pouch\command\GivePouchCommand;
use hcf\customitems\pouch\PouchTier;
use hcf\HCFLoader;
use pocketmine\plugin\PluginManager;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;

class CustomManager
{
    public function __construct()
    {
        Loot::getInstance()->load();
        AirdropData::init();
        $hcf = HCFLoader::getInstance();

        #=====REGISTER COMMANDS=====
        $hcf->getServer()->getCommandMap()->register('HCF', new AirdropCommand());
        $hcf->getServer()->getCommandMap()->register('HCF', new LootBoxCommand());
        $hcf->getServer()->getCommandMap()->register('HCF', new GivePouchCommand());

        #=====REGISTER EVENTS=====
        $hcf->getServer()->getPluginManager()->registerEvents(new PouchTier(), $hcf);
        $hcf->getServer()->getPluginManager()->registerEvents(new LootBoxListener($hcf), $hcf);
        $hcf->getServer()->getPluginManager()->registerEvents(new Airdrop(), $hcf);

        #=====REGISTER ENTITIES=====
        

// a....
        
    }
}