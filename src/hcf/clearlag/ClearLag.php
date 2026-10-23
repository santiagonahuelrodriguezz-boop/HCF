<?php

declare(strict_types=1);

namespace hcf\clearlag;

use pocketmine\entity\object\ExperienceOrb;
use pocketmine\scheduler\ClosureTask;
use pocketmine\entity\object\ItemEntity;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use hcf\HCFLoader;

final class ClearLag {
    use SingletonTrait;

    private const TIME_EXECUTE = 5 * 60;

    public function clearEntitiesFromWorlds(array $worldNames) : int {
        $worldManager = Server::getInstance()->getWorldManager();
        $entities = 0;

        foreach ($worldNames as $worldName) {
            $world = $worldManager->getWorldByName($worldName);
            if ($world !== null) {
                foreach ($world->getEntities() as $entity) {
                    if (!$entity instanceof ExperienceOrb && !$entity instanceof ItemEntity) {
                        continue;
                    }

                    if ($entity->isFlaggedForDespawn() || $entity->isClosed()) {
                        continue;
                    }

                    if ($entity->ticksLived < 10 * 20) {
                        continue;
                    }
                    $entities++;
                    $entity->flagForDespawn();
                }
            }
        }

        return $entities;
    }

    public function task() : void {
        HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () : void {
            $worlds = ["world", "deathban", "end", "nether"];
            $entities = $this->clearEntitiesFromWorlds($worlds);
            HCFLoader::getInstance()->getServer()->broadcastMessage(' §7En Total §f[§9' . $entities . '§f] §7Se Han Removido');
        }), self::TIME_EXECUTE * 10);
    }
}