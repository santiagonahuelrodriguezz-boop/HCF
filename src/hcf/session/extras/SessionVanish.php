<?php
    
namespace hcf\session\extras;

use pocketmine\player\Player;

class SessionVanish{
    
    public static array $inVanish = [];
    
    public static function isVanish(Player $player): ?Player {
            return self::inVanish[$player->getName()] ?? null;
        }
    
    public static function addVanished(Player $player): void {
            foreach ($player->getServer()->getOnlinePlayers() as $online) {
                $online->hidePlayer($player);
                $online->getNetworkSession()->onPlayerRemoved($player);
            }
            self::$inVanish[$player->getName()] = $player;
        }

        public static function removeVanished(Player $player): void {
            unset(self::$inVanish[$player->getName()]);
            foreach ($player->getServer()->getOnlinePlayers() as $online) {
                $online->showPlayer($player);
                $online->getNetworkSession()->onPlayerAdded($player);
            }
        }
}