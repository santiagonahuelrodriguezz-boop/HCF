<?php

declare(strict_types=1);

namespace hcf\command\admin;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\HCFLoader;
use hcf\kohi\Kohi;
use hcf\faction\Faction;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class FwhoCommand extends Command
{
    public function __construct()
    {
        parent::__construct('fwho');
        $this->setPermission('op.permission');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("§cUse: /fwho (facción)");
            return;
        }

        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($args[0]);

        if ($faction === null) {
            $webHook = new Webhook(Kohi::WEBHOOK_LB);
            $msg = new Message();
            $embed = new Embed();
            $embed->setTitle("No faction found");
            $embed->setColor(0x8B0000);
            $msg->addEmbed($embed);
            $webHook->send($msg);

            $sender->sendMessage(TextFormat::RED . "La facción no existe.");
            return;
        }

        $generalinfo = $faction->getName() . ' [' . count($faction->getOnlineMembers()) . '/' . count($faction->getMembers()) . ']' . PHP_EOL;
        $hq = $faction->getHome() !== null ? 'X: ' . $faction->getHome()->getFloorX() . ' Z: ' . $faction->getHome()->getFloorZ() . PHP_EOL : 'Not set ' . PHP_EOL;
        $leaders = implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? ' ' : '') . $session->getName() . ' [' . $session->getKills() . ']';
        }, $faction->getMembersByRole(Faction::LEADER))) . PHP_EOL;
        $coliders = implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? ' ' : '') . $session->getName() . ' [' . $session->getKills() . ']';
        }, $faction->getMembersByRole(Faction::CO_LEADER))) . PHP_EOL;
        $captains = implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? ' ' : '') . $session->getName() . ' [' . $session->getKills() . ']';
        }, $faction->getMembersByRole(Faction::CAPTAIN))) . PHP_EOL;
        $members = implode(', ', array_map(function ($session) {
            return ($session->isOnline() ? ' ' : '') . $session->getName() . ' [' . $session->getKills() . ']';
        }, $faction->getMembersByRole(Faction::MEMBER))) . PHP_EOL;
        $balance = $faction->getBalance() . " $" . PHP_EOL;
        $deathsuntilraidable = $faction->getDtr() >= $faction->getMaxDtr() ? '' : ($faction->getDtr() <= 0.00 ? ' ' : '') . round($faction->getDtr(), 2) . '■' . PHP_EOL;
        $timeuntilregen = gmdate('H:i:s', $faction->getTimeRegeneration()) . PHP_EOL;
        $points = $faction->getPoints() . PHP_EOL;
        $koths = $faction->getKothCaptures() . PHP_EOL;
        $strikes = $faction->getStrikes() . PHP_EOL;

        $webHook = new Webhook(Kohi::WEBHOOK_LB);
        $msg = new Message();

        $embed = new Embed();
        $embed->setTitle("Faction Information🏆");
        $embed->setColor(0x8B0000);

        if ($faction->getTimeRegeneration() !== null) {
            $embed->setDescription('**Faction:** ' . $generalinfo . "**HQ:** " . $hq . "**Leaders:** " . $leaders . "**Coleaders:** " . $coliders . "**Captains:** " . $captains . "**Members:** " . $members . "**Balance:** " . $balance . "**Deaths until Raidable:** " . $deathsuntilraidable . "**Time Until Regen:** " . $timeuntilregen . "**Points:** " . $points . "**KoTH Captures:** " . $koths . "**Strikes:** " . $strikes);
            $embed->setFooter(Kohi::IP);
            $embed->setThumbnail(Kohi::IMAGE);
            $msg->addEmbed($embed);
            $webHook->send($msg);
            return;
        }
$sender->sendMessage("§a!Ready¡ Webhook sent successfully");
        $embed->setDescription('**Faction:** ' . $generalinfo . "**HQ:** " . $hq . "**Leaders:** " . $leaders . "**Coleaders:** " . $coliders . "**Captains:** " . $captains . "**Members:** " . $members . "**Balance:** " . $balance . "**Deaths until Raidable:** " . $deathsuntilraidable . "**Points:** " . $points . "**KoTH Captures:** " . $koths . "**Strikes:** " . $strikes);
        $embed->setFooter(Kohi::IP);
        $embed->setThumbnail(Kohi::IMAGE);
        $msg->addEmbed($embed);
        $webHook->send($msg);
    }
}