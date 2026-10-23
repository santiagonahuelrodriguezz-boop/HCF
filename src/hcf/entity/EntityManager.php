<?php

declare(strict_types=1);

namespace hcf\entity;

use hcf\entity\server\DailyEntity;
use hcf\entity\server\NetworksEntity;
use hcf\entity\server\ShopEntity;
use hcf\entity\SupportMenu;
use hcf\entity\mob\{HCFCreeper,
    HCFEnderMan,
    Hog1,
    Hog2,
    Ho3};
use hcf\entity\default\EnderPearlEntity;
use hcf\entity\default\SplashPotionEntity;
use hcf\entity\projectiles\FireworksRocket;
use hcf\entity\server\RepairEntity;
use hcf\entity\command\EntitySpawnCommand;
use hcf\entity\command\RemoveEntityCommand;
use hcf\entity\server\SpawnEntity;
use hcf\entity\server\TeleportEntity;
use hcf\abilities\entity\AbilityEntity;
use hcf\entity\server\InfoEntity;
use hcf\entity\server\DeathEntity;
use hcf\entity\tops\TopFactionsEntity;
use hcf\entity\tops\TopKDREntity;
use hcf\entity\server\CoinShopEntity;
use hcf\entity\tops\TopKillsEntity;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\item\PotionType;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use hcf\HCFLoader;

class EntityManager
{
    public function __construct()
    {
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new EntitySpawnCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new RemoveEntityCommand());

        EntityFactory::getInstance()->register(CustomItemEntity::class, function (World $world, CompoundTag $nbt): ItemEntity {
            $itemTag = $nbt->getCompoundTag("Item");
            if ($itemTag === null) {
                throw new SavedDataLoadingException("Expected \"Item\" NBT tag not found");
            }

            $item = Item::nbtDeserialize($itemTag);
            if ($item->isNull()) {
                throw new SavedDataLoadingException("Item is invalid");
            }
            return new CustomItemEntity(EntityDataHelper::parseLocation($nbt, $world), $item, $nbt);
        }, ['Item', 'minecraft:item'], EntityIds::ITEM);

        EntityFactory::getInstance()->register(TextEntity::class, function (World $world, CompoundTag $nbt): TextEntity {
            return new TextEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['TextEntity', 'minecraft:textentity'], EntityIds::BAT);

        EntityFactory::getInstance()->register(EnderPearlEntity::class, function (World $world, CompoundTag $nbt): EnderPearlEntity {
            return new EnderPearlEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['ThrownEnderpearl', 'minecraft:ender_pearl'], EntityIds::ENDER_PEARL);

        EntityFactory::getInstance()->register(SplashPotionEntity::class, function (World $world, CompoundTag $nbt): SplashPotionEntity {
            return new SplashPotionEntity(EntityDataHelper::parseLocation($nbt, $world), null, PotionType::STRONG_HEALING(), $nbt);
        }, ['ThrownPotion', 'minecraft:splash_potion'], EntityIds::SPLASH_POTION);

        EntityFactory::getInstance()->register(AbilityEntity::class, function (World $world, CompoundTag $nbt): AbilityEntity {
            return new AbilityEntity(EntityDataHelper::parseLocation($nbt, $world), AbilityEntity::parseSkinNBT($nbt), $nbt);
        }, ['AbilityEntity']);

        EntityFactory::getInstance()->register(DeathEntity::class, function (World $world, CompoundTag $nbt): DeathEntity {
            return new DeathEntity(EntityDataHelper::parseLocation($nbt, $world), DeathEntity::parseSkinNBT($nbt), $nbt);
        }, ['DeathEntity']);

        EntityFactory::getInstance()->register(TeleportEntity::class, function (World $world, CompoundTag $nbt): TeleportEntity {
            return new TeleportEntity(EntityDataHelper::parseLocation($nbt, $world), TeleportEntity::parseSkinNBT($nbt), $nbt);
        }, ['TeleportEntity']);

        EntityFactory::getInstance()->register(SpawnEntity::class, function (World $world, CompoundTag $nbt): SpawnEntity {
            return new SpawnEntity(EntityDataHelper::parseLocation($nbt, $world), SpawnEntity::parseSkinNBT($nbt), $nbt);
        }, ['SpawnEntity']);

        EntityFactory::getInstance()->register(ShopEntity::class, function (World $world, CompoundTag $nbt): ShopEntity {
            return new ShopEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['ShopEntity']);

        EntityFactory::getInstance()->register(RepairEntity::class, function (World $world, CompoundTag $nbt): RepairEntity {
            return new RepairEntity(EntityDataHelper::parseLocation($nbt, $world), RepairEntity::parseSkinNBT($nbt), $nbt);
        }, ['RepairEntity']);

        EntityFactory::getInstance()->register(InfoEntity::class, function (World $world, CompoundTag $nbt): InfoEntity {
            return new InfoEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['InfoEntity']);

        EntityFactory::getInstance()->register(FireworksRocket::class, static function (World $world, CompoundTag $nbt): FireworksRocket {
            return new FireworksRocket(EntityDataHelper::parseLocation($nbt, $world), Item::nbtDeserialize($nbt->getCompoundTag("Item")));
        }, ["FireworksRocket", EntityIds::FIREWORKS_ROCKET]);
    }
}   
