<?php

declare(strict_types=1);

namespace hcf\timer\command;

use hcf\HCFLoader;
use hcf\utils\time\Timer;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use hcf\kohi\Kohi;

class RagnarokCommand extends Command
{
    
    /**
     * RagnarokCommand construct.
     */
    public function __construct()
    {
        parent::__construct('blackhole', '§bCommand for event fury');
        $this->setPermission('op.permission');
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
            $sender->sendMessage(TextFormat::colorize('&cUse /blackhole help'));
            return;
        }
        
        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
                    TextFormat::colorize('&eBlackHole Commands') . "\n" .
                    TextFormat::colorize('&7/blackhole start [time] - &eUse this command to start the BlackHole') . "\n" .
                    TextFormat::colorize('&7/blackhole stop - &eUse this command to stop BlackHole ')
                );
                break;
            
            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getRagnarok()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe BlackHole is already started'));
                    return;
                }
                
                if (count($args) < 2) {
                    $sender->sendMessage(TextFormat::colorize('&cUse /blackhole start [time]'));
                    return;
                }
                $time = $args[1];
                
                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getRagnarok()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getRagnarok()->setTime((int) $time);
                $broadcastMessage = [
                    TextFormat::colorize("&7███████"),
                    TextFormat::colorize("&7██&0█&0█&0█&7██"),
                    TextFormat::colorize("&7█&0████&0█&7█ &r&7[§l§0BlackHole Event§r§7]"),
                    TextFormat::colorize("&7█&0████&0█&7█ &r&eThe Event Has Started!"),
                    TextFormat::colorize("&7█&0████&0█&7█ Kill players to get a key"),
                    TextFormat::colorize("&7██&0█&0█&0█&7██"),
                    TextFormat::colorize("&7███████")
                ];
                foreach ($broadcastMessage as $message) {
                    $sender->getServer()->broadcastMessage($message);
                }

                
                $webHook = new Webhook(Kohi::WEBHOOK_EVENT);
                $msg = new Message();
                $msg->setContent("**BlackHole Event Started! **".Kohi::TAG);
                $embed = new Embed();
                $embed->setDescription("**Duration:** {$time} seconds\n".
                "**IP:** `".Kohi::IP."`\n".
                "**PORT:** `".Kohi::PORT."`\n"
                ."**DETAILS:** `In this frenetic event, kills are worth KEYS! For every kill you make, you'll earn a BLACKHOLE key (Everyone will have Strength 2 in this event).`");
                $embed->setThumbnail(Kohi::IMAGE);
                $embed->setImage(Kohi::BANNER);
                $embed->setFooter("Powered by AresPvP");
                $msg->addEmbed($embed);

                $webHook->send($msg);

                $sender->sendMessage(TextFormat::colorize('&aThe BlackHole has started!'));
                break;
            
            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getRagnarok()->isActive()) {
                    $sender->sendMessage(TextFormat::colorize('&cThe BlackHole has not started'));
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getRagnarok()->setActive(false);
                $sender->sendMessage(TextFormat::colorize('&cYou have turned off the BlackHole'));
                break;
            
            default:
                $sender->sendMessage(TextFormat::colorize('&cUse /blackhole help'));
                break;
        }
    }
}