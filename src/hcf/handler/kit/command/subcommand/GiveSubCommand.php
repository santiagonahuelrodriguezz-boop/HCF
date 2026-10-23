<?php

namespace hcf\handler\kit\command\subcommand;

use hcf\HCFLoader;  
use hcf\handler\kit\command\KitSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Embed;
use hcf\kohi\Kohi;

class GiveSubCommand implements KitSubCommand {

    public function execute(CommandSender $sender, array $args) : void {

        if (count($args) < 3) {
            $sender->sendMessage(TextFormat::RED . "Uso: /kit give [category: free|pay|specials] [player] [kit]");
            return;
        }

        $category = strtolower($args[0]);
        $targetName = $args[1];
        $kitName = $args[2];

        $targetPlayer = HCFLoader::getInstance()->getServer()->getPlayerExact($targetName);

        if (!$targetPlayer instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Jugador $targetName no encontrado!");
            return;
        }

        switch ($category) {
            case "free":
                $kit = HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getKit($kitName);
                break;

            case "pay":
                $kit = HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->getKit($kitName);
                break;

            case "specials":
                $kit = HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->getKit($kitName);
                break;

            default:
                $sender->sendMessage(TextFormat::RED . "Categoría inválida. Usa: free | pay | specials");
                return;
        }

        if (!$kit) {
            $sender->sendMessage(TextFormat::RED . "Kit $kitName no encontrado!");
            return;
        }

        $kit->giveTo($targetPlayer);

        $sender->sendMessage(TextFormat::GREEN . "Le has dado el kit {$kit->getName()} a {$targetPlayer->getName()}");
        $targetPlayer->sendMessage(TextFormat::GREEN . "Se te ha dado el kit {$kit->getName()}");

        $webHook = new Webhook(Kohi::WEBHOOK_ADMIN);
        $msg = new Message();
        $embed = new Embed();

        $embed->setTitle("🎁 Kit entregado");
        $embed->setColor(0xFFFF00);

        $embed->setDescription(
            "👤 **Staff:** {$sender->getName()}\n" .
            "🎮 **Jugador:** {$targetPlayer->getName()}\n" .
            "📂 **Categoría:** " . ucfirst($category) . "\n" .
            "📦 **Kit:** {$kit->getName()}"
        );

        $embed->setFooter("HCF MAP OG");
        $embed->setThumbnail(Kohi::IMAGE);

        $msg->addEmbed($embed);
        $webHook->send($msg);
    }
}