<?php

declare(strict_types=1);

namespace hcf\support\provider;

use hcf\HCFLoader;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class ProviderSupport
{
    use SingletonTrait;
    private array $partners = [], $players = [];
    

    public function loadData(): void
    {
        $dataFolder = HCFLoader::getInstance()->getDataFolder();
        if (!file_exists($dataFolder . 'database')) {
            @mkdir($dataFolder . 'database');
        }
        
        $configPlayers = new Config($dataFolder . 'database/players.json', Config::JSON, []);
        $configPartners = new Config($dataFolder . 'database/partners.json', Config::JSON, [
            'organization' => [],
            'types' => []
        ]);
        $this->players = $configPlayers->getAll();
        $this->partners = $configPartners->getAll();

        if (empty($this->players)) {
            HCFLoader::getInstance()->getLogger()->warning("Error... no se cargaron el contenido del Support.");
        }

        if (empty($this->partners) || !isset($this->partners['organization'], $this->partners['types'])) {
            HCFLoader::getInstance()->getLogger()->warning("Error...");
        }
    }

    public function saveData(): void
    {
        if (is_array($this->partners) && is_array($this->players)) {
            $dataFolder = HCFLoader::getInstance()->getDataFolder();

            $configPlayers = new Config($dataFolder . 'database/players.json', Config::JSON, []);
            $configPartners = new Config($dataFolder . 'database/partners.json', Config::JSON, [
                'organization' => [],
                'types' => []
            ]);

            $configPlayers->setAll($this->players);
            $configPlayers->save();

            $configPartners->setAll($this->partners);
            $configPartners->save();
        }
    }

    public function getPartners(): array
    {
        return $this->partners;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getPartner(string $name): ?array
    {
        return $this->partners['types'][$name] ?? null;
    }

    public function getPlayer(string $name): ?array
    {
        return $this->players[$name] ?? null;
    }

    public function addPartner(string $partnerName, string $format): void
    {
        $this->partners['types'][$partnerName] = [
            'format' => $format,
            'amount_support' => 0
        ];
    }
    
    public function addSupport(string $partnerName): void
    {
        $this->partners['types'][$partnerName]['amount_support']++;
    }

    public function removePartner(string $partnerName): void
    {
        unset($this->partners['types'][$partnerName]);
    }
    
    public function setSlotOrganization(int $slot, string $partnerName): void
    {
        $this->partners['organization'][$slot] = $partnerName;
    }
    
    public function removeSlotOrganization(string $partnerName): void
    {
        $key = array_search($partnerName, $this->partners['organization']);
        unset($this->partners['organization'][$key]);
    }
    
    public function resetSlotsOrganization(): void
    {
        $this->partners['organization'] = [];
    }
    
    public function setPlayer(string $name, int $minutes, string $partner): void
    {
        $this->players[$name] = [
            'time' => time() + ($minutes * 60),
            'last_support' => $partner
        ];
    }
}