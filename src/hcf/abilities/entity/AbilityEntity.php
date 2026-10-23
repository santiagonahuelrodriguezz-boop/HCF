<?php

namespace hcf\abilities\entity;

use hcf\HCFLoader;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\block\utils\DyeColor;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\ItemFlags;
use hcf\kohi\Kohi;
use partneritems\Loader;

class AbilityEntity extends Human
{

    /** @var int|null */

    /**
     * @param Player $player
     *
     * @return AbilityEntity
     */
    public static function create(Player $player): self
    {
        $nbt = CompoundTag::create()
            ->setTag("Pos", new ListTag([
                new DoubleTag($player->getLocation()->x),
                new DoubleTag($player->getLocation()->y),
                new DoubleTag($player->getLocation()->z)
            ]))
            ->setTag("Motion", new ListTag([
                new DoubleTag($player->getMotion()->x),
                new DoubleTag($player->getMotion()->y),
                new DoubleTag($player->getMotion()->z)
            ]))
            ->setTag("Rotation", new ListTag([
                new FloatTag($player->getLocation()->yaw),
                new FloatTag($player->getLocation()->pitch)
            ]));
        return new self($player->getLocation(), $player->getSkin(), $nbt);
    }

    /**
     * @param int $currentTick
     *
     * @return bool
     */
    public function onUpdate(int $currentTick): bool
    {
        $parent = parent::onUpdate($currentTick);

        $this->setNameTag("§gABILITIES  \n §7Click on the NPC to open the menu");
        $this->setNameTagAlwaysVisible(true);

        return $parent;
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $source->cancel();

        if (!$source instanceof EntityDamageByEntityEvent) {
            return;
        }

        $damager = $source->getDamager();

        if (!$damager instanceof Player) {
            return;
        }
        if ($damager->hasPermission('npc.command') && $damager->getInventory()->getItemInHand()->getCustomName() === "§eRemove NPC §r§7(Right Click)") {
                    $this->kill();
                    return;
                }
               
#=======================================
        $shop = '§r§6Purchase at §c> '.Kohi::TEBEX.' <';
        $enchantment = new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3);

        # PearlDissabler
        $disabler = VanillaItems::AMETHYST_SHARD()->addEnchantment($enchantment);
        $disabler->setCustomName("§sPearl Disabler");
        $disabler->setLore([
            "",
            "§l§aINFO:§r",
            
            "§6Hit your enemy with this ability to disable their ender pearls",
            "§a",
            "§cStore: §s$shop"
        ]);
        $disabler->getNamedTag()->setString("Abilities", "PearlDisabler");
        
        # TimeWarp
        $timewarp = VanillaItems::BRICK()->addEnchantment($enchantment);

        $timewarp->setCustomName("§uTime Warp");

        $timewarp->setLore([
            "",
            "§l§aINFO:§r",
            "§6Hit your enemy with this ability to disable their ender pearls",
            "§a",
            "§cStore: §s$shop"
        ]);
        $timewarp->getNamedTag()->setString("Abilities", "TimeWarp");
        
        # PumpKin
        $pumpkin = VanillaItems::GOLDEN_AXE()->addEnchantment($enchantment);
        $pumpkin->setCustomName("§gPumpKin");
        $pumpkin->setLore([
            "",
            "§l§aINFO:§r",
            "§6Duration of 6 seconds",
            "§6Hit your enemy with this item and it will change their helmet into a melon",
            "§a",
            "§cStore: §s$shop"
        ]);
        $pumpkin->getNamedTag()->setString("Abilities", "PumpKin");

        # AntiTrapper Bone
        $exoticbone = VanillaItems::BONE()->addEnchantment($enchantment);
        $exoticbone->setCustomName("§6Antitraper Bone");
        $exoticbone->setLore([
            "",
            "§l§aINFO:§r",
            "§6Hit a player 3 times in a row",
            "§6to prevent them placing/breaking blocks",
            "§6or interacting with openables for 20 seconds",
            "§a",
            "§cStore: §s$shop"
        ]);
        $exoticbone->getNamedTag()->setString("Abilities", "Bone");

        # Effects Disabler
        $effectdisabler = VanillaItems::SLIMEBALL()->addEnchantment($enchantment);
        $effectdisabler->setCustomName("§6Effects Disabler");
        $effectdisabler->setLore([
            "",
            "§l§aINFO:§r",
            "§6Hit a player with the item",
            "§6to clear the effects of the other player!",
            "§a",
            "§cStore: §s$shop"
        ]);
        $effectdisabler->getNamedTag()->setString("Abilities", "EffectDisabler");

        # Samurai
        $samurai = VanillaItems::DIAMOND_SWORD()->addEnchantment($enchantment);
        $samurai->setCustomName("§5Samurai§r");
        $samurai->setLore([
            "",
            "§l§aINFO:§r",
            "§r§6Use this item to get Samurai Strength II",
            "§r§6and Speed III for seconds",
            "§a",
            "§cStore §s$shop"
        ]);
        $samurai->getNamedTag()->setString("Abilities", "Samurai");

        # Risky Mode
        $riskymode = VanillaItems::DYE()->setColor(DyeColor::ORANGE())->addEnchantment($enchantment);
        $riskymode->setCustomName("§eRisky Mode§r");
        $riskymode->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this item to get Strength III for a few seconds",
            "§a",
            "§cStore: §s$shop"
        ]);
        $riskymode->getNamedTag()->setString("Abilities", "RiskyMode");

        # Focus Mode
        $focusmode = VanillaItems::GOLD_NUGGET()->addEnchantment($enchantment);
        $focusmode->setCustomName("§bFocus Mode");
        $focusmode->setLore([
            "",
            "§l§aINFO:§r",
            "§6Increases enemy damage by 30%",
            "§a",
            "§cStore: §s$shop"
        ]);
        $focusmode->getNamedTag()->setString("Abilities", "FocusMode");

        # Portable Bard
        $portalebard = VanillaItems::ZOMBIE_SPAWN_EGG()->addEnchantment($enchantment);
        $portalebard->setCustomName("§aPortable Bard");
        $portalebard->setLore([
            "",
            "§l§aINFO:§r",
            "§6Using the spawn egg will create a witch and they hold negative effects to your enemies!",
            "§a",
            "§cStore: §s$shop"
        ]);
        $portalebard->getNamedTag()->setString("Abilities", "PortableBard");
        
        # Pre Pearl
        $prepearl = VanillaItems::SHULKER_SHELL()->addEnchantment($enchantment);
        $prepearl->setCustomName("§sPre Pearl");
        $prepearl->setLore([
            "",
            "§l§aINFO:§r",
            "§6This item will remove the enderpearl cooldown",
            "§6to give you a second chance",
            "",
            "§cStore: §s$shop"
        ]);
        $prepearl->getNamedTag()->setString("Abilities", "PrePearl");

        
        # Switcher
        $switcher = VanillaItems::SNOWBALL()->addEnchantment($enchantment);
        $switcher->setCustomName("§bSwitcher");
        $switcher->setLore([
            "",
            "§a§lINFO:§r",
            "§6Throw this snowball to your enemy to change",
            "§6your position with him",
            "§f",
            "§cStore: §s$shop"
        ]);
        $switcher->getNamedTag()->setString("Abilities", "Switcher");
        
        # Strength II
        $strength = VanillaItems::BLAZE_POWDER()->addEnchantment($enchantment);
        $strength->setCustomName("§cStrength II");
        $strength->setLore([
            "",
            "§l§aINFO:§r",
            "§6This item will give you strength II",
            "§6for a few seconds",
            "§3",
            "§cStore: §s$shop"
        ]);
        $strength->getNamedTag()->setString("Abilities", "Strength");

        # Speed
        $speed = VanillaItems::SUGAR()->addEnchantment($enchantment);
        $speed->setCustomName("§bSpeed");
        $speed->setLore([
            "",
            "§l§aINFO:§r",
            "§6This item will give you Speed II for",
            "§6a few seconds",
            "§f",
            "§cStore: §s$shop"
        ]);
        $speed->getNamedTag()->setString("Abilities", "Speed");

        # MedKit
        $medkit = VanillaItems::PAPER()->addEnchantment($enchantment);
        $medkit->setCustomName("§GMedKit");
        $medkit->setLore([
            "§l§aINFO:§r",
            "§6This item will give you Regeneration II for",
            "§6a few seconds",
            "§f",
            "§cStore: §s$shop"
        ]);
        $medkit->getNamedTag()->setString("Abilities", "MedKit");
        
       # Regeneration
        $regeneration = VanillaItems::MELON()->addEnchantment($enchantment);
        $regeneration->setCustomName("§4Regeneration");
        $regeneration->setLore([
            "",
            "§l§aINFO:§r",
            "§6This item will give you Regeneration II for",
            "§6a few seconds",
            "§f",
            "§cStore: §s$shop"
        ]);
        $regeneration->getNamedTag()->setString("Abilities", "Regeneration");

      # ballofrange
        $ballofrange= VanillaItems::EGG()->addEnchantment($enchantment);
        $ballofrange->setCustomName("§cBall of Range");
        $ballofrange->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability to have Strength and give negative effects to your enemy. ",
            "§6For a few seconds ",
            "§f",
            "§cStore: §s$shop"
        ]);
        $ballofrange->getNamedTag()->setString("Abilities", "BallOfRange");

        # Rebirth
        $cookie = VanillaItems::COOKIE()->addEnchantment($enchantment);
        $cookie->setCustomName("§gRebirth");
        $cookie->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability and when you have 3 hearts it regenerates you",
            "§3",
            "§cStore §s$shop"
        ]);
        $cookie->getNamedTag()->setString("Abilities", "Rebirth");

        # Combo
        $combo = VanillaItems::PUFFERFISH()->addEnchantment($enchantment);
        $combo->setCustomName("§gCombo Ability");
        $combo->setLore([
            "",
            "§l§aINFO:§r",
            "§6After 10 hits you will have strength 2",
            "§f",
            "§cStore: §s$shop"
        ]);
        $combo->getNamedTag()->setString("Abilities", "ComboAbility");

        # Berserk
        $berserk = VanillaItems::DYE()->setColor(DyeColor::RED())->addEnchantment($enchantment);
        $berserk->setCustomName("§gBerserk");
        $berserk->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability to have Strength 2, speed and resistance ",
            "§f",
            "§cStore: §3$shop"
        ]);
        $berserk->getNamedTag()->setString("Abilities", "Berserk");

        # JumpBoost
        $jumpboost = VanillaItems::FEATHER()->addEnchantment($enchantment);
        $jumpboost->setCustomName("§aJump Boost");
        $jumpboost->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this skill to have a jumping effect ",
            "§f",
            "§cStore: §s$shop"
        ]);
        $jumpboost->getNamedTag()->setString("Abilities", "JumpBoost");

        # Potion Refill
        $potionrefill = VanillaItems::GLASS_BOTTLE()->addEnchantment($enchantment);
        $potionrefill->setCustomName("§bPotion Refill");
        $potionrefill->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability to fill your inventory of passions",
            "§f",
            "§cStore: §3$shop"
        ]);
        $potionrefill->getNamedTag()->setString("Abilities", "PotionRefill");

        # Ninja Star
        $ninjastar = VanillaItems::NETHER_STAR()->addEnchantment($enchantment);
        $ninjastar->setCustomName("§3Ninja Star");
        $ninjastar->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability to teleport towards your enemy.",
            "§f",
            "§cStore: §s$shop"
        ]);
        $ninjastar->getNamedTag()->setString("Abilities", "NinjaStar");

        #Starving Flesh
   $flesh = VanillaItems::ROTTEN_FLESH()->addEnchantment($enchantment);        $flesh->setCustomName("§gStarving Flesh");
        $flesh->setLore([
            "§f",
            "§g Hit your enemy with this item to make them hungry.",
            "§f",
            "§cStore §s$shop"
        ]);
        $flesh->getNamedTag()->setString("Abilities", "StarvingFlesh");

        # ReverseNinja
        $reverseninja = VanillaItems::SHEARS()->addEnchantment($enchantment);
        $reverseninja->setCustomName("§dReverse Ninja");
        $reverseninja->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability to teleport your enemy towards you.",
            "§f",
            "§cStore: §s$shop"
        ]);
        $reverseninja->getNamedTag()->setString("Abilities", "ReverseNinja");

        # StormBreaker
        $stormbreaker = VanillaItems::STONE_AXE()->addEnchantment($enchantment);
        $stormbreaker->setCustomName("§eStorm Breaker");
        $stormbreaker->setLore([
            "",
            "§l§aINFO:§r",
            "§6Hit your enemy to remove his helmet",
            "§f",
            "§cStore: §$shop"
        ]);
        $stormbreaker->getNamedTag()->setString("Abilities", "StormBreaker");
        
                 # Debuff
        $debuff = VanillaItems::SPIDER_EYE()->addEnchantment($enchantment);
        $debuff->setCustomName("§4Debuff");
        $debuff->setLore([
            "",
            "§l§aINFO:§r",
            "§6Hit your enemy to give them negative effects",
            "§f",
            "§cStore: §$shop"
        ]);
        $debuff->getNamedTag()->setString("Abilities", "Debuff");
        
         # Cactus
        $cactus = VanillaBlocks::CACTUS()->asItem()->addEnchantment($enchantment);
        $cactus->setCustomName("§3Cactus");
        $cactus->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this item to gain Strength, Toughness, and other effects.",
            "§f",
            "§cStore: §$shop"
        ]);
        $cactus->getNamedTag()->setString("Abilities", "Cactus");
        
         # Cocaine
        $cocaine = VanillaItems::GLOWSTONE_DUST()->addEnchantment($enchantment);
        $cocaine->setCustomName("§fCocaine");
        $cocaine->setLore([
            "",
            "§l§aINFO:§r",
            "§6This ability gives you strength and other effects.",
            "§f",
            "§cStore: §$shop"
        ]);
        $cocaine->getNamedTag()->setString("Abilities", "Cocaine");
        
         # Gold
        $gold = VanillaItems::GOLD_INGOT()->addEnchantment($enchantment);
        $gold->setCustomName("§eGold");
        $gold->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability to gain Strength, Regeneration, and other effects. ",
            "§f",
            "§cStore: §$shop"
        ]);
        $gold->getNamedTag()->setString("Abilities", "Gold");
        
         # Resistance
        $resis = VanillaItems::IRON_INGOT()->addEnchantment($enchantment);
        $resis->setCustomName("§b Resistance III");
        $resis->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this skill to have 3 resistance for a few seconds ",
            "§f",
            "§cStore: §$shop"
        ]);
        $resis->getNamedTag()->setString("Abilities", "Resistance");
       
        # InventoryClogger 
        $clogger = VanillaItems::WOODEN_HOE()->addEnchantment($enchantment);
        $clogger->setCustomName("§gInventoryClogger");
        $clogger->setLore([
            "",
            "§l§aINFO:§r",
            "§6Hit your enemy to mess up their inventory.",
            "§f",
            "§cStore: §$shop"
        ]);
        $clogger->getNamedTag()->setString("Abilities", "InventoryClogger");
        
        # BeaconAntiTrap
        $beacon = VanillaBlocks::BEACON()->asItem()->addEnchantment($enchantment);
        $beacon->setCustomName("§bAnti Trap");
        $beacon->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability on your enemies' claim ",
            "§f",
            "§cStore: §$shop"
        ]);

        $beacon->getNamedTag()->setString("Abilities", "BeaconAntiTrap");

       # Prepearl
        $prepearl = VanillaItems::PHANTOM_MEMBRANE()->addEnchantment($enchantment);
        $prepearl->setCustomName("§1PrePearl");
        $prepearl->setLore([
            "",
            "§l§aINFO:§r",
            "§6Use this ability to teleport to the location where the act is performed.",
            "§f",
            "§cStore: §$shop"
        ]);
        $prepearl->getNamedTag()->setString("Abilities", "PrePearl"); 
        
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName("§l§g» §r§5PartnerItems §f§l«");
        $items = [
           $timewarp, $pumpkin, $exoticbone, $effectdisabler, $samurai, $riskymode, $focusmode,

            $portalebard, $switcher, $strength,

            $jumpboost, $speed, $regeneration, $potionrefill, $reverseninja, $ninjastar,

            $stormbreaker, $medkit, $cookie, $ballofrange, $berserk, $combo, $flesh, $cactus, $debuff, $cocaine, $resis, $gold, $prepearl, $beacon, $clogger, $disabler, 
        ];
                   
        foreach ($items as $index => $item) {
    $menu->getInventory()->setItem($index, $item);
}

$menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
    return $transaction->discard();
});

$menu->send($damager);
    }
}