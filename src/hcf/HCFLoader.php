<?php

declare(strict_types=1);

namespace hcf;

use hcf\vcs\data\Vouchers;
use pocketmine\utils\Config;
use hcf\entity\CustomItemEntity;
use hcf\player\disconnected\LogoutMob;
use hcf\player\disconnected\DisconnectedManager;
use hcf\prefix\PrefixManager;
use hcf\module\enchantment\EnchantmentManager;
use hcf\timer\TimerManager;
use hcf\customitems\CustomManager;
use hcf\faction\FactionManager;
use hcf\koth\KothManager;
use hcf\handler\HandlerManager;
use hcf\command\CommandManager;
use hcf\session\SessionManager;
use hcf\vcs\VoucherManager;
use hcf\module\ModuleManager;
use hcf\abilities\AbilitiesManager;
use hcf\rollback\RollbackManager;
use hcf\entity\EntityManager;
use hcf\claim\ClaimManager;
use hcf\session\Session;
use hcf\player\UpdateScoreTagTask;
use pocketmine\scheduler\AsyncTask;
use pocketmine\scheduler\ClosureTask;
use hcf\addons\AddonsManager;
use hcf\provider\Provider;
use hcf\entity\TextEntity;
use hcf\item\ItemManager;
use hcf\kohi\Kohi;
use hcf\timer\types\TimerCustom;
use hcf\timer\types\TimerKillAndKing;
use hcf\utils\serialize\Serialize;
use hcf\pm5\CustomItems;
use hcf\pm5\pm5Listener;
use hcf\clearlag\ClearLag;
use hcf\clearlag\ClearLagCommand;
use hcf\portals\PortalsHandler;
use hcf\portals\utils\PT;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\EntityDataHelper;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;
use muqsit\invmenu\InvMenuHandler;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use CortexPE\Commando\PacketHooker;
use hcf\support\SupportManager;
use hcf\bank\BankManager;
use hcf\support\provider\ProviderSupport;
use hcf\crates\CrateManager;
use hcf\abilities\entity\SullCratEntity;
use hcf\extras\EndHeightListener;
use hcf\extras\DimensionEvitListener;
use hcf\extras\BookMoveListener;
use hcf\extras\AntiSpam;
use hcf\handler\package\PackageListener;
#________________________________
use hcf\Mrd\SnowballListener;
use hcf\Mrd\AutoFixListener;
use hcf\Mrd\WallListener;
#________________________________
use hcf\command\AutoFixCommand;
#________________________________
use hcf\extras\JoinListener;
use hcf\extras\FixAllSignListener;
use hcf\extras\ScoreTagListener;
use hcf\extras\KillKeyListener;

class Welcome implements Listener
{
    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoinEvent(PlayerJoinEvent $event): void
    {
        $player = $event->getPlayer();
        $LOL = TextFormat::RED . "¡MAPA VII!";
        $title = TextFormat::YELLOW . "DemonHCF BEST SERVER HCF";

        $player->sendTitle($LOL, $title, 10, 100, 10);
        $player->sendMessage("§9");
        $player->sendMessage("§8      §4DemonHCF");
        $player->sendMessage("§8  §7Welcome to §4DemonHCF");
        $player->sendMessage("§9");
        $player->sendMessage("§9  DISCORD§7: §9 dsc.gg/Demon");
        $player->sendMessage("§g  Nick§7: §7" . $player->getName());
        $player->sendMessage("§9");
        $player->sendMessage("§r§7  Welcome to the §4Demon §7Server, a friendly HCF Server \n§r§7that eliminates the pay to win feel and implements never seen before features.");
        $event->setJoinMessage("§g[" . TextFormat::GRAY . "§a+" . TextFormat::GREEN . "§r§g] " . TextFormat::GREEN . $player->getName() . TextFormat::GREEN . "§g joined in the server.");
        
        $provider = new Provider();
        
        $provider->savePlayers();
    
    }
    
}

/**
 * Class HCFLoader
 * @package hcf
 */
class HCFLoader extends PluginBase
{
    public SupportManager $supportManager;

    public BankManager $bankManager;
    
    public PrefixManager $prefixManager;
    
    public static HCFLoader $instance;
   
    public Provider $provider;
    
    public EntityManager $entityManager;
    
    public VoucherManager $voucherManager;
    
    public ModManager $modManager;
    
    public ClaimManager $claimManager;
    
    public CommandManager $commandManager;
    
    public $shopManager; // Se asume que ShopManager está definido en otro lugar
    
    public TimerManager $TimerManager;
  
    public FactionManager $factionManager;
    
    public KothManager $kothManager;
   
    public DisconnectedManager $disconnectedManager;
    
    public SessionManager $sessionManager;
    
    public ItemManager $itemManager;
  
    public AbilitiesManager $abilitiesManager;
    
    public ModuleManager $moduleManager;
    
    public HandlerManager $handlerManager;
    
    public CustomManager $customManager;
    
    public DeathBanManager $deathbanManager;
    
    public RollbackManager $rollbackManager;
   
    public static array $enderPearl = [];

    public static array $bard_allow = [];
    
    protected function onLoad(): void
    {
        self::$instance = $this;
    }
    private TimerKillAndKing $killAndKing;
    protected function onEnable() : void
    {
        self::$instance = $this;
        $this->killAndKing = new TimerKillAndKing();
$this->getServer()->getPluginManager()->registerEvents($this->killAndKing, $this);
        // Registro de la entidad personalizada
        EntityFactory::getInstance()->register(
            SullCratEntity::class,
            function(World $world, CompoundTag $nbt) : SullCratEntity {
                return new SullCratEntity(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
            },
            ['SullCrat'] // Save ID único para esta entidad
        );
        PackageListener::setItems(); 
        
        new \hcf\module\AlwaysDay($this);
        
        self::$instance = $this;

    self::$instance = $this;
    // AutoRespawn::init();
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void {
    $this->getTimerManager()->getFireRush()->update();
}), 20);
        // Registro del evento
        $this->getServer()->getPluginManager()->registerEvents(new Welcome(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new JoinListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new KillKeyListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new ScoreTagListener($this->getScheduler()), $this);
        $this->getServer()->getPluginManager()->registerEvents(new FixAllSignListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new BookMoveListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new DimensionEvitListener($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new EndHeightListener($this), $this);
        //$this->killAndKing = new TimerKillAndKing();
//$this->getServer()->getPluginManager()->registerEvents($this->killAndKing, $this);
       // AutoRespawn::setInstance(new AutoRespawn());
      //  AutoRespawn::getInstance()->onLoad();
        //this->getServer()->getPluginManager()->registerEvents(AutoRespawn::getInstance(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new AntiSpam(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new SnowballListener(), $this);
$this->getServer()->getPluginManager()->registerEvents(new AutoFixListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new WallListener(), $this);
        Vouchers::init();
       $this->provider = new Provider;
        if (!InvMenuHandler::isRegistered())
	        InvMenuHandler::register($this);
        if(!PacketHooker::isRegistered()) {
		    PacketHooker::register($this);
	}
	  
        $this->getServer()->getCommandMap()->register("clearlag", new ClearLagCommand());
        $this->getServer()->getCommandMap()->register("autofix", new AutoFixCommand());
        
        #MANAGER
        
        $this->prefixManager = new PrefixManager;
        $this->customManager = new CustomManager;
        $this->provider = new Provider;
        $this->entityManager = new EntityManager;
        $this->claimManager = new ClaimManager;
        $this->commandManager = new CommandManager;
        $this->TimerManager = new TimerManager;
        $this->factionManager = new FactionManager;
        $this->rollbackManager = new RollbackManager;
        $this->kothManager = new KothManager;
        $this->disconnectedManager = new DisconnectedManager;
        $this->sessionManager = new SessionManager;
        $this->itemManager = new ItemManager;
        $this->abilitiesManager = new AbilitiesManager;
        $this->handlerManager = new HandlerManager;
        $this->moduleManager = new ModuleManager;
        $this->voucherManager = new VoucherManager;
        $this->supportManager = new SupportManager;
        $this->bankManager = new BankManager;

        $this->saveDefaultConfig();
        
         ClearLag::getInstance()->task();
        $worldManager = Server::getInstance()->getWorldManager();
   
      #=====LOADED WORLDS=====
        if (!$worldManager->isWorldLoaded("deathban")) {
            if ($worldManager->loadWorld("deathban")) {
                $this->getLogger()->info("El mundo DeathBan se cargó correctamente!");
                
                if (!class_exists(\muqsit\simplepackethandler\SimplePacketHandler::class)) {
        $this->getLogger()->error("¡SimplePacketHandler no está instalado!");
                    return;
                    }
            }
        }
      #=====REGISTER TASK======
       /*$players = $this->getServer()->getOnlinePlayers();
       foreach ($players as $player) {
       $this->getScheduler()->scheduleRepeatingTask(new UpdateScoreTagTask($player), 20);
          }*/
        AddonsManager::init();
        
        #=====REGISTER LISTENER=====
        $this->getServer()->getPluginManager()->registerEvents(new HCFListener(), $this);
       
        $this->getServer()->getNetwork()->setName(TextFormat::colorize(Kohi::MOTD));
        #=====UNREGISTER COMMANDS=====
        $commands = [
            'ban',
            'pl',
        ];
        
        foreach ($commands as $commandName) {
            $command = $this->getServer()->getCommandMap()->getCommand($commandName);
            
            if ($command !== null)
                $this->getServer()->getCommandMap()->unregister($command);
        }
        
        #=====SAVE PROVIDER TASK=====
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            $this->getProvider()->save();
        }), 300 * 20);
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
            #=====KOTH=====
            if (($kothName = $this->getKothManager()->getKothActive()) !== null) {
                if (($koth = $this->getKothManager()->getKoth($kothName)) !== null)
                    $koth->update();
                else
                    $this->getKothManager()->setKothActive(null);
            }
            
            /*if ($this->getTimerManager()->getRagnarok()->isActive()) {
                $players = $this->getServer()->getOnlinePlayers();
                foreach($players as $player){
                    $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 1));
                }
            }*/
                if ($this->getTimerManager()->getDepuracion()->isActive()) {
                foreach ($this->getServer()->getOnlinePlayers() as $player) {
                    $effect = $player->getEffects()->get(VanillaEffects::STRENGTH());
                    if ($effect === null || $effect->getDuration() < 40) {
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 1));
                    }
                }
            }
            #=====EVENTS=====
            $this->getTimerManager()->getSotw()->update();
            $this->getTimerManager()->getEotw()->update();
            $this->getTimerManager()->getPurge()->update();
            $this->getTimerManager()->getPoints()->update();
            $this->getTimerManager()->getDepuracion()->update();
          /*  $this->getTimerManager()->getAirdropAll()->update();
            $this->getTimerManager()->getKeyAll()->update();
            $this->getTimerManager()->getKeyAllOP()->update();
            $this->getTimerManager()->getLoobox()->update();
            $this->getTimerManager()->getOverKill()->update();
            $this->getTimerManager()->getStarKill()->update();
            $this->getTimerManager()->getBlackHole()->update();
            $this->getTimerManager()->getFireRush()->update();
            $this->getTimerManager()->getMisteryAll()->update();
            $this->getTimerManager()->getFreeKits()->update();
            $this->getTimerManager()->getPackages()->update();*/
            $this->getTimerManager()->getAirdropAll()->update();
            $this->getTimerManager()->getKeyAll()->update();
            $this->getTimerManager()->getKeyAllOP()->update();
            $this->getTimerManager()->getLoobox()->update();
            $this->getTimerManager()->getOverKill()->update();
            $this->getTimerManager()->getStarKill()->update();
            $this->getTimerManager()->getBlackHole()->update();
            $this->getTimerManager()->getFireRush()->update();
            $this->getTimerManager()->getMisteryAll()->update();
            $this->getTimerManager()->getFreeKits()->update();
            $this->getTimerManager()->getPackages()->update();
            foreach($this->getTimerManager()->getCustomTimers() as $name => $timer){
                if($timer instanceof TimerCustom)
                    $timer->update();
            } 
            #=====LOGOUT MOB=====
            foreach($this->getServer()->getWorldManager()->getDefaultWorld()->getEntities() as $entity) {
                if($entity instanceof LogoutMob) {
                    $entity->onUpdate(20);
                }
            }
                
            #=====SESSIONS=====
            foreach ($this->getSessionManager()->getSessions() as $session)
                $session->onUpdate();
                
            #=====FACTIONS=====
            foreach ($this->getFactionManager()->getFactions() as $faction)
                $faction->onUpdate();
        }), 20);
    }
          
       protected function onDisable(): void {
       ProviderSupport::getInstance()->saveData();
	    
    $this->getProvider()->save();
    $this->disconnectedManager->onDisable();
    /*foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->transfer("206.183.128.31", 2552);
        }*/
    $world = $this->getServer()->getWorldManager()->getDefaultWorld();
    foreach ($world->getEntities() as $entity) {
        if ($entity instanceof CustomItemEntity || $entity instanceof TextEntity) 
            $entity->kill();
    }
  }
    
    public static function getInstance(): HCFLoader {
        return self::$instance;
    }
    
    public function getAbilitiesManager(): AbilitiesManager {
        return $this->abilitiesManager;
        }
    public function getProvider(): Provider {
        return $this->provider;
    }

    public function getPrefixManager(): PrefixManager {
        return $this->prefixManager;
    }
    
    public function getEntityManager(): EntityManager {
        return $this->entityManager;
    }
    
    public function getClaimManager(): ClaimManager {
        return $this->claimManager;
    }
    
    public function getCustomManager(): CustomManager {
        return $this->customManager;
    }
    
    public function getCommandManager(): CommandManager {
        return $this->commandManager;
    }
    
    public function getDeathBanManager(): DeathBanManager {
        return $this->deathbanManager;
    }
    
    public function getTimerManager(): TimerManager {
        return $this->TimerManager;
    }
    
    public function getFactionManager(): FactionManager {
        return $this->factionManager;
    }
    
    public function getKothManager(): KothManager {
        return $this->kothManager;
    }
    
    public function getDisconnectedManager(): DisconnectedManager {
        return $this->disconnectedManager;
    }
    
    public function getSessionManager(): SessionManager {
        return $this->sessionManager;
    }
    
    public function getItemManager(): ItemManager {
        return $this->itemManager;
    }
    
    public function getModuleManager(): ModuleManager {
        return $this->moduleManager;
    }
    
    public function getRollbackManager(): RollbackManager {
        return $this->rollbackManager;
    }
    
    public function getHandlerManager(): HandlerManager {
        return $this->handlerManager;
    }
    
    public function getVoucherManager(): VoucherManager {
        return $this->voucherManager;
    }
    
    public function getBankManager(): BankManager {
        return $this->bankManager;
    }

    public function getSupportManager(): SupportManager {
        return $this->supportManager;
    }
}
