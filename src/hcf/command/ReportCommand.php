<?php

namespace hcf\command;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\HCFLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use hcf\player\Player;
use hcf\kohi\Kohi;

class ReportCommand extends Command
{
    public function __construct()
    {
        parent::__construct('report', '§bCommand for reporting players', '', ["rp"]);
        $this->setPermission("player.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§b» §cNo tienes permiso para utilizar este comando.");
            return;
        }

        if (count($args) < 2) {
            $sender->sendMessage("§b» §cUps, use: /report [player] [razón].");
            return;
        }

        $targetPlayer = HCFLoader::getInstance()->getServer()->getPlayerExact($args[0]);
        if ($targetPlayer === null) {
            $sender->sendMessage(TextFormat::colorize("§b» §cEl jugador no fue encontrado, intenta de nuevo."));
            return;
        }

        $reason = explode(" ", $args[1])[0];
        $sender->sendMessage(TextFormat::colorize("§b» §7Has reportado a §b" . $targetPlayer->getName() . " §7por: §b" . $reason));
        $webHook = new Webhook(Kohi::WEBHOOK_REPORT);

        $msg = new Message();
        $msg->setContent("**Reports 🌐**");

        $embed = new Embed();
        $embed->setTitle("A player has been reported");
        $embed->setColor(0xFF0000);
        $embed->addField("» » Report Executed by:", $sender->getName());
        $embed->setFooter("Powered by Kohi MCPE"); 

        $embed->setThumbnail(Kohi::IMAGE);
        $embed->addField("» » Reported player:", $targetPlayer->getName());
        $embed->addField("» Reason:", $reason);
        $msg->addEmbed($embed);

        $webHook->send($msg);
    }
}