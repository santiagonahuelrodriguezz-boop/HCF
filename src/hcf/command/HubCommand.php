<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\HCFLoader;
use hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;

class HubCommand extends Command
{
    
    /**
     * LogoutCommand construct.
     */
    public function __construct()
    {
        parent::__construct('lobby', 'Command for go to lobby', "", ["hub"]);
        $this->setPermission("player.use");
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getCooldown('hub') !== null)
            return;
            
        $sender->getSession()->addCooldown('hub',  ' &l&f| &cHub&r&7: &c', 10);
        
        $xuid = (string)$sender->getUniqueId();
        $position = $sender->getPosition();
        /** @var TaskHandler */
        $handler = null;
        $handler = HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$position): void {
            $s = $sender->getSession();
            
            if (!$sender->isOnline()) {
                if ($s->getCooldown('hub') !== null) $s->removeCooldown('hub');
                $handler->cancel();
                return;
            }

            if ($s->getCooldown('combat.tag') !== null) {
                $s->removeCooldown('faction.stuck');
                $handler->cancel();
                return;
            }
            
            if ($position->distance($sender->getPosition()) > 3) {
                if ($s->getCooldown('hub') !== null) $s->removeCooldown('hub');
                $handler->cancel();
                return;
            }
            
            if ($sender->getSession()->getCooldown('hub') === null) {
                $sender->transfer("206.183.128.31", 2552);
                $handler->cancel();
            }
        }), 20);
    }
}