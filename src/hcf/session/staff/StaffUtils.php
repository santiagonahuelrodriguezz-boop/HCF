<?php

namespace hcf\session\staff;

use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use hcf\moderation\staffmode\items\Freeze;
use hcf\moderation\staffmode\items\PlayerInfo;
use hcf\moderation\staffmode\items\RandomTeleport;
use hcf\moderation\staffmode\items\Teleport;
use hcf\moderation\staffmode\items\Vanish;
use hcf\moderation\staffmode\items\InvSee;
use hcf\moderation\staffmode\items\EnderInvSee;
use hcf\session\staff\Session;

class StaffUtils {
    use SingletonTrait;

    public static function getPlayers(): array
    {
        return Server::getInstance()->getOnlinePlayers();
    }

    public static function sendKit(Player $player): void
    {
        $player->getInventory()->setContents([
            0 => new Teleport(),
            1 => new InvSee(),
            2 => new Freeze(),
            4 => new Vanish(),
            6 => new RandomTeleport(),
            7 => new EnderInvSee(),
            8 => new PlayerInfo()
        ]);
    }

    public static function broadcastMessage(string|Translatable $message): int
    {
        return Server::getInstance()->broadcastMessage($message);
    }

    public static function equals($object1, $object2): bool
    {
        return $object1 === $object2;
    }

    public static function getPlayerByPrefix(string $name) : ?Player
    {
        $found = null;
        $name = strtolower($name);
        $delta = PHP_INT_MAX;
        foreach (self::getPlayers() as $player) {
            if (stripos($player->getName(), $name) === 0) {
                $curDelta = strlen($player->getName()) - strlen($name);
                if ($curDelta < $delta) {
                    $found = $player;
                    $delta = $curDelta;
                }
                if ($curDelta === 0) {
                    break;
                }
            }
        }
        return $found;
    }

    public static function getInputMode(Player $player): string
    {
        $data = $player->getPlayerInfo()->getExtraData();
        return match ($data["CurrentInputMode"]) {
            InputMode::TOUCHSCREEN => "Touch",
            InputMode::MOUSE_KEYBOARD => "Keyboard",
            InputMode::GAME_PAD => "Controller",
            InputMode::MOTION_CONTROLLER => "Motion Controller",
            default => "Unknown"
        };
    }

    public static function getDeviceOS(Player $player): string
    {
        $data = $player->getPlayerInfo()->getExtraData();

        if ($data["DeviceOS"] === DeviceOS::ANDROID && $data["DeviceModel"] === "") {
            return "Linux";
        }

        return match ($data["DeviceOS"]) {
            DeviceOS::ANDROID => "Android",
            DeviceOS::IOS => "iOS",
            DeviceOS::OSX => "MacOS",
            DeviceOS::AMAZON => "FireOS",
            DeviceOS::GEAR_VR => "Gear VR",
            DeviceOS::HOLOLENS => "Hololens",
            DeviceOS::WINDOWS_10 => "Windows",
            DeviceOS::WIN32 => "WinEdu",
            DeviceOS::DEDICATED => "Dedicated",
            DeviceOS::TVOS => "TV OS",
            DeviceOS::PLAYSTATION => "PlayStation",
            DeviceOS::NINTENDO => "Nintendo Switch",
            DeviceOS::XBOX => "Xbox",
            DeviceOS::WINDOWS_PHONE => "Windows Phone",
            default => "Unknown"
        };
    }
}