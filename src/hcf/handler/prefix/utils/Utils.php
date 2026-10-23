<?php

declare(strict_types=1);

namespace hcf\handler\prefix\utils;

use hcf\HCFLoader;
use hcf\player\Player;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

final class Utils
{
    
    /**
     * @param Player $player
     * @return CompoundTag
     */
    public static function createBasicNBT(Player $player): CompoundTag
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
        return $nbt;
    }

    /**
     * @param Player $player
     */
    public static function openPrefixMenu(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents([
            0 => self::getItem(241, 1),
            1 => self::getItem(241, 1),
            7 => self::getItem(241, 1),
            8 => self::getItem(241, 1),
            9 => self::getItem(241, 1),
            17 => self::getItem(241, 1),
            36 => self::getItem(241, 1),
            44 => self::getItem(241, 1),
            45 => self::getItem(241, 1),
            46 => self::getItem(241, 1),
            52 => self::getItem(241, 1),
            53 => self::getItem(241, 1)
        ]);
        
        $menu->getInventory()->setItem(21, self::getItem(399)->setCustomName(TextFormat::colorize('&l&cEspecial Prefixes')));
        $menu->getInventory()->setItem(22, self::getItem(138)->setCustomName(TextFormat::colorize('&l&bChristmas Prefixes')));
        $menu->getInventory()->setItem(23, self::getItem(86)->setCustomName(TextFormat::colorize('&l&6Hallowen Prefixes')));
        $menu->getInventory()->setItem(30, self::getItem(131)->setCustomName(TextFormat::colorize('&l&7Normal Prefixes')));
        $menu->getInventory()->setItem(32, self::getItem(357)->setCustomName(TextFormat::colorize('&l&eCountry Prefixes')));
        $menu->getInventory()->setItem(49, self::getItem(351, 1, 1)->setCustomName(TextFormat::colorize('&l&4Quitar Prefix')));
        
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            $type = TextFormat::clean($item->getCustomName());
            
            if ($item->getCustomName() === TextFormat::colorize('&l&4Quitar Prefix')) {
                $player->getSession()->setPrefix(null);
                $player->sendMessage(TextFormat::colorize("&cAcabas de quitar tu prefix"));
                $player->removeCurrentWindow();
            }
            
            if (self::getPage($type) !== null) {
                self::openPagePrefixMenu($player, $type);
                $player->removeCurrentWindow();
            }
            return $transaction->discard();
        });
        $menu->send($player, TextFormat::colorize('&7Prefix Menu'));
    }
    
    /**
     * @param Player $player
     * @param string $type
     */
    public static function openPagePrefixMenu(Player $player, string $type): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->getInventory()->setContents([
            0 => self::getItem(241, 1),
            1 => self::getItem(241, 1),
            7 => self::getItem(241, 1),
            8 => self::getItem(241, 1),
            9 => self::getItem(241, 1),
            17 => self::getItem(241, 1),
            36 => self::getItem(241, 1),
            44 => self::getItem(241, 1),
            45 => self::getItem(241, 1),
            46 => self::getItem(241, 1),
            52 => self::getItem(241, 1),
            53 => self::getItem(241, 1)
        ] + self::getPage($type));
        
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            /** @var Player $player */
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();
            
            if ($item->getNamedTag()->getTag('price') !== null) {
                $newItem = $item->setLore([]);
                
                if ($item->getCustomName() === TextFormat::colorize('&l&cCheater &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Cheater")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Cheater");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &cCheater"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&4SIMP &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("SIMP")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("SIMP");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &4&lSIMP"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&4Banned &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Banned")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Banned");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &4&lBanned"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&8Mono &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Mono")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Mono");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&8Mono"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&1F&fR&cA &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("FRA")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("FRA");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&1F&fR&cA &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&1U&cS&fA &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("USA")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("USA");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&1U&cS&fA &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&cE&6S&cP &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("ESP")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("ESP");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&cE&6S&cP &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&cP&fE&cR&fU &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("PERU")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("PERU");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&cP&fE&cR&fU &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&eE&1C&cU &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("ECU")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("ECU");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&eE&1C&cU &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&eC&1O&cL &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("COL")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("COL");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&eC&1O&cL &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&cB&eL&aV &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("BLV")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("BLV");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&cB&eL&aV &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&9C&fH&cL &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("CHL")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("CHL");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&9C&fH&cL &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&eV&1N&cZ &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("VNZ")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("VNZ");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&eV&1N&cZ &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&aM&fE&cX &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("MEX")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("MEX");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&aM&fE&cX &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&cWWW &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("WWW")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("WWW");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&cWWW &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&cLmao &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Lmao")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Lmao");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&cLmao &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&dCringe &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Cringe")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Cringe");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&dCringe &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&eBoZo &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("BoZo")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("BoZo");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&eBoZo &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&d<3 &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Corazon")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Corazon");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&d<3 &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&cNUB &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("NUB")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("NUB");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&cNUB &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&9Noob &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Noob")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Noob");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&9Noob &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&2Toxic &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Toxic")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Toxic");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&9C&fH&cL &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&8Hax &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Hax")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Hax");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&8Hax &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&6GOD &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("GOD")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("GOD");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&6GOD &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&6Hallowen &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Hallowen")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Hallowen");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&6Hallowen &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
                
                if ($item->getCustomName() === TextFormat::colorize('&l&bChristmas &7Prefix')) {
                    $prefixPermission = HCFLoader::getInstance()->getPrefixManager()->getPrefix("Christmas")->getPermission();
                    if (!$player->hasPermission($prefixPermission)) {
                        $player->sendMessage(TextFormat::colorize("&cYou do not have permission to use this item"));
                        $player->removeCurrentWindow();
                    } elseif ($player->hasPermission($prefixPermission)) {
                        $player->getSession()->setPrefix("Christmas");
                        $player->sendMessage(TextFormat::colorize("&aTe has colocado el prefix &l&bChristmas &7Prefix"));
                        $player->removeCurrentWindow();
                    }
                }
            }
            return $transaction->discard();
        });
        $menu->setInventoryCloseListener(function (Player $player, $inventory) {
            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($player): void {
                if ($player->isOnline())
                    self::openPrefixMenu($player);
            }), 1);
        });
        $menu->send($player, TextFormat::colorize('&7' . $type));
    }
    
    /**
     * @param Item $item
     * @param int $price
     * @return Item
     */
    private static function prepareItem(Item $item, int $price): Item
    {
        $namedtag = $item->getNamedTag();
        $namedtag->setInt('price', $price);
        $item->setNamedTag($namedtag);
        
        return $item;
    }
    
    /**
     * @param string $type
     * @return array|null
     */
    private static function getPage(string $type): ?array
    {
        switch ($type) {
            case 'Especial Prefixes':
                return [
                    22 => self::prepareItem(self::getItem(351, 1, 1), 40)->setCustomName(TextFormat::colorize('&l&cCheater &7Prefix')),
                    30 => self::prepareItem(self::getItem(351, 5, 1), 40)->setCustomName(TextFormat::colorize('&l&4SIMP &7Prefix')),
                    31 => self::prepareItem(self::getItem(351, 4, 1), 40)->setCustomName(TextFormat::colorize('&l&4Banned &7Prefix')),
                    32 => self::prepareItem(self::getItem(351, 8, 1), 40)->setCustomName(TextFormat::colorize('&l&8Mono &7Prefix'))
                ];
                
            case 'Country Prefixes':
                return [
                    20 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&aM&fE&cX &7Prefix')),
                    21 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&eV&1N&cZ &7Prefix')),
                    22 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&9C&fH&cL &7Prefix')),
                    23 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&cB&eL&aV &7Prefix')),
                    24 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&eC&1O&cL &7Prefix')),
                    29 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&eE&1C&cU &7Prefix')),
                    30 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&cP&fE&cR&fU &7Prefix')),
                    31 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&cE&6S&cP &7Prefix')),
                    32 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&1U&cS&fA &7Prefix')),
                    33 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&1F&fR&cA &7Prefix'))
                ];
                
            case 'Christmas Prefixes':
                return [
                    21 => self::prepareItem(self::getItem(241, 9, 1), 10)->setCustomName(TextFormat::colorize('&l&bChristmas &7Prefix'))
                ];
                
            case 'Hallowen Prefixes':
                return [
                    21 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&6Hallowen &7Prefix'))
                ];
                
            case 'Normal Prefixes':
                return [
                    20 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&6GOD &7Prefix')),
                    21 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&8Hax &7Prefix')),
                    22 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&2Toxic &7Prefix')),
                    23 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&9Noob &7Prefix')),
                    24 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&cNUB &7Prefix')),
                    29 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&d<3 &7Prefix')),
                    30 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&eBoZo &7Prefix')),
                    31 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&dCringe &7Prefix')),
                    32 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&cLmao &7Prefix')),
                    33 => self::prepareItem(self::getItem(351, 1, 1), 80)->setCustomName(TextFormat::colorize('&l&cWWW &7Prefix'))
                ];
        }
        return null;
    }

    public static function getItem($id, $meta = 0, $count = 1): Item {
        return LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}")->setCount($count);
    }
}
