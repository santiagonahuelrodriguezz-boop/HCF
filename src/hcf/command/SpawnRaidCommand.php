<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\entity\mobs\DefendRaidEntity;
use hcf\HCFLoader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SpawnRaidCommand extends Command
{
    private HCFLoader $plugin;

    public function __construct()
    {
        parent::__construct("spawnraid", "Spawnea el zombi Defend Raid", "/spawnraid");
        $this->setPermission("hcf.spawnraid");
    }

    public function execute(CommandSender $sender, string $label, array $args): void
    {
        if(!$sender instanceof Player){
            $sender->sendMessage("§cEste comando solo puede usarse en el juego.");
            return;
        }

        if(!$this->testPermission($sender)){
            $sender->sendMessage("§cNo tienes permiso para usar este comando.");
            return;
        }

        $claim = $this->plugin->getClaimManager()->getClaimByPosition($sender->getPosition());

        if($claim === null){
            $sender->sendMessage("§cNo estás dentro de ningún claim.");
            return;
        }

        if($claim->getType() !== "Defend"){
            $sender->sendMessage("§cSolo puedes spawnear el raid en zonas de tipo §eDefend§c.");
            return;
        }

        DefendRaidEntity::spawn($sender->getWorld(), $sender->getLocation());
        $sender->sendMessage("§aZombi Defend Raid spawneado correctamente.");
    }
}