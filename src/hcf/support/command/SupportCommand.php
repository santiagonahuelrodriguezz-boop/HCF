<?php

declare(strict_types=1);

namespace hcf\support\command;

use hcf\support\provider\ProviderSupport;
use hcf\support\manager\Manager;
use hcf\support\utils\SupportMenu;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class SupportCommand extends Command
{
    
    public function __construct()
    {
        parent::__construct('support', '§bSupport your favorite partner');
        $this->setPermission('support.cmd');
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if (!isset($args[0])) {
            SupportMenu::openSupportChest($sender);
            return;
        }
        
        switch (strtolower($args[0])) {
            case 'edit':
                if (!$sender->hasPermission('support.op')) return;
                SupportMenu::openSupportEditChest($sender);
                break;
           
           case 'create':
               if (!$sender->hasPermission('support.op')) return;
               
               if (!isset($args[1])) {
                   $sender->sendMessage('§cInsufficient arguments. Enter the name of the Partner');
                   return;
               }
               
               if (!isset($args[2])) {
                   $sender->sendMessage('§c insufficient arguments, use /support create [name] [NameFormat]');
                   return;
               }
               ProviderSupport::getInstance()->addPartner($args[1], $args[2]);
               $sender->sendMessage('§bYou created a new partner to support. Please put it in the partner chest §7(/support edit)');
               
               $sender->getInventory()->addItem(SupportMenu::makeItem($args[1]));
               break;
               
           case 'remove':
               if (!$sender->hasPermission('support.op')) return;
               
               if (!isset($args[1])) {
                   $sender->sendMessage('§cInsufficient arguments. Enter the name of the partner');
                   return;
               }
               ProviderSupport::getInstance()->removePartner($args[1]);
               $sender->sendMessage('§cYou have successfully removed the partner');
               break;
        }
    }
}