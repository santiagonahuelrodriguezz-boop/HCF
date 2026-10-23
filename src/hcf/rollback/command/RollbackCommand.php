<?php

namespace hcf\rollback\command;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\HCFLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use hcf\kohi\Kohi;

class RollbackCommand extends Command
{
    public function __construct()
    {
        parent::__construct('rb', '§bCommand for rollback', '', ["rollback"]);
        $this->setPermission("rb.command.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§b» §gNo tienes permiso para utilizar este comando:");
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage("§b» §gUso: /rb (player) [razon]");
            return;
        }

        $targetPlayer = HCFLoader::getInstance()->getServer()->getPlayerExact($args[0]);
        if ($targetPlayer === null) {
            $sender->sendMessage("§b» §gEl jugador no fue encontrado, intenta de nuevo.");
            return;
        }

        $reason = "Sin razón";
        if (isset($args[1])) {
            $reason = implode(" ", array_slice($args, 1));
        }

        $rollback = HCFLoader::getInstance()->getRollbackManager();
        if ($rollback->getRollback($targetPlayer->getName()) === null) {
            $sender->sendMessage("§b» §gUps! parece que este jugador no tiene muertes.");
            return;
        }

        $rollback->getRollback($targetPlayer->getName())->giveTo($targetPlayer);
        $rollback->removeRollback($targetPlayer->getName());

        $sender->sendMessage("§b» §7Le has restaurado el inventario a §b" . $targetPlayer->getName());
        $targetPlayer->sendMessage("§b» §7El staff §b" . $sender->getName() . " §7te ha restaurado el inventario.");

        $webHook = new Webhook(Kohi::WEBHOOK_ADMIN);
        $msg = new Message();
        $embed = new Embed();

        $embed->setTitle("Rollback ejecutado");
        $embed->setColor(0xFFFF00);

        $embed->setDescription(
            "👤 **Staff:** {$sender->getName()}\n" .
            "🎮 **Jugador:** {$targetPlayer->getName()}\n" .
            "⚠️ **Razón:** $reason"
        );

        $embed->setFooter("HCF MAP OG");
        $embed->setThumbnail(Kohi::IMAGE);

        $msg->addEmbed($embed);
        $webHook->send($msg);
    }
}