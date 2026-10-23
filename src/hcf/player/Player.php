<?php

declare(strict_types=1);

namespace hcf\player;

use itoozh\bounties\session\SessionFactory;
use hcf\module\enchantment\Enchantment;
use hcf\HCFLoader;
use hcf\handler\kit\classes\ClassFactory;
use hcf\handler\kit\classes\HCFClass;
use hcf\session\Session;
use hcf\timer\types\TimerCustom;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player as BasePlayer;
use pocketmine\player\PlayerInfo;
use pocketmine\utils\TextFormat as TF;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use DateTime; 
use DateTimeZone;

/**
 * Class Player
 * @package hcf\player
 */
class Player extends BasePlayer
{

    public bool $scoreboardMode = true;
    private int $titleId = 0;
    
    /** @var int|float  */
    private int|float $lastCheck = -1;

    /** @var int */
    private int $lastLine = 0;

    /** @var PlayerScoreboard */
    private PlayerScoreboard $scoreboard;

    private ?HCFClass $class = null;

    /** @var string|null */
    private ?string $currentClaim = null;
    
    /** @var bool */
    private bool $god = false;

    /**
     * Player construct.
     * @param Server $server
     * @param NetworkSession $session
     * @param PlayerInfo $thisInfo
     * @param bool $authenticated
     * @param Location $spawnLocation
     * @param CompoundTag|null $namedtag
     */
    public function __construct(Server $server, NetworkSession $session, PlayerInfo $thisInfo, bool $authenticated, Location $spawnLocation, ?CompoundTag $namedtag)
    {
        parent::__construct($server, $session, $thisInfo, $authenticated, $spawnLocation, $namedtag);
        $this->scoreboard = new PlayerScoreboard($this);
    }

    public function getScoreboardMode(): bool
    {
        return $this->scoreboardMode;
    }
    
    /**
     * @return PlayerScoreboard
     */
    public function getScoreboard(): PlayerScoreboard
    {
        return $this->scoreboard;
    }

    public function getClass(): ?HCFClass
    {
        return $this->class;
    }

    public function setClass(?HCFClass $class): void
    {
        $this->class = $class;
    }
    
    /**
     * @return string|null
     */
    public function getCurrentClaim(): ?string
    {
        return $this->currentClaim;
    }
    
    /**
     * @return bool
     */
    public function isGod(): bool
    {
        return $this->god;
    }
    
    /**
     * @return Session|null
     */
    public function getSession(): ?Session
    {
        return HCFLoader::getInstance()->getSessionManager()->getSession((string)$this->getUniqueId());
    }
    
    /**
     * @param string|null $claimName
     */
    public function setCurrentClaim(?string $claimName = null): void
    {
        $this->currentClaim = $claimName;
    }
    
    /**
     * @param bool $value
     */
    public function setGod(bool $value): void
    {
        $this->god = $value;
    }

    public function setScoreboardMode($mode = true): void
    {
        $this->scoreboardMode = $mode;
    }
    
  /*  public function updateNameTag() : void {
    $session = $this->getSession();
    $factionNameTag = '&c[No Faction]';
    $faction = null;

    
    if ($session->getFaction() !== null) {
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($session->getFaction());
        if ($faction !== null) {
            
            $dtrColor = ($faction->getDtr() === (count($faction->getMembers()) + 0.1)) ? '&a' : '&c';
            $factionNameTag = '&6[&c' . $faction->getName() . ' ' . $dtrColor . $faction->getDtr() . '■&6]';
        }
    }

    
    if (isset(Archer::$archerMark[$this->getName()]) && Archer::$archerMark[$this->getName()] > time()) {
        $name = TextFormat::LIGHT_PURPLE . "[ArcherMarked] " . $this->getName();
    } elseif ($session->getCooldown("pvp.timer") !== null) {
        $name = TextFormat::GREEN . $this->getName();
    } else {
        $name = TextFormat::RED . $this->getName();
    }

   
    $this->setScoreTag(TextFormat::colorize($factionNameTag));

    
    $this->sendCustomTagTo($name, $this->getViewers());
    }
    */
    public function join(): void
    {
        # Scoreboard setup
        $this->scoreboard->init();
        
       
        
        if ($this->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
            $faction->announce('§aMember online: §f' . $this->getName());
           
        }
        # Disconnected 
        $disconnectedManager = HCFLoader::getInstance()->getDisconnectedManager();
        $disconnected = $disconnectedManager->getDisconnected((string)$this->getUniqueId());
        
        if ($disconnected !== null)
            $disconnected->join($this);
        
         # Add coordinates
        $pk = GameRulesChangedPacket::create([
            'showCoordinates' => new BoolGameRule(true, false)
        ]);
        $this->getNetworkSession()->sendDataPacket($pk);
        
        # Mob
if ($this->getSession()->isMobKilled()) {
    $this->getSession()->setMobKilled(false);
    $this->getInventory()->clearAll();
    $this->getArmorInventory()->clearAll();
    $this->getEffects()->clear();
    $this->setHealth($this->getMaxHealth());

    $default = Server::getInstance()->getWorldManager()->getDefaultWorld();
    $this->teleport($default->getSafeSpawn());
}
        
        # Logout
        if ($this->getSession()->isLogout())
            $this->getSession()->setLogout(false);
    }
  
        

    private function updateScoreboard(): void
   {
        $this->scoreboard->updateTitles();
        if ($this->getScoreboardMode() === false) {
            if ($this->scoreboard->isSpawned()) $this->scoreboard->remove();
            return;
        }
        
        $tz = 'EST';
        $date = new DateTime('now', new DateTimeZone($tz));
        $date = $date->format('     §7j/n/Y | H:i:s');
        $lines = [
            TextFormat::colorize(' '.$date),
            TextFormat::colorize(' '.HCFLoader::getInstance()->getConfig()->get('scoreboard.placeholder'))
        ];
        
        #Claims 
        if ($this->getCurrentClaim() !== null) {
            
            
            $currentClaim = HCFLoader::getInstance()->getClaimManager()->getClaim($this->getCurrentClaim());
            if ($currentClaim !== null) {
                $claimName = '§a' . $this->getCurrentClaim();

                if ($currentClaim->getType() === 'spawn') {
                    $claimName = '&a' . $this->getCurrentClaim();
                    
                } elseif ($currentClaim->getType() === 'road'){
                    $claimName = '&g' . $this->getCurrentClaim();
                }elseif ($currentClaim->getType() === 'koth'){
                    $claimName = '§9KoTH ' . $this->getCurrentClaim();
                }elseif ($currentClaim->getType() === 'faction'){
                    $claimName = '§c' . $this->getCurrentClaim();
                }elseif ($currentClaim->getName() === 'Bank'){
                    $claimName = '§b' . $this->getCurrentClaim();
                }
                $lines[] = TextFormat::colorize(' §l§f| §r§cLocation§r§f: §f'. $claimName);
            }
        }else{
            
            $lines[] = TextFormat::colorize(' §l§f| §r§cLocation: §f'. ($this->getPosition()->distance($this->getWorld()->getSafeSpawn()) > 400 ? '§7Wilderness' : '§gWarzone'));
            
        }
        # ESTADISTICAS ! 
       //$lines[] = TextFormat::colorize(' §r§f| §4Kills§7: §b' . $this->getSession()->getKills());
      // $lines[] = TextFormat::colorize(' §r§c| §gPing§7: §b' . $this->getNetworkSession()->getPing() . '§bms');
        $lines[] = TextFormat::colorize(' §l§f| §r§cFaction§r§7: §a' . ($this->getSession()->getFaction() !== null ? $this->getSession()->getFaction() : '§7N-A'));
       // $lines[] = TextFormat::colorize(' §r§c| §gPrefix§7: §b[§r ' . ($this->getSession()->getPrefix() . ' §r§b]' ?? 'No prefix'));
        
# Events
      if (($sotw = HCFLoader::getInstance()->getTimerManager()->getSotw())->isActive())
            $lines[] = TextFormat::colorize(' ' . $sotw->getFormat() . Timer::format($sotw->getTime()));
        if (($points = HCFLoader::getInstance()->getTimerManager()->getPoints())->isActive())
            $lines[] = TextFormat::colorize(' ' . $points->getFormat() . Timer::format($points->getTime()));
        if (($depuracion = HCFLoader::getInstance()->getTimerManager()->getDepuracion())->isActive())
            $lines[] = TextFormat::colorize(' ' . $depuracion->getFormat() . Timer::format($depuracion->getTime()));
        if (($eotw = HCFLoader::getInstance()->getTimerManager()->getEotw())->isActive())
           $lines[] = TextFormat::colorize(' ' . $eotw->getFormat() . Timer::format($eotw->getTime()));
        if (($purge = HCFLoader::getInstance()->getTimerManager()->getPurge())->isActive())
           $lines[] = TextFormat::colorize(' ' . $purge->getFormat() . Timer::format($purge->getTime()));
        if (($airdrop = HCFLoader::getInstance()->getTimerManager()->getAirdropAll())->isActive())
            $lines[] = TextFormat::colorize(' ' . $airdrop->getFormat() . Timer::format($airdrop->getTime()));
        if (($keyall = HCFLoader::getInstance()->getTimerManager()->getKeyAll())->isActive())
            $lines[] = TextFormat::colorize(' ' . $keyall->getFormat() . Timer::format($keyall->getTime()));
        if (($keyallop = HCFLoader::getInstance()->getTimerManager()->getKeyAllOP())->isActive())
            $lines[] = TextFormat::colorize(' ' . $keyallop->getFormat() . Timer::format($keyallop->getTime()));
        if (($lootbox = HCFLoader::getInstance()->getTimerManager()->getLoobox())->isActive())
            $lines[] = TextFormat::colorize(' ' . $lootbox->getFormat() . Timer::format($lootbox->getTime()));
        if (($mistery = HCFLoader::getInstance()->getTimerManager()->getMisteryAll())->isActive())
            $lines[] = TextFormat::colorize(' ' . $mistery->getFormat() . Timer::format($mistery->getTime()));
        if (($pkg = HCFLoader::getInstance()->getTimerManager()->getPackages())->isActive())
            $lines[] = TextFormat::colorize(' ' . $pkg->getFormat() . Timer::format($pkg->getTime()));
        if (($freekits = HCFLoader::getInstance()->getTimerManager()->getFreeKits())->isActive()) 
            $lines[] = TextFormat::colorize(' ' . $freekits->getFormat() . Timer::format($freekits->getTime()));
        if (($blackhole = HCFLoader::getInstance()->getTimerManager()->getBlackHole())->isActive())
            $lines[] = TextFormat::colorize(' ' . $blackhole->getFormat() . Timer::format($blackhole->getTime()));
        if (($firerush = HCFLoader::getInstance()->getTimerManager()->getFireRush())->isActive())
    $lines[] = TextFormat::colorize(' ' . $firerush->getFormat() . Timer::format($firerush->getTime()));
        if (($overkill = HCFLoader::getInstance()->getTimerManager()->getOverKill())->isActive()) 
            $lines[] = TextFormat::colorize(' §r§f§r §fOverKill: §f' . $overkill->getFormat() . Timer::format($overkill->getTime()));
        if (($starkill = HCFLoader::getInstance()->getTimerManager()->getStarKill())->isActive())
            $lines[] = TextFormat::colorize(' ' . $starkill->getFormat() . Timer::format($starkill->getTime()));

		foreach(HCFLoader::getInstance()->getTimerManager()->getCustomTimers() as $name => $timer){
        if(!$timer instanceof TimerCustom) continue; // Cambié return por continue
        if (($custom = HCFLoader::getInstance()->getTimerManager()->getCustomTimerByName($name))->isActive())
            $lines[] = TextFormat::colorize(' §f§l|§r ' . $custom->getFormat() . Timer::format($custom->getTime()));
        }
        
				# Cooldowns - SECCIÓN CRÍTICA PARA MOSTRAR COOLDOWNS
    $session = $this->getSession();
    if ($session !== null) {
        foreach ($session->getCooldowns() as $cooldown) {
            // Verificar que el cooldown sea visible y tenga tiempo restante
            if ($cooldown->isVisible() && $cooldown->getTime() > 0) {
                $lines[] = TextFormat::colorize(' §f§l|§r ' . $cooldown->getFormat() . Timer::format($cooldown->getTime()));
            }
        }

        # Energies
        foreach ($session->getEnergies() as $energy) {
            $lines[] = TextFormat::colorize(' §f§l|§r ' . $energy->getFormat() . ($energy->getEnergy().'.0'));
        }
    }
            
        #KOTH
        if (($kothName = HCFLoader::getInstance()->getKothManager()->getKothActive()) !== null) {
            $koth = HCFLoader::getInstance()->getKothManager()->getKoth($kothName);
            
            if ($koth !== null) {
                $lines[] = TextFormat::colorize(' §f§l|§r &4' . $koth->getName() . '&r&f: &r&e' . Timer::format($koth->getProgress()));
            }
        }
        
        # 
        
        # Faction
        if ($this->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($this->getSession()->getFaction());
            
            # Focus
                   
            if ($faction->getFocus() !== null) {
                if (($targetFaction = HCFLoader::getInstance()->getFactionManager()->getFaction($faction->getFocus())) !== null) {
                $lines[] = '§7§r§r ';
                $lines[] = TextFormat::colorize(' §f§l|§r §cTeam: §9' . $targetFaction->getName());
                $lines[] = TextFormat::colorize(' §f§l|§r §cHome: §7' . ($targetFaction->getHome() !== null ? $targetFaction->getHome()->getFloorX() . ', ' . $targetFaction->getHome()->getFloorZ() : '§7Has no home'));
                $lines[] = TextFormat::colorize(' §f§l|§r §cDTR: §f' . $targetFaction->getDtr() . ' §c■');
                $lines[] = TextFormat::colorize(' §f§l|§r §cOnline: §a' . count($targetFaction->getOnlineMembers()));
                }
            }
            
            # Rally
               
           /* if (($rally = $faction->getRally()) !== null) {
                $pos = $this->getPosition();
                $x = (int)$pos->getX();
                $y = (int)$pos->getY();
                $z = (int)$pos->getZ();
                $lines[] = '§7§7§r§r ';
                $lines[] = TextFormat::colorize(' §f| §r§fRally&7: &a' . $rally[0]);
                $lines[] = TextFormat::colorize(' §f|§r §fCoords&7: &6' .$x. ', ' .$y. ', ' .$z);
            }*/
        }
        $lines[] = TextFormat::colorize('&7'.HCFLoader::getInstance()->getConfig()->get('scoreboard.placeholder'));

        if (count($lines) === 3) {
            if ($this->scoreboard->isSpawned()) $this->scoreboard->remove();
            return;
        }

       /* if (count($lines) === 20) {
            if ($this->scoreboard->isSpawned()) $this->scoreboard->remove();
            return;
        }*/
        
        if (!$this->scoreboard->isSpawned())
            $this->scoreboard->init();
        else $this->scoreboard->clear();
        
        foreach ($lines as $line => $content)
            $this->scoreboard->addLine($content.' ');
    }

    /**
     * @param int $currentTick
     */
    public function onUpdate(int $currentTick): bool
    {
        $update = parent::onUpdate($currentTick);
        
        if ($update) {
            if ($currentTick % 20 === 0) {

                # Update custom enchants
                foreach ($this->getArmorInventory()->getContents() as $armor) {
                    foreach ($armor->getEnchantments() as $enchantment) {
                        $type = $enchantment->getType();

                        if ($type instanceof Enchantment)
                            $type->giveEffect($this);
                    }
                }
                
                # Update scoreboard
                $this->updateScoreboard();
                
                # Update invisibility 
                $this->loadInvisibility();

                if ($this->getClass() !== null)
                    $this->getClass()->onRun($this);
                else {
                    foreach(ClassFactory::getClasses() as $class) {
                        if ($class->isActive($this)) {
                            $this->class = $class;
                            break;
                        }
                    }
                }
            }
            
            if ($currentTick % 40 === 0) {
                
                # Update last line
                if ($this->lastLine >= 2) {
                    $this->lastLine = 0;
                } else {
                    $this->lastLine++;
                }
            }
        }
        return $update;
    }

    public function loadInvisibility() : void
    {
        if (!$this->getEffects()->has(VanillaEffects::INVISIBILITY()))
            return;
        $metadata = clone $this->getNetworkProperties();
        $metadata->setGenericFlag(EntityMetadataFlags::INVISIBLE, false);
        $pk2 = new SetActorDataPacket();
        $pk2->syncedProperties = new PropertySyncData([], []);
        $pk2->actorRuntimeId = $this->getId();
        $pk2->metadata = $metadata->getAll();

        foreach ($this->getViewers() as $viewer) {
            if ($viewer instanceof self) {
                if ($viewer->getSession()->getFaction() === null)
                    continue;

                if ($viewer->getSession()->getFaction() === $this->getSession()->getFaction())
                    $viewer->getNetworkSession()->sendDataPacket($pk2);
            }
        }
    }
    
    protected function processMostRecentMovements() : void
    {
        $micro = microtime(true);
        
        if ($micro - $this->lastCheck > 1) {
            $this->lastCheck = $micro;
            
            foreach ($this->getArmorInventory()->getContents() as $armor) {
                foreach ($armor->getEnchantments() as $enchantment) {
                    $type = $enchantment->getType();

                    if ($type instanceof Enchantment)
                        $type->handleMove($this);
                }
            }
        }
        parent::processMostRecentMovements();
    }
   /**
     * @return string
     */ 
  
}
