<?php

declare(strict_types=1);

namespace hcf\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\item\Armor;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\utils\TextFormat;

class AutoFixCommand extends Command {

    public function __construct() {
        parent::__construct("autofix", "Apply AutoFix enchantment to armor");
        $this->setPermission("fix.auto");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command must be used in-game.");
            return false;
        }

        if (!$this->testPermission($sender)) {
            return false;
        }

        $item = $sender->getInventory()->getItemInHand();
        if (!$item instanceof Armor) {
            $sender->sendMessage(TextFormat::RED . "You must hold an armor item to apply AutoFix.");
            return false;
        }

        $nbt = $item->getNamedTag() ?? new CompoundTag();
        $nbt->setInt("Autofix", 1);
        $item->setNamedTag($nbt);

        $lore = $item->getLore();
        if (!in_array(TextFormat::RED . "AutoFix", $lore)) {
            $lore[] = TextFormat::RED . "AutoFix";
            $item->setLore($lore);
        }

        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage(TextFormat::GREEN . "AutoFix enchantment applied successfully.");
        return true;
    }
}