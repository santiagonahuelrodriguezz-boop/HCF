<?php

declare(strict_types=1);

namespace hcf;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\Server as ServerInsano;
use hcf\entity\default\EnderPearlEntity;
use hcf\entity\projectiles\FireworksRocket;
use hcf\item\Fireworks;
use hcf\player\Player;
use hcf\player\UpdateScoreTagTask;
use hcf\HCFLoader;
use hcf\kohi\Kohi;
use hcf\session\Session;
use hcf\addons\modules\IpLogger;
use hcf\utils\time\Timer;
use hcf\crates\CrateManager;
use pocketmine\block\tile\Sign;
use pocketmine\block\utils\SignText;
use pocketmine\block\Water;
use pocketmine\entity\Location;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Armor;
use pocketmine\item\Bucket;
use pocketmine\item\EnderPearl;
use pocketmine\item\FlintSteel;
use pocketmine\item\Hoe;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\Shovel;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\utils\SignalHandler;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\ThrowSound;
use pocketmine\event\block\PlayerBucketEmptyEvent;

class HCFListener implements Listener
{
       
    /**
     * @param EntityDamageEvent $event
     * @priority HIGH
     */
    public function handleDamage(EntityDamageEvent $event): void
    {
        $cause = $event->getCause();
        $entity = $event->getEntity();

        if ($entity instanceof Player) {
            if ($event->isCancelled()) return;

            if ($entity->getSession()->getCooldown('starting.timer')) {
                $event->cancel();
                return;
            }

            if ($entity->getSession()->getCooldown('pvp.timer') !== null) {
    if (
        $cause === EntityDamageEvent::CAUSE_ENTITY_ATTACK || 
        $cause === EntityDamageEvent::CAUSE_PROJECTILE || 
        $cause === EntityDamageEvent::CAUSE_FALL ||
        $cause === EntityDamageEvent::CAUSE_FIRE ||
        $cause === EntityDamageEvent::CAUSE_FIRE_TICK ||
        $cause === EntityDamageEvent::CAUSE_LAVA ||
        $cause === EntityDamageEvent::CAUSE_SUFFOCATION ||
        $cause === EntityDamageEvent::CAUSE_DROWNING
    ) {
        $event->cancel();
        return;
    }
}
            if($entity->getCurrentClaim() === 'DeathZone')
            {
              $event->cancel();
            }
            
            
            if($entity->getCurrentClaim() === 'Nether Spawn')
            {
              $event->cancel();
            }

            if ($entity->getCurrentClaim() === 'Spawn') {
                $event->cancel();
                return;
            }

            if ($event instanceof EntityDamageByEntityEvent || $event instanceof EntityDamageByChildEntityEvent) {
                $damager = $event->getDamager();

                if ($damager instanceof Player) {
                    if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                        $event->cancel();
                        return;
                    }
                    if($damager->getCurrentClaim() === 'DeathZone')
                    {
                      $event->cancel();
                    }
                    
                    if($damager->getCurrentClaim() === 'Nether Spawn')
                    {
                      $event->cancel();
                    }

                    if ($damager->getCurrentClaim() === 'Spawn') {
                        $event->cancel();
                        return;
                    }

                    if ($entity->getSession()->getFaction() !== null && $damager->getSession()->getFaction() !== null) {
                        if ($entity->getSession()->getFaction() === $damager->getSession()->getFaction()) {
                            $damager->sendMessage(TextFormat::colorize('&eA Tu Amigo No §3' . $entity->getName() . '§e.'));
                            $event->cancel();
                            return;
                        }
                    }
                    $entity->getSession()->addCooldown('lastDamage', '', 15, false, false);

                    $entity->getSession()->addCooldown('spawn.tag', ' §r§cCombat Tag: §f', 25);
                    $damager->getSession()->addCooldown('spawn.tag', ' §r§cCombat Tag: §f', 30);
                }
            }
        }
    }
    
    public function handlePickupItem(EntityItemPickupEvent $event): void
    {
        $entity = $event->getEntity();
        $origin = $event->getOrigin();
        
        if ($entity instanceof Player) {
            $session = HCFLoader::getInstance()->getSessionManager()->getSession((string)$entity->getUniqueId());
            $owningEntity = $origin->getOwningEntity();
            
            if ($session->getCooldown('pvp.timer') !== null || $session->getCooldown('starting.timer') !== null) {
                if ($owningEntity === null || $owningEntity->getId() !== $entity->getId()) {
                    $entity->sendTip(TextFormat::colorize("§cyou have §gPvPTimer"));
                    $event->cancel();
                }
            }
        }
    }

    public function handleChat(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $message = $event->getMessage();

        if ($player instanceof Player) {
            if ($player->getSession()->getFaction() !== null && $player->getSession()->hasFactionChat()) {
                $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());

                if ($faction !== null) {
                    $event->cancel();

                    foreach ($faction->getOnlineMembers() as $member)
                        $member->sendMessage(TextFormat::colorize('§gFaction: §a ' . $player->getName() . '§d> §f' . $message));
                    return;
                }
            }
        }
    }

           public function handleCreation(PlayerCreationEvent $event): void
    {
        $event->setPlayerClass(Player::class);
    }

public function handleDeath(PlayerDeathEvent $event): void
{
    /** @var Player */
    $player = $event->getPlayer();

    if (!$player instanceof Player)
        return;
        
    $player->getSession()->addCooldown('pvp.timer', ' §r§aPvP Timer: §f', 3600); 

    $last = $player->getLastDamageCause();

    $killerXuid = null;
    $killer = null;
    $itemInHand = null;
    $message = '';

    if ($last instanceof EntityDamageByEntityEvent || $last instanceof EntityDamageByChildEntityEvent) {
        $damager = $last->getDamager();

        if ($damager instanceof Player) {
            $killerXuid = (string)$damager->getUniqueId();
            $killer = $damager->getName();
            $itemInHand = $damager->getInventory()->getItemInHand();

            $damager->getSession()->addKill();
            $damager->getSession()->addKillStreak();
            $damager->getSession()->setCrystals($damager->getSession()->getCrystals() + 1);

        if (HCFLoader::getInstance()->getTimerManager()->getDepuracion()->isActive()) {  
            $damager->getSession()->setCrystals($damager->getSession()->getCrystals() + 1);  
            $damagerName = $damager->getSession()->getName();  
            ServerInsano::getInstance()->dispatchCommand(new ConsoleCommandSender(ServerInsano::getInstance(), ServerInsano::getInstance()->getLanguage()), "key give Koth 1 ".'"'.$damagerName.'"');
        }  
          
        if (HCFLoader::getInstance()->getTimerManager()->getBlackHole()->isActive()) {
            $damager->getSession()->setCrystals($damager->getSession()->getCrystals() + 10);
            $damagerName = $damager->getName();
            ServerInsano::getInstance()->dispatchCommand(new ConsoleCommandSender(ServerInsano::getInstance(), ServerInsano::getInstance()->getLanguage()), "key give BlackHole 1 ".'"'.$damagerName.'"');
        }
        
        if (HCFLoader::getInstance()->getTimerManager()->getOverKill()->isActive()) {
            $damager->getSession()->setCrystals($damager->getSession()->getCrystals() + 3);
            $damagerName = $damager->getName();
            ServerInsano::getInstance()->dispatchCommand(new ConsoleCommandSender(ServerInsano::getInstance(), ServerInsano::getInstance()->getLanguage()), "key give Overkill 1 ".'"'.$damagerName.'"');
        }

            if (HCFLoader::getInstance()->getTimerManager()->getPoints()->isActive()) {
                $damager->getSession()->setCrystals($damager->getSession()->getCrystals() + 2);
            }

            if ($damager->getSession()->getKillStreak() > $damager->getSession()->getHighestKillStreak())
                $damager->getSession()->addHighestKillStreak(); 
            
            #Faction Check
            $damagerFactionName = $damager->getSession()->getFaction();
            if ($damagerFactionName !== null) {
                $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($damagerFactionName);
                if ($faction !== null) {
                    if (HCFLoader::getInstance()->getTimerManager()->getPoints()->isActive()) {
                        $faction->setPoints($faction->getPoints() + 2);
                    } else {
                        $faction->setPoints($faction->getPoints() + 1);
                    }
                }
            }
        }
    }
                

    if ($player->getSession()->getCooldown('spawn.tag') !== null)
        $player->getSession()->removeCooldown('spawn.tag');

    $spawnClaim = HCFLoader::getInstance()->getClaimManager()->getClaim('Spawn');
    
    if ($spawnClaim !== null && $spawnClaim->getType() === 'spawn')
        $player->getSession()->addDeath();

    $player->getSession()->setKillStreak(0);

    #Faction Check
    $factionName = $player->getSession()->getFaction();
    if ($factionName !== null) {
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($factionName);

        if ($faction !== null) {
            // ✅ FIX: Reducir DTR cuando alguien muere
            $faction->setDtr($faction->getDtr() - 1.0);
        }
        // Faction Raid
        if ($faction->getDtr() < 0.00 && !$faction->isRaidable()) {
            $faction->setRaidable(true);
            $faction->setPoints($faction->getPoints() - 5);
            
    $announcement = TextFormat::colorize('&bThe &gfaction &c&l' . $faction->getName() . ' &r&bIs now raideable!!');
    $announcement .= TextFormat::colorize("\n&l&7█&f█████&7█\n");
    $announcement .= TextFormat::colorize("&l&f███&4█&f███\n");
    $announcement .= TextFormat::colorize("&l&f███&4█&f███  &cRaid Announce\n");
    $announcement .= TextFormat::colorize("&l&f███&4█&f███  &r&6[&c" . $faction->getName() . "&6] is now raideable\n");
    $announcement .= TextFormat::colorize("&l&f████&f███  &r&a[Use /f focus " . $faction->getName() . "]\n");
    $announcement .= TextFormat::colorize("&l&f███&4█&f███\n");
    $announcement .= TextFormat::colorize("&l&7█&f█████&7█");

    ServerInsano::getInstance()->broadcastMessage($announcement);
            
            $homeCoords = $faction->getHome();
            $location = "Not set";
    if ($homeCoords !== null) {
        $location = "X: " . $homeCoords->getFloorX() . ", Y: " . $homeCoords->getFloorY() . ", Z: " . $homeCoords->getFloorZ();
    }
            $name = $faction->getName();
            
$msg = new Message();
$msg->setContent("**Faction Raid!**".Kohi::TAGGG);
$webhookUrl = Kohi::RAID_ANNOUNCE;
$webHook = new Webhook($webhookUrl);

$embed = new Embed();
$embed->setTitle("``🚨`` FACTION RAID ``🚨``");
$embed->setColor(0xFF0000);
$embed->setDescription("**Faction:** {$name} \n**Location:** {$location} \n**World:** HCF\n \n - IP: play.demonhcf
.fun\n - Port: 19132\n");
$embed->setFooter("Powered by DemonHCF"); 
$embed->setThumbnail(Kohi::IMAGE); 
$msg->addEmbed($embed);
$webHook->send($msg);

            if ($killerXuid !== null) {
                $session = HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid);

                if ($session !== null && $session->getFaction()) {
                    $fac = HCFLoader::getInstance()->getFactionManager()->getFaction($session->getFaction());

                    if ($fac !== null) {
                        $fac->setPoints($fac->getPoints() + 5);
                    }
                }
            }
        }

       // Regen time
       if (!$faction->isRaidable()) {
       $faction->setTimeRegeneration(10 * 60); // 15 minutos
        } else {
       $regenTime = $faction->getTimeRegeneration();
       $value = $regenTime + (3 * 60); // +3 minutos
       $faction->setTimeRegeneration($value < 10 * 60 ? $value : 15 * 60);
     }
   }     

        $playerKills = $player->getSession()->getKills();
$playerName = $player->getName();

if ($killer === null) {

    $message = "&c{$playerName}&4[{$playerKills}] &edied";
    $webhookMsg = "`{$playerName}[{$playerKills}] died`";

} else {

    $killerSession = HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid);
    $killerKills = $killerSession->getKills();

    if (!$itemInHand->isNull() && $itemInHand instanceof Tool) {

        $itemName = $itemInHand->getName();
        $message = "&c{$playerName}&4[{$playerKills}] &ewas slain by &9{$killer}&4[{$killerKills}] &cusing {$itemName}";

    } else {

        $message = "&c{$playerName}&4[{$playerKills}] &ewas slain by &9{$killer}&4[{$killerKills}]";

    }

    $webhookMsg = "`{$playerName}[{$playerKills}] was slain by {$killer}[{$killerKills}]`";
}

$event->setDeathMessage(TextFormat::colorize($message));


HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(
    new class($webhookMsg) extends \pocketmine\scheduler\Task {

        private string $msg;

        public function __construct(string $msg) {
            $this->msg = $msg;
        }

        public function onRun(): void {
            try {
                $webHook = new Webhook(Kohi::WEBHOOK_KILL);
                $msg = new Message();
                $msg->setContent($this->msg);
                $webHook->send($msg);
            } catch (\Exception $e) {
                
            }
        }
    },
    1 
  );
} 


    /**
     * @param PlayerExhaustEvent $event
     */
    public function handleExhaust(PlayerExhaustEvent $event): void
    {
        $player = $event->getPlayer();

        if ($player instanceof Player) {
            if ($player->getCurrentClaim() !== null) {
                $claim = HCFLoader::getInstance()->getClaimManager()->getClaim($player->getCurrentClaim());

                if ($claim !== null && $claim->getType() === 'spawn') {
                    $event->cancel();

                    if ($player->getHungerManager()->getFood() !== $player->getHungerManager()->getMaxFood())
                        $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                    return;
                }
            }

            if ($player->getSession()->hasAutoFeed()) {
                $event->cancel();

                if ($player->getHungerManager()->getFood() !== $player->getHungerManager()->getMaxFood())
                    $player->getHungerManager()->setFood($player->getHungerManager()->getMaxFood());
                return;
            }
        }
    }

 public function handleInteract(PlayerInteractEvent $event)
{
    $action = $event->getAction();
    $block = $event->getBlock();
    $player = $event->getPlayer();
    $item = $event->getItem();

    if (!$player instanceof Player)
        return;

    if ($player->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 170) {
        if ($item instanceof Bucket) {
            $event->cancel();
            return;
        }

        if ($item->getNamedTag()->getTag('pp_packages') !== null) {
            return;
        }

        if ($item->getNamedTag()->getTag('mystery_box') !== null) {
            return;
        }

        if ($item->getNamedTag()->getTag('airdrop') !== null) {
            return;
        }

        if ($block instanceof Water) {
            $event->cancel();
            return;
        }

        if ($item instanceof Fireworks) {
            $event->cancel();
            return;
        }

        if ($item instanceof Shovel) {
            $event->cancel();
            return;
        }

        if ($item instanceof Hoe) {
            $event->cancel();
            return;
        }
    }

    if ($item instanceof FlintSteel) {
        $event->cancel();
        return;
    }

 
    if ($event instanceof PlayerBucketEmptyEvent) {
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

        if ($claim === null) {
            if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400) {
                $event->cancel();
                return;
            }
        }

        if ($player->getInventory()->getItemInHand()->equals(VanillaItems::WATER_BUCKET(), false, false)) {
            $event->cancel();
            $player->sendMessage(TextFormat::colorize('§8[§4!§8] §7You cannot place blocks in this area'));
            return;
        }

        if (in_array($claim->getType(), ['spawn', 'road', 'koth', 'citadel', 'deathzone'])) {
            $event->cancel();
            $player->sendMessage(TextFormat::colorize('&cYou cannot place blocks in this area'));
            return;
        }
    }
}

    public function handleItemConsume(PlayerItemConsumeEvent $event): void
    {
      
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($player instanceof Player)

            if ($event->isCancelled())
                return;

        if ($item->getTypeId() == ItemTypeIds::GOLDEN_APPLE) {
            if ($player->getSession()->getCooldown('apple') !== null) {
                $player->sendTip('§gYou have cooldown GOLDEN_APPLE');
                $event->cancel();
                return;
            }
            $player->getSession()->addCooldown('apple', ' §r§gApple§r: §f', 15);
            $player->sendMessage("§c» §7You can't use: §gGOLDEN_APPLE");
        } elseif ($item->getTypeId() == ItemTypeIds::ENCHANTED_GOLDEN_APPLE) {
            if ($player->getSession()->getCooldown('apple.enchanted') !== null) {
                
                $player->sendTip('§gYou have cooldown ENCHANTED_GOLDEN_APPLE');
                $event->cancel();
                return;
            }
            $player->getSession()->addCooldown('apple.enchanted', ' §r§dGapple Enchanted: §f', 3600);
                    #message
            $player->sendMessage('§l§7█§f█████§7█'."\n".'§l§f███§0█§f███'."\n".'§l§f██§e███§f██'."\n".'§l§f█§e█████§f█'."\n".'§l§f█§e█████§f█'."\n".'§l§f█§e█████§f█ §r§7Cooldown: §c59:00m'."\n".'§l§7██§e███§f██ §r§7Haz usado la §gGapple Enchant'."\n".'§r§7█§f█████§7█§r');
        }
    }

    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();

        if ($item instanceof Armor) {
            $event->cancel();
            return;
        }
    }
    
    public function handleLogin(PlayerLoginEvent $event): void
    {
        $player = $event->getPlayer();
        $session = HCFLoader::getInstance()->getSessionManager()->getSession((string)$player->getUniqueId());

        if ($session === null)
            HCFLoader::getInstance()->getSessionManager()->addSession((string)$player->getUniqueId(), [
                'name' => $player->getName(),
                'faction' => null,
                'prefix' => null,
                'balance' => 0,
                'crystals' => 0,
                'cooldowns' => [],
                'energies' => [],
                'stats' => [
                'kills' => 0,
                'deaths' => 0,
                'killStreak' => 0,
                'highestKillStreak' => 0
                ]
            ]);
        else {
            if ($player->getName() !== $session->getName())
                $session->setName($player->getName());
                   }
    }
    
        /**
     * @param PlayerQuitEvent $event
     */
    public function handleQuit(PlayerQuitEvent $event): void
    {
        /** @var Player */
        $player = $event->getPlayer();

        if (!$player instanceof Player)
            return;
        $quitMessage = str_replace('{player}', $player->getName(), HCFLoader::getInstance()->getConfig()->get('quit.message'));
        $disconnectedManager = HCFLoader::getInstance()->getDisconnectedManager();

        if ($player->getSession() !== null && $player->getSession()->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($player->getSession()->getFaction());
            $faction->announce(TextFormat::colorize("&cMember offline: &f" . $player->getName() . "\n&cDTR: &f" . $faction->getDtr()));
        }

        if ($player->getSession() !== null && !$player->getSession()->isLogout()) {
            if ($player->getCurrentClaim() === 'Spawn') {
                $claim = HCFLoader::getInstance()->getClaimManager()->getClaim($player->getCurrentClaim());
            } else {
                $disconnectedManager->addDisconnected($player);
            }
        }
        $event->setQuitMessage(TextFormat::colorize($quitMessage));
    }
    
   public function handleMove(PlayerMoveEvent $event): void {
       
        $player = $event->getPlayer();
        $claim = $player->getCurrentClaim();

      
        if ($claim === 'Spawn' || $claim === 'Bank' || $claim === 'DeathZone' || $claim === 'Nether Spawn') {
         $effectManager = $player->getEffects();
         $effectManager->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 20 * 15, 1));
        } 
    }
    public function handlePlayerItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if(!$player instanceof Player) return;
        if($item instanceof EnderPearl){
            $event->cancel();
            $session = $player->getSession();
            if ($player->getCurrentClaim() === 'Citadel'){
                $player->sendTip(TextFormat::colorize('&cYou can\'t use this in Citadel &cclaim.'));
                return;
            }

            if ($session->getCooldown('enderpearl') !== null) {
                $player->sendTip(TextFormat::colorize('&gYou have cooldown enderpearl'));
                $event->cancel();
                return;
            }
            $location = $player->getLocation();

            $projectile = new EnderpearlEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $location->yaw, $location->pitch), $player);
            $projectile->setMotion($player->getDirectionVector()->multiply($item->getThrowForce()));

            $projectileEv = new ProjectileLaunchEvent($projectile);
            $projectileEv->call();
            if($projectileEv->isCancelled()){
                $projectile->flagForDespawn();
                return;
            }

            $projectile->spawnToAll();

            $location->getWorld()->addSound($location, new ThrowSound());
            $session->addCooldown('enderpearl', '§r§3Enderpearl&7: &9', 10);
            if($item->getCount() > 1){
                $item->setCount($item->getCount() - 1);
            } else {
                $item = VanillaItems::AIR();
            }
            $player->getInventory()->setItemInHand($item);
        }
    }

   public function onDeath(PlayerDeathEvent $event) : void {
        $player = $event->getPlayer();
        $session = Session::get($player);
        if($session === null) return;

        $faction = $session->getFaction();
        if($faction === null) return;

        $dtrLoss = 1.0;
        $faction->setDTR($faction->getDTR() - $dtrLoss);

        $playerName = $player->getName();
        $factionName = $faction->getName();
        ServerInsano::getInstance()->broadcastMessage(
            "§cEl jugador §f$playerName §cde la facción §6$factionName §cha muerto. DTR: §f" . $faction->getDTR()
        );

        if($faction->getDTR() <= 0){
            ServerInsano::getInstance()->broadcastMessage(
                "§4La facción §6$factionName §4ha quedado RAIDEABLE!"
            );
        }
    } // <-- aquí termina onDeath

} // <-- aquí termina HCFListener