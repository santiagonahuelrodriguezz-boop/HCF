<?php

declare(strict_types=1);

namespace hcf\rollback;

use hcf\HCFLoader;
use hcf\handler\kit\command\KitCommand;
use hcf\rollback\command\RollbackCommand;
use hcf\utils\serialize\Serialize;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use RuntimeException;

class RollbackManager
{
    private array $rollbacks = [];

    public function __construct() {
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new RollbackCommand());
        
        $this->loadRollbacks();
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new RollbackListener(), HCFLoader::getInstance());
    }

    private function loadRollbacks(): void {
        $provider = HCFLoader::getInstance()->getProvider();
        $loaded = 0;
        $failed = 0;

        foreach ($provider->getRollbacks() as $name => $data) {
            try {
                $items = $this->safeDeserializeItems($data['items'] ?? []);
                $armor = $this->safeDeserializeItems($data['armor'] ?? []);
                
                $this->addRollback($name, $items, $armor);
                $loaded++;
            } catch (\Exception $e) {
                $failed++;
                HCFLoader::getInstance()->getLogger()->warning("Failed to load rollback '$name': " . $e->getMessage());
            }
        }

        HCFLoader::getInstance()->getLogger()->info(
            TextFormat::GREEN . "Loaded $loaded rollbacks" . 
            ($failed > 0 ? TextFormat::RED . " ($failed failed)" : "")
        );
    }

    private function safeDeserializeItems(array $serializedItems): array {
        $items = [];
        
        foreach ($serializedItems as $key => $serialized) {
            try {
                if (is_string($serialized)) {
                    $item = Serialize::deserialize($serialized);
                    if ($item instanceof Item && !$item->isNull()) {
                        $items[$key] = $item;
                    }
                }
            } catch (\Exception $e) {
                HCFLoader::getInstance()->getLogger()->debug(
                    "Failed to deserialize rollback item at index $key: " . $e->getMessage()
                );
            }
        }
        
        return $items;
    }

    public function getRollbacks(): array {
        return $this->rollbacks;
    }

    public function getRollback(string $rollbackName): ?Rollback {
        return $this->rollbacks[$rollbackName] ?? null;
    }

    public function addRollback(string $rollbackName, array $items, array $armor): void {
        $this->rollbacks[$rollbackName] = new Rollback($rollbackName, $items, $armor);
    }

    public function removeRollback(string $rollbackName): void {
        unset($this->rollbacks[$rollbackName]);
    }
}