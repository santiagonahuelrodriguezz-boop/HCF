<?php

namespace hcf\customitems\airdrops;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\world\sound\BlazeShootSound;
use pocketmine\world\Position;
use hcf\HCFLoader;
use hcf\kohi\Kohi;
use hcf\customitems\airdrops\data\AirdropData;
use hcf\customitems\airdrops\task\AirdropAnimationTask;
use hcf\player\Player as PlayerPlayer;
use pocketmine\Server;

class Airdrop implements Listener
{
    public function giveAirdrop(Player $player, int $amount = 1): void
    {
        $airdrop = VanillaBlocks::FURNACE()->asItem()->setCount($amount);
        $airdrop->setCustomName("§l§dAIRDROP");

        $lootItems = AirdropData::getLoot();
        $lootDescriptions = [];
        foreach ($lootItems as $item) {
            /** @var Item $item */
            $lootDescriptions[] = "§7- " . $item->getName() . " §fx" . $item->getCount();
        }
        $lore = [
            "§r§e• Content:",
            "§r§dStore: §9•> " . Kohi::TEBEX . " §9<•"
        ];

        $lore = array_merge($lore, $lootDescriptions);

        $airdrop->setLore($lore);
        $airdrop->addEnchantment(new EnchantmentInstance(VanillaEnchantments::INFINITY(), 1));
        $airdrop->getNamedTag()->setInt('airdrop', 1);

        $player->getInventory()->addItem($airdrop);
    }

    public function onBlockInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->getCustomName() === "§l§dAIRDROP" && $event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
            $event->cancel();

            $block = $event->getBlock();
            $dropPosition = new Position(
                $block->getPosition()->getX(),
                $block->getPosition()->getY() + 10,
                $block->getPosition()->getZ(),
                $block->getPosition()->getWorld()
            );
            if (!$this->isAllowZone($dropPosition, $player)) {
                $player->sendMessage("§cNo puedes lanzar un Airdrop en esta zona.");
                return;
            }
            $item->setCount($item->getCount() - 1);
            $player->getInventory()->setItemInHand($item);
            $player->getWorld()->addSound($player->getPosition(), new BlazeShootSound());
            $task = new AirdropAnimationTask($dropPosition);
            HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask($task, 5);
        }
    }

    private function isAllowZone(Position $p,Player $player):bool{
        if(!class_exists(HCFLoader::class))return true;
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($p);
        if($claim === null){
            if($p->distance(\pocketmine\Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400)return false;
        }else{
            if(in_array($claim->getType(), ['spawn', 'road', 'koth', 'citadel', 'custom', 'bank']))return false;
            if($player instanceof PlayerPlayer && $player->getSession()->getFaction()!==$claim->getName())return false;
        }
        return true;
    }
}