<?php

declare(strict_types=1);

namespace hcf\command\admin;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\faction\Faction;
use hcf\HCFLoader;
use hcf\player\Player;
use hcf\kohi\Kohi;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

/**
 * Class FwhoCommand
 * @package juqn\hcf\command\discord
 */
class FtopCommand extends Command
{

    /**
     * GodCommand construct.
     */
    public function __construct()
    {
        parent::__construct('ftop');
        $this->setPermission('op.permission');
    }

    /**
     * @return array
     */
    private function getFactions(): array
    {
        $points = [];

        foreach (HCFLoader::getInstance()->getFactionManager()->getFactions() as $name => $faction) {
            if (in_array($faction->getName(), ['Spawn', 'North Road', 'South Road', 'East Road', 'West Road', 'Nether Spawn', 'End Spawn', 'Nether North Road', 'Nether South Road', 'Nether West Road', 'Nether East Road', '§5Citadel§c', 'Glowstonemontain']))
                continue;
            $points[$name] = $faction->getPoints();
        }
        return $points;
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

        $webHook = new Webhook(Kohi::WEBHOOK_LB);

        $msg = new Message();

        $data = $this->getFactions();
        arsort($data);
        $message = "";

        for ($i = 0; $i < 10; $i++) {
            $position = $i + 1;
            $factions = array_keys($data);
            $points = array_values($data);

            if (isset($factions[$i]))
                $message .= TextFormat::colorize(PHP_EOL . '' . $position . '. ' . $factions[$i] . '  **Points:** ' . $points[$i] . '');
        }
$sender->sendMessage("§a!Ready¡ Webhook sent successfully");
        $embed = new Embed();
        $embed->setTitle("FTop Scheduler 🦎");
        $embed->setColor(0x8B0000);
        $embed->setDescription("{$message}");
        $embed->setFooter(Kohi::IP);
        $embed->setThumbnail(Kohi::IMAGE);
        $msg->addEmbed($embed);

        $webHook->send($msg);
    }
}