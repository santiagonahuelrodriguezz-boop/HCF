<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\kohi\Kohi;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class FreeKitsCommand
 * @package hcf\timer\command
 */
class FreeKitsCommand extends Command
{
    
    /**
     * FreeKitsCommand construct.
     */
    public function __construct()
    {
        parent::__construct('freekits', 'Command for event freekitsall');
        $this->setPermission('freekits.command');
    }
    
    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender))
            return;
            
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /freekits help'));
            return;
        }
        
        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&eFreeKits Commands') . "\n" .
                    TextFormat::colorize('&7/freekits start [time] - &eUse this command to start the freekits') . "\n" .
                    TextFormat::colorize('&7/freekits stop - &eUse this command to stop freekits')
                );
                break;
            
            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getFreeKits()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe FreeKits is already started'));
                    return;
                }
                
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /freekits start [time]'));
                    return;
                }
                $time = $args[1];
                
                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getFreeKits()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getFreeKits()->setTime((int) $time);
                
                $broadcastMessage = [
                    TextFormat::colorize("&7███████"),
                    TextFormat::colorize("&7█&b████&7██"),
                    TextFormat::colorize("&7█&b█&7█████ &r&7[§l§bFreeKits Event§r§7]"),
                    TextFormat::colorize("&7█&b████&7██ &r&eThe Event Has Started!"),
                    TextFormat::colorize("&7█&b█&7█████ Claim all kits !"),
                    TextFormat::colorize("&7█&b█&7█████"),
                    TextFormat::colorize("&7███████")
                ];
                foreach ($broadcastMessage as $message) {
                    $sender->getServer()->broadcastMessage($message);
                }
                $webHook = new Webhook(Kohi::WEBHOOK_EVENT);

                $msg = new Message();
                $msg->setContent("**FreeKits Event Started!**".Kohi::TAG);

                $embed = new Embed();
                $embed->setTitle("FreeKits Event 🗝️");
                $embed->setDescription("**Duration:** {$time} seconds\n".
                "**IP:** `".Kohi::IP."`\n".
                "**PORT:** `".Kohi::PORT."`\n"
                ."**DETAILS:** `At this event, all paid kits can be claimed without any permits!`");
                $embed->setThumbnail(Kohi::IMAGE);
                $embed->setImage(Kohi::BANNER);
                $embed->setFooter("Powered by AresPvP");
                $msg->addEmbed($embed);

                $webHook->send($msg);
                
                $sender->sendMessage(TextFormat::colorize('&aThe dfreekits has started!'));
                break;
            
            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getFreeKits()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe freekits has not started'));
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getFreeKits()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the freekits'));
                break;
            
            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /freekits help'));
                break;
        }
    }
}