<?php

namespace hcf\handler\package;

use hcf\HCFLoader;
use hcf\player\Player;

use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\event\Listener;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\Item;
use pocketmine\block\utils\DyeColor;

class PackageListener implements Listener {

    public static array $items;
    
    public static function givePartner(Player $player, int $int): void {
        $partner = VanillaBlocks::ENDER_CHEST()->asItem()->setCount($int);
        $partner->setCustomName("§aPartnerPackages");
        $partner->setLore(["\n§r§7Right click to receive random §aablitys.\n§ePurchase at §3https://" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $partner->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
        
        $player->getInventory()->addItem($partner);
        $player->sendMessage("§aYou have received a PartnerPackages!");
    }
    
    public function onItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getCustomName() !== "§aPartnerPackages") {
            return;
        }

        $item->setCount($item->getCount() - 1);
        $player->getInventory()->setItemInHand($item);
        
        if (count(self::$items) == 0)
                return;

            if (!$player->getInventory()->canAddItem($this->getRandomItem())) {
                $player->sendMessage("§4Your inventory is full!");
                return;
            }

            $item1 = $this->getRandomItem();
            $player->getInventory()->addItem($item1);

    }
    
    public function onBlockInteract(PlayerInteractEvent $event): void {
        $item = $event->getItem();
        $player = $event->getPlayer();

        if ($item->getCustomName() !== "§aPartnerPackages") {
            return;
        }

        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $event->cancel();
        }
    }

    public function getRandomItem(): Item
    {
        return self::$items[array_rand(self::$items)];
    }

    public static function setItems() {
       //------------------Samurai----------------
        $Samurai = VanillaItems::DIAMOND_SWORD();
        $Samurai->setCustomName("§r§l§1Samurai");
        $Samurai->setLore([
        "§r§f",
        "§r§7Teleport to your enemy and",
        "§r§7Gives you Strength 2 and",
        "§r§7Resistance 2 for 10 Seconds",
        "§r§a",
        "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Samurai->getNamedTag()->setString("Abilities","Samurai");
        //------------------------------------------

        //------------------RiskyMode----------------
        $Risky = VanillaItems::GOLD_NUGGET();
        $Risky->setCustomName("§r§l§gRiskyMode");
        $Risky->setLore([
        "§r§f",
        "§r§7Gives you strength 3 for 7 seconds",
        "§r§a",
        "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Risky->getNamedTag()->setString("Abilities","RickyMode");
        //------------------------------------------

        //------------------PotionRefill----------------
        $Refill = VanillaItems::POTION();
        $Refill->setCustomName("§r§l§bPotionRefill");
        $Refill->setLore([
        "§r§f",
        "§r§7Refills your inventory of instant health potions",
        "§r§a",
        "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Refill->getNamedTag()->setString("Abilities","Potion");
        //------------------------------------------

        //------------------Invisiblity----------------
        $Invi = VanillaItems::INK_SAC();
        $Invi->setCustomName("§r§l§bInvisibility");
        $Invi->setLore([
        "§r§f",
        "§r§7Gives you invisibility for a 10 seconds",
        "§r§a",
        "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Invi->getNamedTag()->setString("Abilities","Invisibility");
        //------------------------------------------

        //------------------ComboAbility----------------
        $Combo = VanillaItems::PUFFERFISH();
        $Combo->setCustomName("§r§l§4Combo Ability");
        $Combo->setLore([
        "§r§f",
        "§r§7Gives you strength 2 for the number of hits you give",
        "§r§a",
        "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Combo->getNamedTag()->setString("Abilities","Combo");
        //------------------------------------------

        //------------------FocusMode----------------
        $FocusMode = VanillaItems::GOLD_NUGGET();
        $FocusMode->setCustomName("§r§l§cFocusMode");
        $FocusMode->setLore([
        "§r§f",
        "§r§7Increase ur strength for a few seconds",
        "§r§a",
        "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $FocusMode->getNamedTag()->setString("Abilities","FocusMode");
        //------------------------------------------

        //------------------SullCrat----------------
        $SullCrat = VanillaItems::FIRE_CHARGE();
        $SullCrat->setCustomName("§r§l§4SullCrat");
        $SullCrat->setLore([
            "§r§f",
            "§r§7Use this item and after 5s",
            "§r§7Change the fence gate for air when it touches it.",
            "§r§a",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $SullCrat->getNamedTag()->setString("Abilities","SullCrat");
        //------------------------------------------

        //------------------ExoticBone----------------
        $ExoticBone = VanillaItems::BONE();
        $ExoticBone->setCustomName("§r§l§bExoticBone");
        $ExoticBone->setLore([
            "§r§f",
            "§r§7Hit a player 3 times in a row",
            "§r§7to prevent them placing/breaking blocks",
            "§r§7or interacting with openables for 20 seconds",
            "§r§a",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $ExoticBone->getNamedTag()->setString("Abilities","ExoticBone");
        //------------------------------------------

        //------------------EffectDisabler----------------
        $EffectDisabler = VanillaItems::SLIMEBALL();
        $EffectDisabler->setCustomName("§r§l§aEffects Disabler");
        $EffectDisabler->setLore([
            "§r",
            "§r§7hit a player with the item",
            "§r§7to clear the effects of the other player!",
            "§r§f",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $EffectDisabler->getNamedTag()->setString("Abilities","EffectDisabler");
        //------------------------------------------

        //------------------PortableBard----------------
        $PortaleBard = VanillaItems::ZOMBIE_SPAWN_EGG()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
        $PortaleBard->setCustomName("§r§l§dPortable Bard");
        $PortaleBard->setLore([
            "§r§f",
            "§r§7Using the spawn egg will create a Bard and they hold positive effects!",
            "§r§a",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $PortaleBard->getNamedTag()->setString("Abilities","PortableBard");
        //------------------------------------------

        //------------------FreezerGun----------------
        $FrezzerGun = VanillaItems::SNOWBALL();
        $FrezzerGun->setCustomName("§r§l§4Demon §bBall");
        $FrezzerGun->setLore([
            "§r§f",
            "§r§7When shooting an enemy, they will be completely frozen for a certain time",
            "§r§a",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $FrezzerGun->getNamedTag()->setString("Abilities","FrezzerGun");
        //------------------------------------------

        //------------------SecondChance----------------
        $SecondChance = self::getItem(280, 1);
        $SecondChance->setCustomName("§r§l§bSecondChance");
        $SecondChance->setLore([
            "§r§f",
            "§r§7This item will remove the enderpearl cooldown",
            "§r§7to give you a second chance",
            "§r§2",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $SecondChance->getNamedTag()->setString("Abilities","SecondChance");
        //------------------------------------------

        //------------------AbilityDisabler----------------
        $AbilityDisabler = VanillaItems::POISONOUS_POTATO();
        $AbilityDisabler->setCustomName("§r§l§aAbility Disabler");
        $AbilityDisabler->setLore([
            "§r§f",
            "§r§7When you use this item every player within",
            "§r§715 blocks of you wont be able",
            "§r§7to use any pp item for 90 seconds",
            "§r§2",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $AbilityDisabler->getNamedTag()->setString("Abilities","AbilityDisabler");
        //------------------------------------------

        //------------------Switcher----------------
        $Switcher = VanillaItems::SNOWBALL();
        $Switcher->setCustomName("§r§l§bSwitcher");
        $Switcher->setLore([
            "§r§a",
            "§r§7thorw this snowball to your enemy to change",
            "§r§7your position with him",
            "§r§f",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Switcher->getNamedTag()->setString("Abilities","Switcher");
        //------------------------------------------

        //------------------Strength----------------
        $Strength = VanillaItems::BLAZE_POWDER();
        $Strength->setCustomName("§r§l§cStrength II");
        $Strength->setLore([
            "§r§f",
            "§r§7this item will give you strength II",
            "§r§7for a 10 seconds",
            "§r§3",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Strength->getNamedTag()->setString("Abilities","Strength");
        //------------------------------------------

        //------------------jumpboost----------------
        $JumpBoost = self::getItem(288, 1);
        $JumpBoost->setCustomName("§r§l§6JumpBoost");
        $JumpBoost->setLore([
            "§r§2",
            "§r§f§7this item will give you Jump Boost VII for",
            "§r§7a 8 seconds",
            "§r§f",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $JumpBoost->getNamedTag()->setString("Abilities","JumpBoost");
        //------------------------------------------

        //------------------speed----------------
        $Speed = self::getItem(353, 1);
        $Speed->setCustomName("§r§l§6Speed");
        $Speed->setLore([
            "§r§2",
            "§r§7this item will give you Speed II for",
            "§r§7a few seconds",
            "§r§f",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Speed->getNamedTag()->setString("Abilities","Speed");
        //------------------------------------------

        //------------------resistance----------------
        $Resistance = VanillaItems::IRON_INGOT();
        $Resistance->setCustomName("§r§l§dResistance III");
        $Resistance->setLore([
            "§r§f",
            "§r§7this item will give you Resistence III for",
            "§r§7a 12 seconds",
            "§r§f",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Resistance->getNamedTag()->setString("Abilities","Resistance");
        //------------------------------------------

        //------------------regeneration----------------
        $Regeneration = self::getItem(370, 1);
        $Regeneration->setCustomName("§r§l§6Regeneration");
        $Regeneration->setLore([
            "§r§2",
            "§r§7this item will give you Regeneration III for",
            "§r§7a 12 seconds",
            "§r§f",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Regeneration->getNamedTag()->setString("Abilities","Regeneration");
        //------------------------------------------

        //------------------BallOfRage----------------
        $BallOfRage = VanillaItems::EGG();
        $BallOfRage->setCustomName("§r§l§cBall of Rage");
        $BallOfRage->setLore([
            "§r§f",
            "§r§7when you throw this egg, the members of your",
            "§r§7faction that are near will receive strength II",
            "§r§7and Resistence III for a few seconds, and the",
            "§r§7enemies will receive Wither II",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $BallOfRage->getNamedTag()->setString("Abilities","BallOfRange");
        //------------------------------------------

        //------------------FireWork----------------
        $FireWork = VanillaItems::FEATHER();
        $FireWork->setCustomName("§r§l§3Firework");
        $FireWork->setLore([
            "§r§f",
            "§r§7This is the item capable of rising like",
            "§r§7the firework so you can escape from enemies",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $FireWork->getNamedTag()->setString("Abilities","Firework");
        //------------------------------------------

        //------------------FireWork2----------------
        $FireWork2 = VanillaItems::FEATHER();
        $FireWork2->setCustomName("§r§l§3Firework");
        $FireWork2->setLore([
            "§r§f",
            "§r§7This is the item capable of rising like",
            "§r§7the firework so you can escape from enemies",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $FireWork2->getNamedTag()->setString("Abilities","Firework");
        //------------------------------------------

    	//----------------FLOWERTANK----------------
        $FlowerTank = self::getItem(38, 0);
        $FlowerTank->setCustomName("§r§l§cFlower Tank");
        $FlowerTank->setLore([
            "§r§f",
            "§r§7Use this item to get Resistance III, Regeneration IV",
            "§r§7and Absorpcion II for 3 seconds",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $FlowerTank->getNamedTag()->setString("Abilities","FlowerTank");
        //------------------------------------------

        //------------------Berserk----------------
        $Berserk = VanillaItems::DYE()->setColor(DyeColor::RED());
        $Berserk->setCustomName("§r§l§cBerserk");
        $Berserk->setLore([
            "§r§f",
            "§r§7Use this item to get Strength II, Resistance III",
            "§r§7and Speed III for 10 seconds",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Berserk->getNamedTag()->setString("Abilities","Berserk");
        //------------------------------------------

        //------------------Ninja Star----------------
        $NinjaStar = VanillaItems::NETHER_STAR();
        $NinjaStar->setCustomName("§r§l§5NinjaStar");
        $NinjaStar->setLore([
            "§r§f",
            "§r§7Teleports you to the last person who",
            "§r§7hit you in the last 15 seconds!",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $NinjaStar->getNamedTag()->setString("Abilities","NinjaStar");
        //------------------------------------------

        //------------------StromBreaker----------------
        $StromBreaker = VanillaItems::STONE_AXE();
        $StromBreaker->setCustomName("§r§l§bStromBreaker");
        $namedTag = $StromBreaker->getNamedTag();
        if(!$namedTag->getTag("uses")){
        $namedTag->setInt("uses", 3); // Default 3 usos
        }
        $usesLeft = $namedTag->getInt("uses");
        $StromBreaker->setLore([
        "§r§7Swap enemy helmet with their hand item.",
        "§r§7Cooldown: §f120s",
        "§r§ePurchase at §3" . HCFLoader::getInstance()->getConfig()->get('tebex'),
        "§r§7Uses left: §f" . $usesLeft
        ]);
        $namedTag->setString("Abilities", "StromBreaker");
        //------------------------------------------

        //------------------Pumpkin----------------
        $Pumpkin = VanillaItems::GOLDEN_AXE();
        $Pumpkin->setCustomName("§r§l§6Pumpkin");
        $usesLeft = $Pumpkin->getNamedTag()->getInt("uses", 5); 
        $Pumpkin->setLore([
            "§r§7Hit an enemy with this item",
            "§r§7and turn their helmet",
            "§r§7into a pumpkin for 7 seconds.",
            "§r§ePurchase at §3" . HCFLoader::getInstance()->getConfig()->get('tebex'),
            "§r§7Uses left: §f" . $usesLeft
        ]);
        $Pumpkin->getNamedTag()->setString("Abilities","Pumpkin");
        //------------------------------------------

        //------------------Close Call----------------
        $CloseCall = VanillaItems::COOKIE();
        $CloseCall->setCustomName("§r§l§bClose Call");
        $CloseCall->setLore([
            "§r§f",
            "§r§7Right click to use",
            "§r§7oOnly use it when you have less than 4 hearts",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $CloseCall->getNamedTag()->setString("Abilities","CloseCall");
        //------------------------------------------

        //------------------Melonite----------------
        $Melonite = VanillaItems::MELON();
        $Melonite->setCustomName("§r§l§dOda YT L Melonite");
        $Melonite->setLore([
            "§r§f",
            "§r§7Use this item to get Resistance III, Regeneration III",
            "§r§7and Absorpcion II for 3 seconds",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Melonite->getNamedTag()->setString("Abilities","Melonite");
        //------------------------------------------


        //------------------WeridLuck----------------
        $WeridLuck = VanillaItems::EMERALD();
        $WeridLuck->setCustomName("§r§l§aWeridLuck");
        $WeridLuck->setLore([
            "§r§f",
            "§r§7When you use this item you will have a probability",
            "§r§7of 50% of having good effects and 50% of having negative effects",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $WeridLuck->getNamedTag()->setString("Abilities","WeridLuck");
        //------------------------------------------

        //------------------Medkit----------------
        $MedKit = VanillaItems::BOOK();
        $MedKit->setCustomName("§r§l§mMedkit");
        $MedKit->setLore([
            "§r§f",
            "§r§7Use this item to get Regeneration V",
            "§r§7and Absorpcion II for 10 seconds",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $MedKit->getNamedTag()->setString("Abilities","Medkit");
        //------------------------------------------

        //------------------Ninja Shear----------------
        $NinjaShear = VanillaItems::SHEARS();
        $NinjaShear->setCustomName("§r§l§8Ninja Shear");
        $NinjaShear->setLore([
            "§r§f",
            "§r§7Teleports you to the last person who",
            "§r§7hit you in the last 15 seconds!",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $NinjaShear->getNamedTag()->setString("Abilities","NinjaShear");
        //------------------------------------------
        
        //------------------Cactus----------------
        $Cactus = VanillaBlocks::CACTUS()->asItem();
        $Cactus->setCustomName("§7[§6!§7] §2Cactus");
        $Cactus->setLore([
        "§2",
        "§7this item will give you Speed III And strength for",
        "§7a few seconds",
        "§f",
        "§ePurchase at §3" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Cactus->getNamedTag()->setString("Abilities","Cactus");
        //------------------------------------------
        
        //------------------HogRider----------------
        $hogrider = VanillaItems::RAW_PORKCHOP()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
        $hogrider->setCustomName("§7[§6!§7] §bHogRider");
        $hogrider->setLore([
            "§f",

            "§7Hit a player 3 times in a row",
            "§7to prevent them placing/breaking blocks",
            "§7or interacting with openables for 20 seconds",
            "§a",
            "§ePurcharse at §3" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $hogrider->getNamedTag()->setString("Abilities","HogRider");
        //------------------------------------------
        
        //------------------Sphere------------------
        $Sphere = self::getItem(106, 1);
        $Sphere->setCustomName("§r§7(§6!§7)§6 Sphere");
        $Sphere->setLore([
            "§r§f",
            "§r§7Hit a player for Generate a Glowstone Sphere ",
            "§r§7for 10 seconds",
            "§r§a",
            "§r§6Purcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Sphere->getNamedTag()->setString("Abilities","Sphere");
        //------------------------------------------
        
        //------------------Freeze----------------
        $freeze = VanillaBlocks::ICE()->asItem()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
        $freeze->setCustomName("§7[§6!§7] §bFreeze");
        $freeze->setLore([
            "§f",
            "§7When shooting an enemy, they will be completely frozen for a certain time",
            "§a",
            "§ePurcharse at §3" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $freeze->getNamedTag()->setString("Abilities","Freeze");
        //------------------------------------------
       
         //------------------Swapper----------------
        $Swapper = VanillaItems::IRON_AXE();
        $Swapper->setCustomName("§r§7(§g!§7)§g Swapper");
        $usesLeft = $Swapper->getNamedTag()->getInt("uses", 5); 
        $Swapper->setLore([
            "§r§7Hit an enemy with this item",
            "§r§7and turn their helmet",
            "§r§7into a helmet for 7 seconds.",
            "§r§6Purchase at §3" . HCFLoader::getInstance()->getConfig()->get('tebex'),
            "§r§7Uses left: §f" . $usesLeft
        ]);
        $Swapper->getNamedTag()->setString("Abilities","Swapper");
        //---------------------------------------------
        
        //------------------Life Saver----------------
        $LifeSaver = VanillaItems::GLOWSTONE_DUST();
        $LifeSaver->setCustomName("§r§7(§g!§7)§g Faction Teleport");
        $LifeSaver->setLore([
            "§r§f",
            "§r§7Teleports you to the nearest",
            "§r§7faction member within 30 blocks",
            "§r§a",
            "§r§6Purchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $LifeSaver->getNamedTag()->setString("Abilities","LifeSaver");
        //------------------------------------------
      self::$items = [
      $FocusMode,
      $Invi,
      $Refill,
      $Risky,
      $Samurai,
      $SullCrat,
      $BallOfRage,
      $AbilityDisabler,
      $ExoticBone,
      $FireWork,
      $EffectDisabler,
      $PortaleBard,
      $FrezzerGun,
      $Berserk,
      $SecondChance,
      $Switcher,
      $JumpBoost,
      $Regeneration,
      $Resistance,
      $Speed,
      $FlowerTank,
      $Strength,
      $freeze,
      $NinjaStar,
      $StromBreaker,
      $FireWork2,
      $CloseCall,
      $Melonite,
      $Pumpkin,
      $Swapper,
      $NinjaShear,
      $WeridLuck,
      $MedKit,
      $Combo,
      $LifeSaver,
      $hogrider,
      $Sphere,
      $Cactus,];
    }
    
    public static function getItem($id, $meta = 0, $count = 1): Item {
        return LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}")->setCount($count);
    }
}