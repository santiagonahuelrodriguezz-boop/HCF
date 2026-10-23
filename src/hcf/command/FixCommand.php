<?php
namespace hcf\command;

use yeivwi\ce\CEnchant;
use hcf\HCFLoader;
use pocketmine\command\Command;
use pocketmine\item\{Item, Armor, Tool};
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\permission\DefaultPermissions;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TE;

class FixCommand extends Command
{
	
	/**
	 * FixCommand Constructor.
	 */
	public function __construct(){
		parent::__construct("fix", "Can repair the items in your inventory or your hand", "fix");
        $this->setPermission('fixall.command.use');
	}
	
	/**
	 * @param CommandSender $sender
	 * @param String $label
	 * @param Array $args
     * @return void
	 */
	public function execute(CommandSender $sender, String $label, Array $args) : void {
		if(empty($args)){
            $sender->sendMessage(TE::RED."§l§7[§6!§7] - §r§c Argument No.1 is Invalid.");
            return;
		}
		switch($args[0]){
			case "all":
				if(!empty($args[1])){
					if($sender->hasPermission("fixallother.command.use")){
						$player = HCFLoader::getInstance()->getServer()->getPlayerByPrefix($args[1]);
						if(empty($player)){
                            $sender->sendMessage("§l§7[§6!§7] - §r§cInvalid Player Name§7.");
							return;
						}
						foreach($player->getInventory()->getContents() as $slot => $item){
							if($item instanceof Tool||$item instanceof Armor){
								if($item->getDamage() > 0){
									$player->getInventory()->setItem($slot, $item->setDamage(0));
								}
							}
						}
						foreach($player->getArmorInventory()->getContents() as $slot => $item){
							if($item instanceof Tool||$item instanceof Armor){
								if($item->getDamage() > 0){
                                    if ($item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(CEnchant::IRREPARABLE))) continue;
									$player->getArmorInventory()->setItem($slot, $item->setDamage(0));
								}
							}
						}
                        $player->sendMessage("§l§7[§6!§7] - §r§7Your items were repaired by §f{$sender->getName()}§7.");
						$sender->sendMessage("§l§7[§6!§7] - §r§7You fixed the items of §f{$player->getName()}§7.");
					} else {
                        $sender->sendMessage(TE::RED."§l§7[§6!§7] - §r§cYou do not have permission to use this command.");
                    }
				}else{
					if(!$sender->hasPermission("fixall.command.use")){
						$sender->sendMessage(TE::RED."§l§7[§6!§7] - §r§cYou do not have permission to use this command.");
						return;
					}
					foreach($sender->getInventory()->getContents() as $slot => $item){
						if($item instanceof Tool||$item instanceof Armor){
							if($item->getDamage() > 0){
                                if ($item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(CEnchant::IRREPARABLE))) continue;
								$sender->getInventory()->setItem($slot, $item->setDamage(0));
							}
						}
					}
					foreach($sender->getArmorInventory()->getContents() as $slot => $item){
						if($item instanceof Tool||$item instanceof Armor){
							if($item->getDamage() > 0){
								$sender->getArmorInventory()->setItem($slot, $item->setDamage(0));
							}
						}
					}
					$sender->sendMessage("§l§7[§6!§7] - §r§7All items in your inventory have been repaired.");
				}
			break;
			case "hand":
				if(!$sender->hasPermission("fixhand.command.use")){
					$sender->sendMessage(TE::RED."§l§7[§6!§7] - §r§cYou do not have permission to use this command.");
					return;
				}
				$item = $sender->getInventory()->getItemInHand();
				if($item instanceof Tool||$item instanceof Armor){
					if($item->getDamage() > 0){
                        if ($item->hasEnchantment(EnchantmentIdMap::getInstance()->fromId(CEnchant::IRREPARABLE))) return;
						$sender->getInventory()->setItemInHand($item->setDamage(0));
					}
				}
				$sender->sendMessage("§l§7[§6!§7] - §r§7Successfully repaired item.");
			break;
		}
        
	}
}
