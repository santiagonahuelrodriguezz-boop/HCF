<?php

namespace hcf\addons;


/*use hcf\addons\modules\IpLogger;
use hcf\addons\modules\KnockBack;
use hcf\addons\modules\AutoBrewing;
use hcf\addons\modules\AutoSmelting;
use hcf\addons\modules\CustomEnchantsEvents;
use hcf\addons\voucher\Voucher;
use hcf\addons\voucher\GiveVoucher;
use hcf\addons\modules\HCFNether;
use hcf\addons\modules\JoinCommand;
use hcf\addons\modules\SwordStats;
use hcf\addons\bow\BowHitSound;
use hcf\addons\enchants\TransactionEnchants;*/
use hcf\addons\walls\PvPTimerWallModule;
use hcf\addons\walls\CombatWallModule;
//use hcf\addons\modules\TemporalCobwebs;
//use hcf\addons\modules\OreBreak; 
use hcf\HCFLoader;
use pocketmine\event\Listener;

final class AddonsManager implements Listener
{
    public static function init(): void
    {
        //IpLogger::setInstance(new IpLogger());
        $plugin = HCFLoader::getInstance();

        
    //    $plugin->getServer()->getPluginManager()->registerEvents(new JoinCommand(), $plugin);
        //$plugin->getServer()->getPluginManager()->registerEvents(new IpLogger(), $plugin);
//$plugin->getServer()->getPluginManager()->registerEvents(new SwordStats(), $plugin);
        $plugin->getServer()->getPluginManager()->registerEvents(new PvPTimerWallModule(), $plugin);
        $plugin->getServer()->getPluginManager()->registerEvents(new CombatWallModule(), $plugin);
      //  $plugin->getServer()->getPluginManager()->registerEvents(new TemporalCobwebs(), $plugin);
       // $plugin->getServer()->getPluginManager()->registerEvents(new KnockBack(), $plugin);
       // $plugin->getServer()->getPluginManager()->registerEvents(new TransactionEnchants(), $plugin);
        //$plugin->getServer()->getPluginManager()->registerEvents(new BowHitSound(), $plugin);
        //$plugin->getServer()->getPluginManager()->registerEvents(new Voucher(), $plugin);

       // $plugin->getServer()->getPluginManager()->registerEvents(new OreBreak($plugin), $plugin);

      
    }
}