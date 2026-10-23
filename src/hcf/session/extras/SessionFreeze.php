<?php
namespace hcf\session\extras;

use pocketmine\player\Player;

class SessionFreeze
{
    private static array $freeze;

    public static function removeFreeze(Player $player):void
    {
        unset(self::$freeze[$player->getName()]);
    }

    public static function addFreeze(Player $player):void
    {
        self::$freeze[$player->getName()] = $player->getPosition();
    }

    public static function isFreeze(Player $player):bool
    {
        return isset(self::$freeze[$player->getName()]);
    }

    public static function hitFreeze(Player $player):void
    {
        if(self::isFreeze($player))
        $player->teleport(self::$freeze[$player->getName()]);
    }
}