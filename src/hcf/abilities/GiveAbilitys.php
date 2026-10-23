<?php

declare(strict_types=1);

namespace hcf\abilities;

use hcf\HCFLoader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTest;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\ice;

/**
 * Class AbilitiesCommand
 * @package hcf\abilities
 */
class GiveAbilitys extends Command
{
    public function __construct()
    {
        parent::__construct('giveabilitys', '');
		$this->setPermission("abilities.command.give.use");
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
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

       /* //----------------------------------FairFight----------------------------------
        $FairFight = VanillaItems::DYE()->setColor(DyeColor::GRAY());
        $FairFight->setCustomName("§r§l§7Fair §fFight");
        $FairFight->setLore([
        "§r§f",
        "§r§7Use this item to weaken your nearest opponent.",
        "§r§7It will temporarily reduce their armor to §cProtection I",
        "§r§7for §f10 seconds§7, making fights fairer!",
        "§r§a",
        "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')
        ]);
        $FairFight->getNamedTag()->setString("Abilities", "FairFight");
        //---------------------------------------------------------
        
        //------------------RiskyMode----------------
        $Risky = VanillaItems::GOLD_NUGGET();
        $Risky->setCustomName("§r§l§gRiskyMode");
        $Risky->setLore([
        "§r§f",
        "§r§7Gives you strength 3 for 7 seconds",
        "§r§a",
        "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Risky->getNamedTag()->setString("Abilities","RickyMode");
        //------------------------------------------*/

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

        /*//------------------Invisiblity----------------
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
        //------------------------------------------*/

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
        
        //------------------PearlCooldown----------------
        $PearlCooldown = VanillaItems::STICK(); // Usamos un palo
        $PearlCooldown->setCustomName("§r§l§7AntiPerl"); // Nombre visible en el inventario
        $PearlCooldown->setLore([
        "§r",
        "§r§7Hit a player with this stick",
        "§r§7to prevent them from using Ender Pearls for 15 seconds!",
        "§r§f",
        "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')
        ]);
        $PearlCooldown->getNamedTag()->setString("Abilities","PearlCooldown"); // Variable de la ability
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

       /* //------------------regeneration----------------
        $Regeneration = self::getItem(370, 1);
        $Regeneration->setCustomName("§r§l§6Regeneration");
        $Regeneration->setLore([
            "§r§2",
            "§r§7this item will give you Regeneration III for",
            "§r§7a 12 seconds",
            "§r§f",
            "§r§ePurcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Regeneration->getNamedTag()->setString("Abilities","Regeneration");
        //------------------------------------------*/

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
        $FireWork = self::getItem(401, 1);
        $FireWork->setCustomName("§r§l§3Firework");
        $FireWork->setLore([
            "§r§f",
            "§r§7This is the item capable of rising like",
            "§r§7the firework so you can escape from enemies",
            "§r§a",
            "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $FireWork->getNamedTag()->setString("Abilities","Firework");
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
        $CloseCall = VanillaItems::APPLE();
        $CloseCall->setCustomName("§r§l§cApple Legend");
        $CloseCall->setLore([
            "§r§f",
            "§r§7Right click to use",
            "§r§7oOnly use it when",
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
        
       /* //------------------Sphere------------------
        $Sphere = self::getItem(106, 1);
        $Sphere->setCustomName("§r§7(§6!§7)§6 Sphere");
        $Sphere->setLore([
            "§r§f",
            "§r§7Hit a player for Generate a Glowstone Sphere ",
            "§r§7for 10 seconds",
            "§r§a",
            "§r§6Purcharse at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')]);
        $Sphere->getNamedTag()->setString("Abilities","Sphere");
        //------------------------------------------*/
        
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
        
       /* //------------------Brick----------------
        $Brick = VanillaItems::BRICK();
        $Brick->setCustomName("§r§l§cBrick");
        $Brick->setLore([
        "§r§f",
        "§r§7Right click to gain:",
        "§r§7Strength II, Resistance II,",
        "§r§7& Speed III (7 seconds)",
        "§r§a",
        "§r§eCooldown: §c1m",
        "§r§ePurchase at §b" . HCFLoader::getInstance()->getConfig()->get('tebex')
        ]);
        $Brick->getNamedTag()->setString("Abilities","Brick");
        //------------------------------------------*/

        if ($sender instanceof Player) {
            $sender->getInventory()->addItem($FocusMode);
            //$sender->getInventory()->addItem($Invi);
            $sender->getInventory()->addItem($Refill);
            //$sender->getInventory()->addItem($Risky);
            $sender->getInventory()->addItem($Samurai);
            $sender->getInventory()->addItem($SullCrat);
            $sender->getInventory()->addItem($BallOfRage);
            $sender->getInventory()->addItem($AbilityDisabler);
            $sender->getInventory()->addItem($ExoticBone);
            $sender->getInventory()->addItem($FireWork);
            $sender->getInventory()->addItem($EffectDisabler);
            $sender->getInventory()->addItem($FrezzerGun);
            $sender->getInventory()->addItem($Berserk);
            $sender->getInventory()->addItem($SecondChance);
            $sender->getInventory()->addItem($Switcher);
            //$sender->getInventory()->addItem($Regeneration);
            $sender->getInventory()->addItem($PearlCooldown);
            $sender->getInventory()->addItem($Resistance);
            $sender->getInventory()->addItem($FlowerTank);
            $sender->getInventory()->addItem($Strength);
            $sender->getInventory()->addItem($NinjaStar);
            $sender->getInventory()->addItem($StromBreaker);
            $sender->getInventory()->addItem($freeze);
            $sender->getInventory()->addItem($CloseCall);
            $sender->getInventory()->addItem($Melonite);
            $sender->getInventory()->addItem($Pumpkin);
            $sender->getInventory()->addItem($MedKit);
            //$sender->getInventory()->addItem($Combo);
           // $sender->getInventory()->addItem($Sphere);
            //$sender->getInventory()->addItem($FairFight);
            $sender->getInventory()->addItem($LifeSaver);
            $sender->getInventory()->addItem($hogrider);
            $sender->getInventory()->addItem($Cactus);
           // $sender->getInventory()->addItem($Brick);
        }

    }

    public static function getItem($id, $meta = 0, $count = 1): Item {
        return LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}")->setCount($count);
    }

}