<?php

declare(strict_types=1);

namespace hcf\handler\kit\command\subcommand;

use hcf\handler\kit\command\KitSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\utils\TextFormat;

/**
 * Class CreateSubCommand
 * @package hcf\handler\kit\command\subcommand
 */
class CreateSubCommand implements KitSubCommand
{
    
    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player)
            return;
            
        if (count($args) < 4) {
            $sender->sendMessage(TextFormat::colorize('&c/kit create [string: kitName] [string: nameFormat] [string: cooldown | optional] [string: permission | optional] [string: category | free|pay|specials]'));
            return;
        }
        $kitName = $args[0];
        $nameFormat = $args[1];
        $cooldown = $args[2];
        $permission = $args[3];
        $category = $args[4];
        
        $items = $sender->getInventory()->getContents();
        $armor = $sender->getArmorInventory()->getContents();
        
        if (HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getKit($kitName) !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThis kit already exists'));
            return;
        }
        $representativeItem = $sender->getInventory()->getItemInHand();
        $cooldown = Timer::time($cooldown);

        if ($category === "free") {
            HCFLoader::getInstance()->getHandlerManager()->getKitManager()->addKit($kitName, $nameFormat, $permission, $representativeItem, $items, $armor, $cooldown);
        } elseif ($category === "pay") {
            HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->addKit($kitName, $nameFormat, $permission, $representativeItem, $items, $armor, $cooldown);
        } elseif ($category === "specials") {
            HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->addKit($kitName, $nameFormat, $permission, $representativeItem, $items, $armor, $cooldown);
        } else {
            $sender->sendMessage(TextFormat::colorize("&cCategory is not valid use: &efree, pay &cor &especials."));
        }
        $sender->sendMessage(TextFormat::colorize('&aYou have successfully created the ' . $kitName . ' kit'));
        
        HCFLoader::getInstance()->getHandlerManager()->getKitManager()->registerPermission($permission);
    }
}