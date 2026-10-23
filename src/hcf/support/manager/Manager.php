<?php

declare(strict_types=1);

namespace hcf\support\manager;

use hcf\support\provider\ProviderSupport;
use pocketmine\utils\SingletonTrait;

class Manager
{
    use SingletonTrait;
    private ProviderSupport $provider;
    
    public function __construct()
    {
        $this->provider = new ProviderSupport($this);
    }
    
    public function onDisable(): void
    {
        $this->provider->saveData();
    }

    public function getPartners(): array
    {
        return $this->provider->getPartners();
    }

    public function getPlayers(): array
    {
        return $this->provider->getPlayers();
    }

    public function getPartner(string $name): ?array
    {
        return $this->provider->getPartner($name);
    }

    public function getPlayer(string $name): ?array
    {
        return $this->provider->getPlayer($name);
    }

    public function addPartner(string $partnerName, string $format): void
    {
        $this->provider->addPartner($partnerName, $format);
    }
    
    public function addSupport(string $partnerName): void
    {
        $this->provider->addSupport($partnerName);
    }

    public function removePartner(string $partnerName): void
    {
        $this->provider->removePartner($partnerName);
    }
    
    public function setSlotOrganization(int $slot, string $partnerName): void
    {
        $this->provider->setSlotOrganization($slot, $partnerName);
    }
    
    public function removeSlotOrganization(string $partnerName): void
    {
        $this->provider->removeSlotOrganization($partnerName);
    }
    
    public function resetSlotsOrganization(): void
    {
        $this->provider->resetSlotsOrganization();
    }
    
    public function setPlayer(string $name, int $minutes, string $partner): void
    {
        $this->provider->setPlayer($name, $minutes, $partner);
    }
}