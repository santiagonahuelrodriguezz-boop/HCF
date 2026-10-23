<?php

namespace hcf\entity\server;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\CompoundTag;
use hcf\player\Player;
use pocketmine\Server;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\DyeColor;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

class TeleportEntity extends Human {

 
    public static function create(Player $player): self {
        $nbt = CompoundTag::create()->setTag("Pos", new ListTag([
            new DoubleTag($player->getLocation()->x),
            new DoubleTag($player->getLocation()->y),
            new DoubleTag($player->getLocation()->z)
        ]))->setTag("Motion", new ListTag([
            new DoubleTag($player->getMotion()->x),
            new DoubleTag($player->getMotion()->y),
            new DoubleTag($player->getMotion()->z)
        ]))->setTag("Rotation", new ListTag([
            new FloatTag($player->getLocation()->yaw),
            new FloatTag($player->getLocation()->pitch)
        ]));
        return new self($player->getLocation(), $player->getSkin(), $nbt);
    }

    public function onUpdate(int $currentTick): bool {
        $this->setNameTag("§e>> §l§dDIMENSIONS§r§e <<\n §r§7Click to teleport to a dimension");
        $this->setScale(1);
        $this->setNameTagAlwaysVisible(true);
        $this->setNameTagVisible(true);
        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $event): void {
        $event->cancel();
        if (!$event instanceof EntityDamageByEntityEvent) {
            return;
        }
        $damager = $event->getDamager();
        if (!$damager instanceof Player) {
            return;
        }

        if ($damager->hasPermission('npc.command') && $damager->getInventory()->getItemInHand()->getCustomName() === "§eRemove NPC §r§7(Right Click)") {
            $this->kill();
            return;
        }

        $this->openMenu($damager);
    }

    public function openMenu(Player $player): void {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("§l§aTeleport Menu§r");
        $glass = VanillaBlocks::STAINED_HARDENED_GLASS()->setColor(DyeColor::BLUE())->asItem();
        $glass->setCustomName(" ");
        $endItem = VanillaItems::PAPER();
        $endItem->setCustomName("§l§uEND");
        for ($i = 0; $i < 27; $i++) {
            $menu->getInventory()->setItem($i, $glass);
        }
        $menu->getInventory()->setItem(12, $endItem);
   
        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $itemClicked = $transaction->getItemClicked();

            if ($itemClicked->getCustomName() === "§l§uEND") {
                $endWorld = Server::getInstance()->getWorldManager()->getWorldByName("end");
                if ($endWorld !== null) {
                    $player->teleport($endWorld->getSafeSpawn());
                    $player->sendMessage("§aTeleported to the END world.");
                } else {
                    $player->sendMessage("§cThe END world is not available.");
                }
            }

            $transaction->getPlayer()->removeCurrentWindow();

            return $transaction->discard();
        });

       
        $menu->send($player);
    }
}