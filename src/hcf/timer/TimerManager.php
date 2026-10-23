<?php

declare(strict_types=1);

namespace hcf\timer;

use hcf\timer\command\AirdropCommand;
use hcf\timer\command\EotwCommand;
use hcf\timer\command\KeyallCommand;
use hcf\timer\command\KeyallopCommand;
use hcf\timer\command\LooboxCommand;
use hcf\timer\command\MisteryCommand;
use hcf\timer\command\PkgallCommand;
use hcf\timer\command\PurgeCommand;
use hcf\timer\command\SotwCommand;
use hcf\timer\command\x2PointsCommand;
use hcf\timer\types\TimerKeepKill;
use hcf\timer\command\KeepKillCommand;
use hcf\timer\command\DepuracionCommand;
use hcf\timer\types\TimerAirdropAll;
use hcf\timer\types\TimerEotw;
use hcf\timer\types\TimerDepuracion;
use hcf\timer\types\TimerKeyAll;
use hcf\timer\types\TimerKeyAllOP;
use hcf\timer\types\TimerLoobox;
use hcf\timer\types\TimerMisteryAll;
use hcf\timer\types\TimerPackages;
use hcf\timer\types\TimerPurge;
use hcf\timer\command\BlackHoleCommand;
use hcf\timer\types\TimerBlackHole;
use hcf\timer\types\TimerSotw;
use hcf\timer\types\Timerx2Points;
use hcf\timer\types\TimerOverKill;
use hcf\timer\types\TimerStarKill;
use hcf\timer\command\StarKillCommand;
use hcf\timer\command\OverKillCommand;
use hcf\timer\types\TimerFireRush;
use hcf\timer\command\FireRushCommand;
use hcf\timer\types\TimerFreeKits;
use hcf\timer\command\FreeKitsCommand;
use hcf\HCFLoader;
use hcf\timer\command\CustomTimerCommand;
use hcf\timer\types\TimerCustom;

/**
 * Class TimerManager
 * @package hcf\timer
 */
class TimerManager
{
    
    private TimerSotw $sotw;
    
    private TimerEotw $eotw;

    private TimerPurge $purge;
    
    private TimerFireRush $fireRush;
   
    private TimerOverKill $overkill;
    
    private Timerx2Points $points;
    
    private TimerStarKill $starkill;
    
    private TimerFreeKits $freeKits;
    
    private TimerDepuracion $depuracion;
    private TimerAirdropAll $airdropAll;
    private TimerKeyAll $keyAll;
    private TimerKeepKill $keepKill;
    private TimerKeyAllOP $keyAllOP;
    private TimerLoobox $loobox;
    private TimerBlackHole $blackhole;
    private TimerMisteryAll $misteryAll;
    private TimerPackages $packages;

    private array $customTimers = [];
    
    /**
     * TimerManager construct.
     */
    public function __construct()
    {
        # Setup main events
        $this->sotw = new TimerSotw;
        $this->eotw = new TimerEotw;
        $this->purge = new TimerPurge;
        $this->overkill = new TimerOverKill;
        $this->fireRush = new TimerFireRush();
        $this->freeKits = new TimerFreeKits;
        $this->starkill = new TimerStarKill;
        $this->points = new Timerx2Points;
        $this->depuracion = new TimerDepuracion;
        $this->airdropAll = new TimerAirdropAll;
        $this->blackhole = new TimerBlackHole;
        $this->keyAll = new TimerKeyAll;
        $this->keyAllOP = new TimerKeyAllOP;
        $this->keepKill = new TimerKeepKill;
        $this->loobox = new TimerLoobox;
        $this->misteryAll = new TimerMisteryAll;
        $this->packages = new TimerPackages;
        # Register commands
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new EotwCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new DepuracionCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new x2PointsCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new SotwCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new PurgeCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new CustomTimerCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new EventCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new AirdropCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new KeyallCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new KeyallopCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new BlackHoleCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new OverKillCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new FireRushCommand(HCFLoader::getInstance()));
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new KeepKillCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new FreeKitsCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new LooboxCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new MisteryCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new PkgallCommand());
        HCFLoader::getInstance()->getServer()->getCommandMap()->register('HCF', new StarKillCommand(HCFLoader::getInstance()));
        # Register listener
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents(new TimerListener(), HCFLoader::getInstance());
    }
    
    /**
     * @return TimerSotw
     */
    public function getSotw(): TimerSotw
    {
        return $this->sotw;
    }
    
    public function getPoints(): Timerx2Points
    {
        return $this->points;
    }
    
    public function getDepuracion(): TimerDepuracion
    {
        return $this->depuracion;
    }
    
    public function getOverKill(): TimerOverKill
    {
        return $this->overkill;
    }
    
    public function getKeepKill(): TimerKeepKill
    {
        return $this->keepKill;
    }
    
    public function getFireRush(): TimerFireRush
{
    return $this->fireRush;
    }
    
    public function getStarKill(): TimerStarKill
    {
        return $this->starkill;
    }
    
    public function getBlackHole(): TimerBlackHole
    {
        return $this->blackhole;
    }
    /**
     * @return TimerEotw
     */
    public function getEotw(): TimerEotw
    {
        return $this->eotw;
    }

    public function getPurge(): TimerPurge
    {
        return $this->purge;
    }

    public function getAirdropAll(): TimerAirdropAll
    {
        return $this->airdropAll;
    }

    public function getKeyAll(): TimerKeyAll
    {
        return $this->keyAll;
    }

    public function getKeyAllOP(): TimerKeyAllOP
    {
        return $this->keyAllOP;
    }

    public function getLoobox(): TimerLoobox
    {
        return $this->loobox;
    }

    public function getMisteryAll(): TimerMisteryAll
    {
        return $this->misteryAll;
    }

    public function getPackages(): TimerPackages
    {
        return $this->packages;
    }
    
    public function getFreeKits(): TimerFreeKits
    {
        return $this->freeKits;
    }

    public function getCustomTimers(): array {
        return $this->customTimers;
    }

    public function getCustomTimerByName(string $name): TimerCustom {
        return $this->customTimers[$name];
    }

    public function addCustomTimer(string $name): void {
        $this->customTimers[$name] = new TimerCustom($name);
    }

    public function removeCustomTimer(string $name): void {
        unset($this->customTimers[$name]);
    }

    public function hasCustomTimer(string $name): bool {
        return isset($this->customTimers[$name]);
    }

}