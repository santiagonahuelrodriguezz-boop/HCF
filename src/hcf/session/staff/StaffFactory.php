<?php

namespace hcf\session\staff;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use hcf\session\staff\{Session, StaffUtils};

class StaffFactory {

    /** @var Session[] */
    private static array $session = [];
    /** @var array */
    private static array $items = [];
    private static array $offhand = [];
    private static array $armor = [];
    private static array $vanish = [];
    private static array $freeze = [];
    private static array $staffchat = [];

    public static function register(Player $player): void
    {
        if (!isset(self::$session[$player->getName()])) {
            self::$session[$player->getName()] = new Session($player);

            
            self::$session[$player->getName()]->setPosition($player->getPosition());

            self::$items[$player->getName()] = $player->getInventory()->getContents();
            self::$offhand[$player->getName()] = $player->getOffHandInventory()->getContents();
            self::$armor[$player->getName()] = $player->getArmorInventory()->getContents();
            $player->getInventory()->clearAll();
            $player->getOffHandInventory()->clearAll();
            $player->getArmorInventory()->clearAll();
            StaffUtils::sendKit($player);
        }
    }

    public static function unregister(Player $player): void
{
    if (isset(self::$session[$player->getName()])) {
        
        $position = self::$session[$player->getName()]->getPosition();
        if ($position instanceof Vector3) {
            $player->teleport($position);
        }
        
        $player->getInventory()->clearAll();
        $player->getOffHandInventory()->clearAll();
        $player->getArmorInventory()->clearAll();
        unset(self::$session[$player->getName()]);
     
        if (isset(self::$items[$player->getName()])) {
            $player->getInventory()->setContents(self::$items[$player->getName()]);
        }
        if (isset(self::$offhand[$player->getName()])) {
            $player->getOffHandInventory()->setContents(self::$offhand[$player->getName()]);
        }
        if (isset(self::$armor[$player->getName()])) {
            $player->getArmorInventory()->setContents(self::$armor[$player->getName()]);
}
        }
    }

    public static function isRegistered(Player $player): bool
    {
        return isset(self::$session[$player->getName()]);
    }

    public static function sendVanish(Player $player): void
    {
        self::$vanish[$player->getName()] = new Session($player);
    }

    public static function isVanish(Player $player): bool
    {
        return isset(self::$vanish[$player->getName()]);
    }

    public static function cancelVanish(Player $player): void
    {
        unset(self::$vanish[$player->getName()]);
    }

    public static function sendFreeze(Player $player): void
    {
        self::$freeze[$player->getName()] = new Session($player);
    }

    public static function isFreeze(Player $player): bool
    {
        return isset(self::$freeze[$player->getName()]);
    }

    public static function cancelFreeze(Player $player): void
    {
        unset(self::$freeze[$player->getName()]);
    }

    public static function sendChat(Player $player): void
    {
        self::$staffchat[$player->getName()] = new Session($player);
    }

    public static function isChat(Player $player): bool
    {
        return isset(self::$staffchat[$player->getName()]);
    }

    public static function cancelChat(Player $player): void
    {
        unset(self::$staffchat[$player->getName()]);
    }
}