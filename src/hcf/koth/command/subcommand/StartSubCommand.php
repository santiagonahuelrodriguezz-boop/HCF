<?php

declare(strict_types=1);

namespace hcf\koth\command\subcommand;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\koth\command\KothSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\TotemUseSound;
use hcf\kohi\Kohi;
use pocketmine\Server;

class StartSubCommand implements KothSubCommand
{
    public function execute(CommandSender $sender, array $args): void
    {
        if (count($args) < 1) {
            $sender->sendMessage(TextFormat::colorize('&c/koth start [string: name]'));
            return;
        }

        $name = $args[0];

        // --- RESTRICCIÓN: mínimo 9 jugadores conectados ---
        if (count(Server::getInstance()->getOnlinePlayers()) < 1) {
            $sender->sendMessage(TextFormat::colorize("&cNo puedes iniciar un KoTH ."));
            return;
        }

        if (HCFLoader::getInstance()->getKothManager()->getKothActive() !== null) {
            $sender->sendMessage(TextFormat::colorize('&cThere is already activated a koth right now'));
            return;
        }

        if (HCFLoader::getInstance()->getKothManager()->getKoth($name) === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe koth does not exist'));
            return;
        }

        $koth = HCFLoader::getInstance()->getKothManager()->getKoth($name);
        $location = $koth->getCoords();
        $time = $koth->getTime() / 60;
        $points = $koth->getPoints();
        $world = $koth->getCapzone()->getWorld($name);

        if ($koth->getCapzone() === null) {
            $sender->sendMessage(TextFormat::colorize('&cThe capzone is not selected'));
            return;
        }

        if ($sender instanceof Player) {
            if ($sender->getSession()->getCooldown("koth.cooldown")) {
                $sender->sendMessage(TextFormat::colorize('&cDebes esperar ' . Timer::format($sender->getSession()->getCooldown("koth.cooldown")->getTime()) . ' para volver a prender un koth'));
                return;
            }
        }

        // --------- NORMAL KOTHs ---------
        if ($koth->getName() !== "Citadel" && $koth->getName() !== "End") {
            HCFLoader::getInstance()->getKothManager()->setKothActive($name);
            $sender->sendMessage(TextFormat::colorize('&aYou have activated the koth ' . $name));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f███████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f███&d█&f█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f██&d█&f██ &r&6[KingOfTheHill]"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d███&f███ &r&9" . $koth->getName() . " &ehas started in &6" . $koth->getCoords() . "!"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f██&d█&f██ &r&6[KingOfTheHill] &eWin the event and get &9Rewards"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f███&d█&f█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f███&d█&f█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f███████"));

            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                $player->sendTitle(TextFormat::colorize("&cKoTH &5" . $koth->getName()), TextFormat::colorize("§ghas started"));
                $player->getWorld()->addSound($player->getPosition(), new TotemUseSound);
            }
            
            $msg = new Message();
            $msg->setContent("**Koth Start!**" . Kohi::TAGG);
            $webhookUrl = Kohi::WEBHOOK_KOTH;
            $webHook = new Webhook($webhookUrl);

            $embed = new Embed();
            $embed->setTitle("KoTH " . $name . " started 🚨");
            $embed->setColor(0xFF0000);
            $embed->addField("Location: ", "{$location}");
            $embed->addField("World: ", "{$world}");
            $embed->addField("Time ", "{$time} minutes", true);
            $embed->addField("Rewards ", "{$points} Points and 5 keys KoTH", true);
            $embed->setFooter("Powered by DemonHCF");
            $embed->setThumbnail(Kohi::IMAGE);
            $msg->addEmbed($embed);
            $webHook->send($msg);

            if ($sender instanceof Player) {
                if ($sender->hasPermission("godrayos.koth")) {
                    $sender->getSession()->addCooldown("koth.cooldown", "", Timer::time("24h"), false, false);
                } else if ($sender->hasPermission("godofwar.koth")) {
                    $sender->getSession()->addCooldown("koth.cooldown", "", Timer::time("38h"), false, false);
                } else if ($sender->hasPermission("kratos.koth")) {
                    $sender->getSession()->addCooldown("koth.cooldown", "", Timer::time("32h"), false, false);
                } else if ($sender->hasPermission("spartan.koth")) {
                    $sender->getSession()->addCooldown("koth.cooldown", "", Timer::time("36h"), false, false);
                } else if ($sender->hasPermission("divinity.koth")) {
                    $sender->getSession()->addCooldown("koth.cooldown", "", Timer::time("40h"), false, false);
                } else if ($sender->hasPermission("atomic.koth")) {
                    $sender->getSession()->addCooldown("koth.cooldown", "", Timer::time("44h"), false, false);
                } else if ($sender->hasPermission("rayo.koth")) {
                    $sender->getSession()->addCooldown("koth.cooldown", "", Timer::time("48h"), false, false);
                } else {
                    return;
                }
            }

          /*  HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f███████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f███&d█&f█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f██&d█&f██ &r&6[KingOfTheHill]"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d███&f███ &r&9" . $koth->getName() . " &ehas started in &6" . $koth->getCoords() . "!"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f██&d█&f██ &r&6[KingOfTheHill] &eWin the event and get &9Rewards"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f███&d█&f█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f█&d█&f███&d█&f█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&f███████"));

            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                $player->sendTitle(TextFormat::colorize("&cKoTH &5" . $koth->getName()), TextFormat::colorize("§ghas started"));
                $player->getWorld()->addSound($player->getPosition(), new TotemUseSound);
            }

            $msg = new Message();
            $msg->setContent("**Koth Start!**" . Kohi::TAGG);
            $webhookUrl = Kohi::WEBHOOK_KOTH;
            $webHook = new Webhook($webhookUrl);

            $embed = new Embed();
            $embed->setTitle("KoTH " . $name . " started 🚨");
            $embed->setColor(0xFF0000);
            $embed->addField("Location: ", "{$location}");
            $embed->addField("World: ", "{$world}");
            $embed->addField("Time ", "{$time} minutes", true);
            $embed->addField("Rewards ", "{$points} Points and 5 keys KoTH", true);
            $embed->setFooter("Powered by DemonHCF");
            $embed->setThumbnail(Kohi::IMAGE);
            $msg->addEmbed($embed);
            $webHook->send($msg);*/
        }

        // --------- CITADEL KOTH ---------
        if ($koth->getName() === "Citadel") {
            if (!$sender->hasPermission("citadel.cmd")) {
                $sender->sendMessage(TextFormat::colorize("&cNo tienes permiso para activar Citadel !."));
                return;
            }
            HCFLoader::getInstance()->getKothManager()->setKothActive($name);
            $sender->sendMessage(TextFormat::colorize('&aYou have activated the koth ' . $name));

            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6[Citadel]"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&ehas started in &6" . $koth->getCoords() . "!"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6&eWin the event and get &9Rewards"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));

            $msg = new Message();
            $msg->setContent("**Koth Start!**" . Kohi::TAGG);
            $webhookUrl = Kohi::WEBHOOK_KOTH;

            $embed = new Embed();
            $embed->setTitle("KoTH " . $name . " has started 🚨");
            $embed->setColor(0xFF0000);
            $embed->addField("Location: ", "{$location}");
            $embed->addField("World: ", "{$world}");
            $embed->addField("Time ", "{$time} minutes", true);
            $embed->addField("Rewards ", "{$points} Points and 5 keys KoTH", true);
            $embed->setFooter("Powered by DemonHCF");
            $embed->setThumbnail(Kohi::IMAGE);
            $msg->addEmbed($embed);

            $webHook = new Webhook("https://discord.com/api/webhooks/1431836190914514954/DM-aNsut7z40AIGr5ZgDxGi7UpdTfEo2ROHP_MGKd2vte_1OVp9pu_WBvxirLqb5Jk2M"); 
            $webHook->send($msg);
        }

        // --------- END KOTH ---------
        if ($koth->getName() === "End") {
            if (!$sender->hasPermission("end.cmd")) {
                $sender->sendMessage(TextFormat::colorize("&cNo tienes permiso para activar End !."));
                return;
            }
            HCFLoader::getInstance()->getKothManager()->setKothActive($name);
            $sender->sendMessage(TextFormat::colorize('&aYou have activated the koth ' . $name));

            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5███████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5███&f█&5███"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5██&f█&5████ &r&6[End]"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5█&f█&5█████ &r&ehas started in &6" . $koth->getCoords() . "!"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5█&f█&5█████ &r&6&eWin the event and get &9Rewards"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5██&f█&5████"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5███&f█&5███"));
            HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5███████"));

            $webhookUrl = "https://discord.com/api/webhooks/1431836190914514954/DM-aNsut7z40AIGr5ZgDxGi7UpdTfEo2ROHP_MGKd2vte_1OVp9pu_WBvxirLqb5Jk2M"; // tu URL
            $webHook = new Webhook($webhookUrl);
            $msg = new Message();

            $embed = new Embed();
            $embed->setTitle("KoTH " . $name . " has started 🚨");
            $embed->setColor(0x800080); // morado para diferenciar End
            $embed->addField("Location:", "{$location}");
            $embed->addField("World:", "{$world}");
            $embed->addField("Time", "{$time} minutes", true);
            $embed->addField("Rewards", "{$points} Points and 4 keys End", true);
            $embed->setFooter("Powered by DemonHCF");
            $embed->setThumbnail(Kohi::IMAGE);

            $msg->addEmbed($embed);
            $webHook->send($msg);
        }
    }
}