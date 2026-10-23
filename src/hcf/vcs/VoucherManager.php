<?php

declare(strict_types=1);

namespace hcf\vcs;

use hcf\vcs\commands\GiveVoucherCommand;
use hcf\vcs\commands\VoucherEditCommand;
use hcf\vcs\vouchers\KothVoucher;
use hcf\vcs\vouchers\DiamondVoucher;
use hcf\vcs\vouchers\BardVoucher;
use hcf\vcs\vouchers\ArcherVoucher;
use hcf\vcs\vouchers\RogueVoucher;
use hcf\vcs\vouchers\RankVoucher;
use hcf\vcs\data\VoucherBase;
use hcf\vcs\data\Vouchers;
use hcf\HCFLoader;
use pocketmine\plugin\PluginManager;

class VoucherManager
{
    public function __construct()
    {
        Vouchers::init();
        $hcf = HCFLoader::getInstance();
        #=====REGISTER COMMANDS=====
        $hcf->getServer()->getCommandMap()->register('HCF', new GiveVoucherCommand());
        $hcf->getServer()->getCommandMap()->register('HCF', new VoucherEditCommand());
        #=====REGISTER EVENTS=====
        $hcf->getServer()->getPluginManager()->registerEvents(new KothVoucher(), $hcf);
        
        $hcf->getServer()->getPluginManager()->registerEvents(new ArcherVoucher(), $hcf);
        $hcf->getServer()->getPluginManager()->registerEvents(new RogueVoucher(), $hcf);
        $hcf->getServer()->getPluginManager()->registerEvents(new BardVoucher(), $hcf);
        $hcf->getServer()->getPluginManager()->registerEvents(new RankVoucher(), $hcf);
        
    }
}