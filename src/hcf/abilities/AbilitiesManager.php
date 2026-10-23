<?php

namespace hcf\abilities;

use hcf\abilities\entity\BallOfRangeEntity;
use hcf\abilities\entity\HogRiderEntityOne;
use hcf\abilities\entity\HogRiderEntityTwo;
use hcf\abilities\entity\HogRiderEntityThree;
use hcf\abilities\entity\FrezzerGunEntity;
use hcf\abilities\entity\PortableBardEntity;
use hcf\abilities\entity\RangeEntity;
use hcf\abilities\entity\SwitcherEntity;
use hcf\abilities\items\AbilityDisabler;
use hcf\abilities\items\BallOfRange;
use hcf\abilities\items\Berserk;
use hcf\abilities\items\FlowerTank;
use hcf\abilities\items\EffectDisabler;
use hcf\abilities\items\Cactus;
use hcf\abilities\items\PearlCooldown;
use hcf\abilities\items\ExoticBone;
use hcf\abilities\items\Firework;
use hcf\abilities\items\FocusMode;
use hcf\abilities\items\FrezzerGun;
use hcf\abilities\items\Freeze;
use hcf\abilities\items\IceSphere;
use hcf\abilities\items\NinjaStar;
use hcf\abilities\items\LifeSaver;
use hcf\abilities\items\PocketBard;
use hcf\abilities\items\PotionRefill;
use hcf\abilities\items\HogRider;
use hcf\abilities\items\Resistance;
use hcf\abilities\items\Samurai;
use hcf\abilities\items\SecondChance;
use hcf\abilities\items\Strength;
use hcf\abilities\items\StromBreaker;
use hcf\abilities\items\Pumpkin;
use hcf\abilities\items\SullCrat;
use hcf\abilities\items\Switcher;
use hcf\abilities\items\CloseCall;
use hcf\abilities\items\Melonite;
use hcf\abilities\items\NinjaShear;
use hcf\abilities\items\Medkit;
use hcf\HCFLoader;
use hcf\vkit\vKitListener;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class AbilitiesManager
{
    public function __construct()
    {
        # Register handler
        //HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new AbilitiesCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new GiveAbilitys());

        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Samurai(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new PotionRefill(), HCFLoader::getInstance());
       // HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new RickyMode(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new PearlCooldown(), HCFLoader::getInstance());
        //HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Invisibility(), HCFLoader::getInstance());
        //HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new ComboAbility(HCFLoader::getInstance()), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new SullCrat(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Switcher(), HCFLoader::getInstance());
        //HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new TimeWarp(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new ExoticBone(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new EffectDisabler(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new LifeSaver(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new FrezzerGun(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new SecondChance(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new AbilityDisabler(), HCFLoader::getInstance());
        //HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Regeneration(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Resistance(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Strength(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Freeze(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new BallOfRange(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Firework(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Berserk(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new NinjaStar(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new StromBreaker(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new FlowerTank(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new FocusMode(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new CloseCall(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Melonite(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Pumpkin(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Medkit(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Cactus(), HCFLoader::getInstance());
        //HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Sphere(), HCFLoader::getInstance());
        //HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new FairFight(), HCFLoader::getInstance());
        //HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new Brick(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new HogRider(), HCFLoader::getInstance());
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new IceSphere(), HCFLoader::getInstance());

        EntityFactory::getInstance()->register(SwitcherEntity::class, function (World $world, CompoundTag $nbt): SwitcherEntity {
            return new SwitcherEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['SwitcherEntity']);

        EntityFactory::getInstance()->register(BallOfRangeEntity::class, function (World $world, CompoundTag $nbt): BallOfRangeEntity {
            return new BallOfRangeEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['BallOfRangeEntity']);
        
        EntityFactory::getInstance()->register(HogRiderEntityOne::class, function(World $world, CompoundTag $nbt): HogRiderEntityOne{
            return new HogRiderEntityOne(EntityDataHelper::parseLocation($nbt,$world));
        },["HogRiderEntityOne"]);
        
        EntityFactory::getInstance()->register(HogRiderEntityTwo::class, function(World $world, CompoundTag $nbt): HogRiderEntityTwo{
            return new HogRiderEntityTwo(EntityDataHelper::parseLocation($nbt,$world));
        },["HogRiderEntityTwo"]);
        
        EntityFactory::getInstance()->register(HogRiderEntityThree::class, function(World $world, CompoundTag $nbt): HogRiderEntityThree{
            return new HogRiderEntityThree(EntityDataHelper::parseLocation($nbt,$world));

        },["HogRiderEntityThree"]);

        EntityFactory::getInstance()->register(FrezzerGunEntity::class, function (World $world, CompoundTag $nbt): FrezzerGunEntity {
            return new FrezzerGunEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['FrezzerGunEntity']);
    }

}