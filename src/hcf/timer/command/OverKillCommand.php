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

class OverKillCommand extends Command
{
   

    public function __construct()
    {
        parent::__construct('overkill', '§eCommand to turn on Overkill');
        $this->setPermission('overkill.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender))
            return;

        if (count($args) < 1) {
            $sender->sendMessage('§cUse /overkill help');
            return;
        }

        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(
 '§eoverkill Commands' . "\n" .
'§7/overkill start [time] - §eUse this command to start the OverKill' . "\n" .
'§7/overkill stop - §eUse this command to stop OverKill'
                );
                break;

            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getOverKill()->isActive()) {
                    $sender->sendMessage('§cThe OverKill is already started');
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage('§cUse /overkill start [time]');
                    return;
                }
                $time = $args[1];

                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getOverKill()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getOverKill()->setTime((int) $time);
                HCFLoader::getInstance()->getServer()->broadcastMessage(
'§f███████' . "\n" . '§f█§g█████§f█' . "\n" .
                    '§f█§g█§f███§g█§f█' . "\n" . '§f█§g█§f███§g█§f█' . ' §e§lOverKill Event§r' . "\n" .
                    '§f█§g█§f███§g█§f█' . ' §g» §r§7Kill, die and get restarts without fear of losing' . "\n" .
                    '§f█§g█§f███§g█§f█' . "\n" . '§f█§g████§g█§f█' . "\n" . '§f███████' . "\n" .
                    '§f███████'
                );

        
                 $webHook = new Webhook(Kohi::WEBHOOK_EVENT);
                $msg = new Message();
                $msg->setContent("**OverKill Event Started!** ".Kohi::TAG);

                $embed = new Embed();
                $embed->setTitle("OverKill Event 🛡️");
                $embed->setColor(0xFFA500); 
                $embed->setDescription("**Duration:** {$time} seconds\n".
                "**IP:** `".Kohi::IP."`\n".
                "**PORT:** `".Kohi::PORT."`\n"
                ."**DETAILS:** `In this event, you can die without even losing your inventory!! Remember that Archer Tag and Backstap do more damage in this event, THERE IS NO DEATHBAN!`");
                $embed->setThumbnail(Kohi::IMAGE);
                $embed->setImage(Kohi::BANNER);
                $embed->setFooter("Powered by AresPvP");
                $msg->addEmbed($embed);

                $webHook->send($msg);

                break;

            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getOverKill()->isActive()) {
                    $sender->sendMessage('§cThe OverKill has not started');
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getOverKill()->setActive(false);
                $sender->sendMessage('§cYou have turned off the OverKill');
                break;

            default:
                $sender->sendMessage('§cUse /overkill help');
                break;
        }
    }
}