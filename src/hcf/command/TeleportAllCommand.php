<?php 

namespace hcf\command;

use hcf\HCFLoader;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TeleportAllCommand extends Command {
    
    public function __construct() {
		parent::__construct('tpall', '§gcommand to teleport all players to you');
		$this->setPermission('op.permission');
	}
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        if (!$sender instanceof Player) return;
        
        if (!$this->testPermission($sender)) return;
        foreach(HCFLoader::getInstance()->getServer()->getOnlinePlayers() as $players){
				$players->teleport($sender->getLocation());
		}
        $sender->sendMessage('');
    }    
}


