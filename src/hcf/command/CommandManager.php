<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\HCFLoader;
use hcf\command\admin\DisbandAllCommand;
use hcf\command\admin\FtopCommand;
use hcf\command\admin\FwhoCommand;

class CommandManager
{
    
    public function __construct()
    {
       // HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new FactionXuidFixCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new FreeRankCommand("freerank"));
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new SetCoinsCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new TeleportCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new ListCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new HubCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new DiscordCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new ShopCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new FtopCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new FwhoCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new PingCommand());     
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new DisbandAllCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new TeleportAllCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new ReportCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new SetBalanceCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new CoinsCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new ListCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new ECCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new BalanceCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new FixCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new GodCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new PvPCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new LogoutCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new NearCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new RenameCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new AutoFeedCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new TLCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new PayCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new FeedCommand());
      //  HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new ClearEntitiesCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new ScoreboardCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new SpawnRaidCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new BrewerCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new SpawnCommand());
         HCFLoader::getInstance()->getServer()->getCommandMap()->register("HCF", new LFFCommand());

    }
}