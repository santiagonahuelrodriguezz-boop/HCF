<?php

namespace hcf\vcs\vouchers;

use pocketmine\item\ItemIdentifier;
use CortexPE\DiscordWebhookAPI\Embed;

use CortexPE\DiscordWebhookAPI\Message;

use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\item\ItemTypeIds;
use pocketmine\Server;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use hcf\vcs\data\VoucherBase;
use hcf\vcs\data\Voucher;
use hcf\kohi\Kohi;

class RankVoucher extends VoucherBase
{
    public function __construct()
    {
        $identifier = new ItemIdentifier(ItemTypeIds::EMERALD, 0);
        parent::__construct($identifier, "§r§dRank §r§7Voucher", ["", "§r§7• Item exclusivo de la crate §cKoTH", "§r§7• Este Voucher hara entrega", "§7• de un rango §4Divinity §7de 3 días.", "", "§r§e•> Clic para reclamar. <•"]);
    }

    protected function onVoucherUse(Player $player): void
    {
        $playerName = $player->getName();
        Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), 'ranks setrank ' . '"' . $playerName . '"' . ' Divinity 3d');
        $player->sendMessage("§c» §7You have received Rank for 3 days");
        
        $webHook = new Webhook(Kohi::WEBHOOK_ANC);
        $msg = new Message();
        $msg->setContent("**Alguien encontró un Voucher🗡️**");

        $embed = new Embed();
        $embed->setTitle("¡$playerName ha encontrado un voucher escondido en GodRayos MCPE! 🗡️");
        $embed->setColor(0x00FF00);
        $embed->addField("IP:", Kohi::IP);
        $embed->addField("Puerto:", Kohi::PORT);
        $embed->setFooter("¡Úsalo pronto!");
        $msg->addEmbed($embed);
        $webHook->send($msg);
    }
}