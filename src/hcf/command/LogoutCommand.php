<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\HCFLoader;
use hcf\player\Player;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;

class LogoutCommand extends Command
{
    public function __construct()
    {
        parent::__construct('logout', 'Command for Logout');
        $this->setPermission("player.use");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;
        
        if ($sender->getSession()->getCooldown('logout') !== null)
            return;
        
        $sender->getSession()->addCooldown('logout', ' §f| §cLogout: §7', 35);
        
        $xuid = (string)$sender->getUniqueId();
        $startPos = $sender->getPosition();
     
        $handler = null;

        $handler = HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(
            new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$startPos): void {

                $session = HCFLoader::getInstance()->getSessionManager()->getSession($xuid);

                if ($session === null) {
                    $handler->cancel();
                    return;
                }
            
                if (!$sender->isOnline()) {
                    if ($session->getCooldown('logout') !== null)
                        $session->removeCooldown('logout');

                    $handler->cancel();
                    return;
                }

                if ($session->getCooldown('combat.tag') !== null) {
                    $sender->sendMessage("§cNo puedes usar §e/logout §cmientras estás en combate.");
                    $session->removeCooldown('logout'); // ← FIX
                    $handler->cancel();
                    return;
                }
                
                if ($startPos->distance($sender->getPosition()) > 3) {
                    if ($session->getCooldown('logout') !== null)
                        $session->removeCooldown('logout');

                    $sender->sendMessage("§cCancelaste tu Logout por moverte.");
                    $handler->cancel();
                    return;
                }
                
                if ($session->getCooldown('logout') === null) {
                    $session->setLogout(true);
                    $sender->kick(TextFormat::colorize('&c[Logout] You have successfully logged out'));
                    $handler->cancel();
                }
            }), 
        20);
    }
}