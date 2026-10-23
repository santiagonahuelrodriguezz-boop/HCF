<?php

namespace hcf\entity\server;

use pocketmine\entity\Human;
use yeivwi\ce\CEnchant;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\item\{Item, Armor, Tool};
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\Server;
use pocketmine\item\Durable;
use hcf\HCFLoader;
use pocketmine\entity\EntitySizeInfo;

class RepairEntity extends Human
{

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

    public function onUpdate(int $currentTick): bool
    {
        $parent = parent::onUpdate($currentTick);

        $this->setNameTag("§e>>  §l§aFIX ALL §r§e<< \n §7 Repair your entire inventory for only §b2000 §7balance ");
        
        $this->setNameTagAlwaysVisible(true);

        return $parent;
    }

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
               
        $repairCost = 2000;

    
        $session = HCFLoader::getInstance()->getSessionManager()->getSession((string)$damager->getUniqueId());
        $balance = $session->getBalance();

       
        if ($balance < $repairCost) {
            $damager->sendMessage("§cNo tienes suficiente dinero.");
            return;
        }


        $session->setBalance($balance - $repairCost);
        $this->repairInventory($damager);
        $damager->sendMessage("§b» §aTu inventario ha sido reparado por §g" . $repairCost . " §amonedas.");
    }

    private function repairInventory(Player $player): void
{
        foreach($player->getInventory()->getContents() as $slot => $item){
            if($item instanceof Tool||$item instanceof Armor){
                if($item->getDamage() > 0){
                    if ($item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(CEnchant::IRREPARABLE))) continue;
                    $player->getInventory()->setItem($slot, $item->setDamage(0));
							}
						}
					}
					foreach($player->getArmorInventory()->getContents() as $slot => $item){
						if($item instanceof Tool||$item instanceof Armor){
							if($item->getDamage() > 0){
								$player->getArmorInventory()->setItem($slot, $item->setDamage(0));
           }
         }
      }
   }
}