<?php

declare(strict_types=1);

namespace hcf\session;

use hcf\HCFLoader;
use hcf\handler\kit\classes\HCFClass;
use hcf\waypoint\WayPoint;
use pocketmine\Server;
use hcf\session\TimerTrait;

class Session
{
    use TimerTrait;
   
    private string $xuid;
    
    private string $name;
   
    private ?string $faction = null;
    
    private ?string $prefix = null;
   
    private int $balance;
    
    private int $crystals;

    private ?WayPoint $wayPoint = null;
   
    private int $kills;
    
    private int $deaths;
    
    private int $killStreak;
    
    private int $highestKillStreak;
    
    private array $cooldowns = [];

    private array $energies = [];

    private bool $autoFeed = false;
   
    private bool $factionchat = false;
   
    private $viewcommand = false;
   
    private bool $mobkilled = false;
    
    private bool $logout = false;
    
    public function __construct(string $xuid, array $data, bool $firstTime)
    {
        $this->xuid = $xuid;
        $this->name = $data['name'];
        if ($data['faction'] !== null && HCFLoader::getInstance()->getFactionManager()->getFaction($data['faction']) !== null)
            $this->faction = $data['faction'];
        
        $this->prefix = $data['prefix'];
        
        $this->balance = (int) $data['balance'];
        $this->crystals = (int) $data['crystals'];
        
        $this->kills = (int) $data['stats']['kills'];
        $this->deaths = (int) $data['stats']['deaths'];
        $this->killStreak = (int) $data['stats']['killStreak'];
        $this->highestKillStreak = (int) $data['stats']['highestKillStreak'];

        foreach ($data['energies'] as $key => $d)
            $this->addEnergy($key, $d['format'], (int) $d['energy']);
        
        foreach ($data['cooldowns'] as $key => $d)
            $this->addCooldown($key, $d['format'], (int) $d['time'], $d['paused'], $d['visible']);
        
       /* if ($firstTime)
            $this->addCooldown('starting.timer', ' §f| §cPvPTimer: §f', 60 * 60);*/
    }

    public function getEnergies(): array
    {
        return $this->energies;
    }
    
    public static function get(\hcf\player\Player|string $player) : ?Session {
    $guid = $player instanceof \hcf\player\Player ? $player->getXuid() : $player;
    return self::$sessions[$guid] ?? null;
}
    
    public function getEnergy(string $key): ?SessionEnergy
    {
        return $this->energies[$key] ?? null;
    }

    public function getWayPoint(): ?WayPoint {
        return $this->wayPoint;
    }

    public function setWayPoint(WayPoint $wayPoint = null): void {
        $this->wayPoint = $wayPoint;
    }

    public function getUuid(): string
    {
        return $this->xuid;
    }
    
    public function getXuid(): string
    {
        return $this->xuid;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getFaction(): ?string
    {
        return $this->faction;
    }
    
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }
    
    public function getBalance(): int
    {
        return $this->balance;
    }
    
    public function getCrystals(): int
    {
        return $this->crystals;
    }
    
    public function getKills(): int
    {
        return $this->kills;
    }
    
    public function getDeaths(): int
    {
        return $this->deaths;
    }
    
    public function getKillStreak(): int
    {
        return $this->killStreak;
    }
    
    public function getHighestKillStreak(): int
    {
        return $this->highestKillStreak;
    }
    
    public function getCooldowns(): array
    {
        return $this->cooldowns;
    }
    
    public function getCooldown(string $key): ?SessionCooldown
    {
        return $this->cooldowns[$key] ?? null;
    }

    public function hasAutoFeed(): bool
    {
        return $this->autoFeed;
    }

    public function hasFactionChat(): bool
    {
        return $this->factionchat;
    }
    
    public function hasViewCommand(): bool
    {
        return $this->viewcommand;
    }

    public function isMobKilled(): bool
    {
        return $this->mobkilled;
    }
    
    public function isLogout(): bool
    {
        return $this->logout;
    }
    
    public function isOnline(): bool
    {
        $player = Server::getInstance()->getPlayerExact($this->getName());
        
        return $player !== null;
    }
    
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    public function setFaction(?string $factionName): void
    {
        $this->faction = $factionName;
    }
    
    public function setPrefix(?string $prefixName): void
    {
        $this->prefix = $prefixName;
    }
    
    public function setBalance(int $balance): void
    {
        $this->balance = $balance;
    }
    
    public function setCrystals(int $crystals): void
    {
        $this->crystals = $crystals;
    }

    public function addEnergy(string $key, string $format, int $energy = 0, bool $paused = false): void
    {
        $this->energies[$key] = new SessionEnergy($format, $energy, $paused);
    }
    
    public function removeEnergy(string $key): void
    {
        unset($this->energies[$key]);
    }
    
    public function addKill(): void
    {
        $this->kills++;
    }
    
    public function removeKill(): void
    {
        $this->kills--;
    }
    
    public function addDeath(): void
    {
        $this->deaths++;
    }
    
    public function removeDeath(): void
    {
        $this->deaths--;
    }
    
    /**
     * @param int $amount
     */
    public function setKillStreak(int $amount): void
    {
        $this->killStreak = $amount;
    }
    
    public function addKillStreak(): void
    {
        $this->killStreak++;
    }
    
    public function removeKillStreak(): void
    {
        $this->killStreak--;
    }
    
    public function addHighestKillStreak(): void
    {
        $this->highestKillStreak++;
    }
    
    public function removeHighestKillStreak(): void
    {
        $this->highestKillStreak--;
    }
    
    public function addCooldown(string $key, string $format, int $time, bool $paused = false, bool $visible = true): void
    {
        $this->cooldowns[$key] = new SessionCooldown($format, $time, $paused, $visible);
    }
    
    public function removeCooldown(string $key): void
    {
        unset($this->cooldowns[$key]);
    }

    public function setAutoFeed(bool $value): void
    {
        $this->autoFeed = $value;
    }

    public function setFactionChat(bool $value): void
    {
        $this->factionchat = $value;
    }
    
    public function setViewCommand(bool $value): void
    {
        $this->viewcommand = $value;
    }

    public function setMobKilled(bool $value): void
    {
        $this->mobkilled = $value;
    }
    
    public function setLogout(bool $value): void
    {
        $this->logout = $value;
    }
    
    public function onUpdate(): void
    {
         foreach ($this->getCooldowns() as $key => $cooldown) {
            $cooldown->update();
            
            if ($cooldown->getTime() <= 0)
                $this->removeCooldown($key);
        }
    }
    
    public function getData(): array
    {
        $data = [
            'name' => $this->getName(),
            'faction' => $this->getFaction(),
            'prefix' => $this->getPrefix(),
            'balance' => $this->getBalance(),
            'crystals' => $this->getCrystals(),
            'cooldowns' => [],
            'energies' => [],
            'stats' => [
                'kills' => $this->getKills(),
                'deaths' => $this->getDeaths(),
                'killStreak' => $this->getKillStreak(),
                'highestKillStreak' => $this->getHighestKillStreak()
            ]
        ];
        
        foreach ($this->getCooldowns() as $key => $cooldown)
            $data['cooldowns'][$key] = [
                'format' => $cooldown->getFormat(),
                'time' => $cooldown->getTime(),
                'paused' => $cooldown->isPaused(),
                'visible' => $cooldown->isVisible()
            ];
            foreach ($this->getEnergies() as $key => $energy)
            $data['energies'][$key] = [
                'format' => $energy->getFormat(),
                'energy' => $energy->getEnergy(),
                'paused' => $energy->isPaused(),
            ];
        return $data;
    }
}