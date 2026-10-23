<?php

namespace hcf\extras;

use pocketmine\event\Listener;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use yeivwi\ce\CEnchant;
use pocketmine\item\{Item, Armor, Tool};
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\Server;
use pocketmine\item\Durable;
use hcf\HCFLoader;

class FixAllSignListener implements Listener {

    private HCFLoader $plugin;

    public function __construct(HCFLoader $plugin) {
        $this->plugin = $plugin;
    }

    public function onSignChange(SignChangeEvent $event): void {
        $lines = $event->getNewText()->getLines();
        if (strtolower($lines[0]) === '[fixall]') {
            $newText = new SignText([
                TextFormat::colorize('§g[§3FixAll§g]'),
                TextFormat::colorize('§e> Click me <'),
                '',
                ''
            ]);
            $event->setNewText($newText);
            $this->plugin->getLogger()->info("Sign [fixall] placed by " . $event->getPlayer()->getName());
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $block = $event->getBlock();
        if ($block->getPosition()->getWorld()->getTile($block->getPosition()) instanceof Sign) {
            $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
            if ($tile instanceof Sign) {
                $lines = $tile->getText()->getLines();
                if (TextFormat::clean($lines[0]) === '[FixAll]') {
                    $player = $event->getPlayer();
                    $this->repairInventory($player);
                    $player->sendMessage(TextFormat::colorize('§aTodos tus ítems han sido reparados.'));
                    $event->cancel();
                }
            }
        }
    }

    private function repairInventory(Player $player): void {
        foreach($player->getInventory()->getContents() as $slot => $item) {
            if ($item instanceof Tool || $item instanceof Armor) {
                if ($item->getDamage() > 0) {
                    if ($item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(CEnchant::IRREPARABLE))) continue;
                    $player->getInventory()->setItem($slot, $item->setDamage(0));
                }
            }
        }
        foreach($player->getArmorInventory()->getContents() as $slot => $item) {
            if ($item instanceof Tool || $item instanceof Armor) {
                if ($item->getDamage() > 0) {
                    $player->getArmorInventory()->setItem($slot, $item->setDamage(0));
                }
            }
        }
    }
}