<?php

declare(strict_types=1);

namespace hcf\provider;

use hcf\HCFLoader;
use hcf\utils\serialize\Serialize;

use hcf\pm5\ItemSerializer;
use pocketmine\utils\Config;

/**
 * Class Provider
 * @package hcf\provider
 */
class Provider
{
    /** @var Config */
public Config $kitSpecialsConfig;
    /** @var Config */
    public Config $rollbacks, $kothConfig, $claimConfig, $kitFreeConfig, $kitPayConfig;

    public function __construct()
    {
        $plugin = HCFLoader::getInstance();

        if (!is_dir($plugin->getDataFolder() . 'database'))
            @mkdir($plugin->getDataFolder() . 'database');

        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players');

        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions');
        
        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes');
        if (!is_dir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'privateVaults'))
            @mkdir($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'privateVaults');
        if (!is_dir($plugin->getDataFolder() . 'others'))
            @mkdir($plugin->getDataFolder() . 'others');

        $plugin->saveDefaultConfig();
        $this->kothConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'koths.yml', Config::YAML);
        $this->claimConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'claims.yml', Config::YAML);
        $this->rollbacks = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'rollbacks.yml', Config::YAML);
        $this->kitSpecialsConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'kitsSpecials.yml', Config::YAML, ["organization" => [], "kits" => []]);
        $this->kitPayConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'kitsPay.yml', Config::YAML, ["organization" => [], "kits" => []]);
        $this->kitFreeConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'kitsFree.yml', Config::YAML, ["organization" => [], "kits" => []]);
     /*   $this->vkitConfig = new Config($plugin->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'vkits.json', Config::JSON, ["organization" => [], "vkits" => []]);*/
    
    }
    public function save(): void {
        $this->savePlayers();
        $this->saveFactions();
        $this->saveRollbacks();
        $this->savePrefixes();
        $this->saveKoths();
        $this->saveClaims();
        $this->saveKits();
        $this->saveKitsPay();
        $this->saveKitsSpecials();
        
    }

    public function getKitConfig(): Config
    {
        return $this->kitFreeConfig;
    }

    public function getKitPayConfig(): Config
    {
        return $this->kitPayConfig;
    }
    
    public function getKitSpecialsConfig(): Config
    {
        return $this->kitSpecialsConfig;
    }
    
    public function getAbilitiesConfig(): Config
    {
        return $this->abilitiesConfig;
    }

    public function getKothConfig(): Config
    {
        return $this->kothConfig;
    }

    public function getClaimsConfig(): Config
    {
        return $this->claimConfig;
    }
    
    
    public function getAbilities() : array {
        $items = [];
        foreach($this->getAbilitiesConfig() as $data){
            $result = $data->getAll();
            if(isset($result["items"])){
                foreach($result["items"] as $item){
                    $items[] = Serialize::deserialize($item);
                }
            }
        }
        return $items;
    }
    public function getRollbacks(): array
{
    $rollbacks = [];

    $data = $this->rollbacks->get('rollbacks');
    if(is_array($data)){
        foreach ($data as $name => $entry) {
            if (isset($entry['items'])) {
                foreach ($entry['items'] as $slot => $item){
                    $entry['items'][$slot] = Serialize::deserialize($item);
                }
            }
            if (isset($entry['armor'])) {
                foreach ($entry['armor'] as $slot => $armor){
                    $entry['armor'][$slot] = Serialize::deserialize($armor);
                }
            }
            $rollbacks[$name] = $entry;
        }
    }

    return $rollbacks;
    }

    public function getPlayers(): array
    {
        $players = [];

        foreach (glob(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR . '*.yml') as $file)
            $players[basename($file, '.yml')] = (new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR . basename($file), Config::YAML))->getAll();
        return $players;
    }

    public function getEnderChests(): array
    {
        $pvs = [];

        foreach (glob(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'privateVaults' . DIRECTORY_SEPARATOR . '*.yml') as $file)
            $pvs[basename($file, '.yml')] = (new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'privateVaults' . DIRECTORY_SEPARATOR . basename($file), Config::YAML))->getAll();
        return $pvs;
       }

    public function getFactions(): array
    {
        $factions = [];
        foreach (glob(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . '*.yml') as $file)
            $factions[basename($file, '.yml')] = (new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . basename($file), Config::YAML))->getAll();
        return $factions;
    }
    
    public function getPrefixes(): array
    {
        $prefixes = [];

        foreach (glob(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes' . DIRECTORY_SEPARATOR . '*.yml') as $file)
            $prefixes[basename($file, '.yml')] = (new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes' . DIRECTORY_SEPARATOR . basename($file), Config::YAML))->getAll();
        return $prefixes;
    }
    
    public function getKoths(): array
    {
        $koths = [];

        foreach ($this->kothConfig->getAll() as $name => $data) {
            $koths[$name] = $data;
        }
        return $koths;
    }
    
    public function getClaims(): array
    {
        $claims = [];

        foreach ($this->claimConfig->getAll() as $name => $data) {
            $claims[$name] = $data;
        }
        return $claims;
    }
    
    public function getKitsSpecials(): array
    {
        $kits = [];

        foreach ($this->kitSpecialsConfig->get('kits') as $name => $data) {
            if ($data['representativeItem'] !== null)
                $data['representativeItem'] = Serialize::deserialize($data['representativeItem']);

            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item){
                    $data['items'][$slot] = Serialize::deserialize($item);
                }
            }

            if (isset($data['armor'])) {
                foreach ($data['armor'] as $slot => $armor){
                    $data['armor'][$slot] = Serialize::deserialize($armor);
                }
            }
            $kits[$name] = $data;
        }
        return $kits;
    }
    
    public function getKits(): array
    {
        $kits = [];
        foreach ($this->kitFreeConfig->get('kits') as $name => $data) {
            if ($data['representativeItem'] !== null)
                $data['representativeItem'] = Serialize::deserialize($data['representativeItem']);
            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item){
                    $data['items'][$slot] = Serialize::deserialize($item);
                }
            }
            if (isset($data['armor'])) {
                foreach ($data['armor'] as $slot => $armor){
                    $data['armor'][$slot] = Serialize::deserialize($armor);
                }
            }
            $kits[$name] = $data;
        }
        return $kits;
    }

    public function getKitsPay(): array
    {
        $kits = [];

        foreach ($this->kitPayConfig->get('kits') as $name => $data) {
            if ($data['representativeItem'] !== null)
                $data['representativeItem'] = Serialize::deserialize($data['representativeItem']);
            if (isset($data['items'])) {
                foreach ($data['items'] as $slot => $item){
                    $data['items'][$slot] = Serialize::deserialize($item);
                }
            }

            if (isset($data['armor'])) {
                foreach ($data['armor'] as $slot => $armor){
                    $data['armor'][$slot] = Serialize::deserialize($armor);
                }
            }
            $kits[$name] = $data;
        }
        return $kits;
    }


    public function saveAbilities() : void {
        $itemData = [];
        foreach(HCFLoader::getInstance()->getAbilitiesManager()->items as $item){
            $itemData[] = Serialize::serialize($item);
        }
        $this->abilitiesConfig->set("items", $itemData);
        $this->abilitiesConfig->save();
    }

    public function saveCrates(): void
    {
        $crates = [];

        foreach (HCFLoader::getInstance()->getHandlerManager()->getCrateManager()->getCrates() as $crate) {
            $crates[$crate->getName()] = $crate->getData();
        }
        $this->crateConfig->setAll($crates);
        $this->crateConfig->save();
    }

    public function savePlayers(): void
    {
        foreach (HCFLoader::getInstance()->getSessionManager()->getSessions() as $xuid => $session) {
            $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'players' . DIRECTORY_SEPARATOR  . $xuid . '.yml', Config::YAML);
            $config->setAll($session->getData());
            $config->save();
        }
    }

    public function saveFactions(): void
    {
        foreach (HCFLoader::getInstance()->getFactionManager()->getFactions() as $name => $faction) {
            $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'factions' . DIRECTORY_SEPARATOR . $name . '.yml', Config::YAML);
            $config->setAll($faction->getData());
            $config->save();
        }
    }
    
    public function savePrefixes(): void
    {
        foreach (HCFLoader::getInstance()->getPrefixManager()->getPrefixes() as $name => $prefix) {
            $config = new Config(HCFLoader::getInstance()->getDataFolder() . 'database' . DIRECTORY_SEPARATOR . 'prefixes' . DIRECTORY_SEPARATOR . $name . '.yml', Config::YAML);
            $config->setAll($prefix->getData());
            $config->save();
        }
    }
    
    public function saveKoths(): void
    {
        $koths = [];

        foreach (HCFLoader::getInstance()->getKothManager()->getKoths() as $koth) {
            $koths[$koth->getName()] = $koth->getData();
        }
        $this->kothConfig->setAll($koths);
        $this->kothConfig->save();
    }

    public function saveKits(): void
    {
        $kits = [];

        foreach (HCFLoader::getInstance()->getHandlerManager()->getKitManager()->getKits() as $kit) {
            $kits[$kit->getName()] = $kit->getData();
        }
        $this->kitFreeConfig->set('kits', $kits);
        $this->kitFreeConfig->save();
    }

    public function saveKitsPay(): void
    {
        $kits = [];

        foreach (HCFLoader::getInstance()->getHandlerManager()->getKitPayManager()->getKits() as $kit) {
            $kits[$kit->getName()] = $kit->getData();
        }
        $this->kitPayConfig->set('kits', $kits);
        $this->kitPayConfig->save();
    }
    
    public function saveKitsSpecials(): void
    {
        $kits = [];

        foreach (HCFLoader::getInstance()->getHandlerManager()->getKitSpecialsManager()->getKits() as $kit) {
            $kits[$kit->getName()] = $kit->getData();
        }
        $this->kitSpecialsConfig->set('kits', $kits);
        $this->kitSpecialsConfig->save();
    }
    
    public function getRollbacksConfig(): Config
    {
        return $this->rollbacks;
    }

    public function saveRollbacks(): void
    {
        $rollbacks = [];

        foreach (HCFLoader::getInstance()->getRollbackManager()->getRollbacks() as $rollback) {
            $rollbacks[$rollback->getName()] = $rollback->getData();
        }
        $this->rollbacks->set('rollbacks', $rollbacks);
        $this->rollbacks->save();
    }

    public function saveClaims(): void
    {
        $claims = [];

        foreach (HCFLoader::getInstance()->getClaimManager()->getClaims() as $name => $claim) {
            $claims[$name] = $claim->getData();
        }
        $this->claimConfig->setAll($claims);
        $this->claimConfig->save();
    }
}