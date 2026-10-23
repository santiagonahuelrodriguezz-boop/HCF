<?php

namespace hcf\command\admin;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\HCFLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use hcf\kohi\Kohi;

class DisbandAllCommand extends Command
{
    private array $excludedFactions = ["Bank", "Spawn", "North Road", "West Road", "South Road", "East Road", "Nether Spawn", "North-Road", "West-Road", "South-Road", "East-Road"];

    public function __construct()
    {
        parent::__construct("disbandall", "§cDisband all factions");
        $this->setPermission("op.permission");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cNot have permissions.");
            return;
        }

        foreach(HCFLoader::getInstance()->getFactionManager()->getFactions() as $faction) {  
            if (in_array($faction->getName(), $this->excludedFactions, true)) {
                continue; 
            }
            
            $faction->disband();
            HCFLoader::getInstance()->getFactionManager()->removeFaction($faction->getName());
            HCFLoader::getInstance()->getServer()->broadcastMessage('§c'. $faction->getName().'\'s §afaction was disbanded');
        }
        
        $sender->sendMessage("§b» §cAll factions have been eliminated!");

        $webHook = new Webhook(Kohi::WEBHOOK_ADMIN);
        $msg = new Message();
        $embed = new Embed();
        $embed->setTitle("Disband all");
        $embed->setColor(0xFF0000);
        $embed->setDescription("¡Todas las factions han sido eliminadas! 🚨");
        $embed->setFooter("Powered by GodRayos HCF");
        $embed->setThumbnail(Kohi::IMAGE);
        $msg->addEmbed($embed);
        $webHook->send($msg);
    }
}