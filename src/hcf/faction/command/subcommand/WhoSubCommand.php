<?php

declare(strict_types=1);

namespace hcf\faction\command\subcommand;

use hcf\faction\command\FactionSubCommand;
use hcf\faction\Faction;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class WhoSubCommand implements FactionSubCommand
{

    /**
     * @param CommandSender $sender
     * @param array $args
     */
    public function execute(CommandSender $sender, array $args): void
    {
     /*   if (!$sender instanceof Player)
            return;*/
        $faction = null;

        if (!isset($args[0])) {
            if ($sender->getSession()->getFaction() === null) {
                $sender->sendMessage(TextFormat::colorize('&cYou don\'t have faction'));
                return;
            }
            $faction = $sender->getSession()->getFaction();
        } else {
            $target = $sender->getServer()->getPlayerByPrefix($args[0]);

            if ($target instanceof Player) {
                if ($target->getSession()->getFaction() === null) {
                    $sender->sendMessage(TextFormat::colorize('Player dont have faction'));
                    return;
                }
                $faction = $target->getSession()->getFaction();
            } else {
                if (HCFLoader::getInstance()->getFactionManager()->getFaction($args[0])) {
                    $faction = $args[0];
                }
            }
        }

        if ($faction === null) {
            $sender->sendMessage(TextFormat::colorize('&cNo faction found'));
            return;
        }
        $message = '' . "\n";
        $message .= '&b' . (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getName()). ' &7[&a' . count(HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getOnlineMembers()) . '/&c7&7] &g- &eHQ: &f' . (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome() !== null ? 'X: ' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorX() . ' Z: ' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getHome()->getFloorZ() : 'Not set ');
        $leaders = 
        $message .=  "\n" . '&e> &gLeader: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::LEADER))) . "\n";
        $message .= '&e> &gColeaders: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CO_LEADER))) . "\n";
        $message .= '&e> &gCaptains: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::CAPTAIN))) . "\n";
        $message .= '&e> &gMembers: &f' . implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? '&a' : '&c') . $session->getName() . ' &7[' . $session->getKills() . ']';
        }, HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMembersByRole(Faction::MEMBER))) . "\n";
        $message .= '&e> &gBalance: &a$' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getBalance() . "\n";
        $message .= '&e> &gDTR: ' . (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() >= HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getMaxDtr() ? '&a' : (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr() <= 0.00 ? '&c' : '&e')) . round(HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getDtr(), 2) . '■' . "\n";

        if (HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration() !== null) {
            $message .= '&e> &gTime Until Regen: &9' . gmdate('H:i:s', HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getTimeRegeneration()) . "\n";
        }
        $message .= '&e> &gPoints: &9' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getPoints() . "\n";
        $message .= '&e> &gKoTH Captures: &9' . HCFLoader::getInstance()->getFactionManager()->getFaction($faction)->getKothCaptures(). "\n";
        $message .= '';

        $sender->sendMessage(TextFormat::colorize($message));
    }
}