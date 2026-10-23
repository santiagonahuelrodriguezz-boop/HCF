<?php

namespace hcf\entity\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use pocketmine\entity\Entity;

class RemoveEntityCommand extends Command {

    public function __construct() {
        parent::__construct("removenpc", "§eDelete NPC's ");
        $this->setPermission('op.permission');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
      
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§b» §cYou do not have permission to use this command.");
            return false;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage("§b» §cThis command can only be used in-game.");
            return false;
        }
        
        if (empty($args)) {
            $item = VanillaItems::GOLDEN_HOE()
                ->addEnchantment(new EnchantmentInstance(VanillaEnchantments::FEATHER_FALLING(), 1))
                ->setCustomName("§eRemove NPC §r§7(Right Click)")
                ->setLore([
                    "§r§7remove currently available npc type",
                    "§r§7configure npc"
                ]);

            $sender->getInventory()->addItem($item);
            $sender->sendMessage("§b» §7You have received the §bRemove NPC §7tool!");
            return true;
        }

        $entityName = $args[0];
        $world = $sender->getWorld();
        $entities = $world->getEntities();
        $found = false;

        foreach ($entities as $entity) {

            if (get_class($entity) === "hcf\\entity\\server\\" . $entityName) {
                $entity->kill();
                $sender->sendMessage("§b» §7The entity §b$entityName §7has been removed.");
                $found = true;
            }
        }

        if (!$found) {
            $sender->sendMessage("§b» §cNo entity found with the name: §b$entityName");
        }

        return true;
    }
}