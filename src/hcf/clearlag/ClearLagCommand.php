<?php

declare(strict_types=1);

namespace hcf\clearlag;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\Server;

class ClearLagCommand extends Command {

    public function __construct() {
        parent::__construct("clearlag", "Elimina todas las entidades de los mundos");
        $this->setPermission('op.permission');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void {
        $worlds = ["world", "deathban", "end"];
        $entities = $this->clearEntitiesFromWorlds($worlds);
        $sender->getServer()->broadcastMessage("§4» §7A total off §f[§c" . $entities . "§f] §7items have been removed");
    }
  private function clearEntitiesFromWorlds(array $worldNames) : int {
    $worldManager = Server::getInstance()->getWorldManager();
    $entities = 0;
    foreach ($worldNames as $worldName) {
        $world = $worldManager->getWorldByName($worldName);
        if ($world !== null) {
            foreach ($world->getEntities() as $entity) {
                if (!$entity instanceof ExperienceOrb && !$entity instanceof ItemEntity) {
                    continue;
                }
                if ($entity->isClosed()) {
                    continue;
                }
                if ($entity->ticksLived < 10 * 20) {
                    continue;
                }
                $entities++;
                $entity->kill();
            }
        }
    }
    return $entities;
    }
}