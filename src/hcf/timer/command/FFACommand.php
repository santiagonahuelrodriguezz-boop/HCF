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

class FFACommand extends Command
{

    public function __construct()
    {
        parent::__construct('ffa', 'Command for FFA');
        $this->setPermission('op.permission');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender))
            return;

        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&cUse /ffa help'));
            return;
        }

        switch (strtolower($args[0])) {
            case 'help':
                $sender->sendMessage(TextFormat::colorize('&effa Commands' . "\n" .
                    '&7/ffa start [time] - &eUse this command to start the FFA'. "\n" .
                    '&7/ffa stop - &eUse this command to stop FFA'
                ));
                break;

            case 'start':
                if (HCFLoader::getInstance()->getTimerManager()->getFFA()->isActive()) {
                    $sender->sendMessage('§cThe FFA is already started');
                    return;
                }

                if (count($args) < 2) {
                    $sender->sendMessage('§cUse /ffa start [time]');
                    return;
                }
                $time = $args[1];

                $time = Timer::time($time);
                HCFLoader::getInstance()->getTimerManager()->getFFA()->setActive(true);
                HCFLoader::getInstance()->getTimerManager()->getFFA()->setTime((int)$time);
                
                HCFLoader::getInstance()->getServer()->broadcastMessage(
                    '§f███████' . "\n" .
                    '§f█§4█████§f█' . "\n" .
                    '§f█§4█§f█████' . "\n" .
                    '§f█§4█§f█████' . ' §c§lRagnarok Event§r' . "\n" .
                    '§f█§4█████§f█' . ' §g» §r§7The FFA has begun, prepare for the fury!' . "\n" .
                    '§f█§4█§f█████' . "\n" .
                    '§f█§4█§f█████' . "\n" .
                    '§f█§4█§f█████' . "\n" .
                    '§f███████'
                );

                $webHook = new Webhook(Kohi::WEBHOOK_EVENT);
                $msg = new Message();
                $msg->setContent("**FFA Event Started!** ".Kohi::TAG);
                $embed = new Embed();
                $embed->setTitle("FFA Event ⚔️");
                $embed->setColor(0x32CD32); 
                $embed->setDescription("**Duration:** {$time} seconds\n".
                "**IP:** `".Kohi::IP."`\n".
                "**PORT:** `".Kohi::PORT."`\n"
                ."**DETAILS:** `Only the last one standing will be the winner, fight to the end!`");
                $embed->setThumbnail(Kohi::IMAGE);
                $embed->setImage(Kohi::BANNER);
                $embed->setFooter("Powered by AresPvP");
                $msg->addEmbed($embed);
                $webHook->send($msg);

                $sender->sendMessage('§aThe FFA has started!');
                break;

            case 'stop':
                if (!HCFLoader::getInstance()->getTimerManager()->getFFA()->isActive()) {
                    $sender->sendMessage('§cThe FFA has not started');
                    return;
                }
                HCFLoader::getInstance()->getTimerManager()->getFFA()->setActive(false);
                $sender->sendMessage('§cYou have turned off the FFA');
                break;

            default:
                $sender->sendMessage('§cUse /ffa help');
                break;
        }
    }
}