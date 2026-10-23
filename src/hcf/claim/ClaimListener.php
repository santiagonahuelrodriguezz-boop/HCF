<?php

declare(strict_types=1);

namespace hcf\claim;

use hcf\entity\EnderpearlEntity;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\block\FenceGate;
use pocketmine\block\EnderChest;
use pocketmine\block\Chest;
use pocketmine\block\Trapdoor;
use pocketmine\block\Door;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\tile\Sign;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Snowball;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\EnderPearl;
use pocketmine\player\GameMode;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\WorldException;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

/**
 * Class ClaimListener
 * @package hcf\claim
 */
class ClaimListener implements Listener
{

    /** @var string */
    const DEATHBAN = '&e(&cDeathban&e)';
    /** @var string */
    const NO_DEATHBAN = '&e(&aNon-Deathban&e)';

    public function handleChat(PlayerChatEvent $event): void
{
    /** @var Player $player */
    $player = $event->getPlayer();
    $message = $event->getMessage();
    
    if ($message == "accept") {
        $event->cancel();
        
        if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($player->getName())) !== null) {
            if (!$creator->isValid()) return;
            
            if ($creator->getType() === 'faction') {
                $factionName = $player->getSession()?->getFaction();
                if ($factionName === null) {
                    $player->sendMessage(TextFormat::colorize('§7[§b!§7] §cYou are not in a faction!'));
                    return;
                }

                $faction = $factionName !== null ? HCFLoader::getInstance()->getFactionManager()->getFaction($factionName) : null;
                $balance = $faction->getBalance() - $creator->calculateValue();
                
                if ($balance < 0) {
                    $player->sendMessage(TextFormat::colorize('§7[§b!§7] §7Your faction does not have enough §amoney §7to pay the claim'));
                    return;
                }

                $faction->setBalance($balance);
            }

            $creator->deleteCorners($player);
            HCFLoader::getInstance()->getClaimManager()->createClaim($creator->getName(), $creator->getType(), $creator->getMinX(), $creator->getMaxX(), $creator->getMinZ(), $creator->getMaxZ(), $creator->getWorld());
            $player->sendMessage(TextFormat::colorize('§8[§b!§8] §gYou have made the &aclaim §gthe opclaim !' . $creator->getName()));
            HCFLoader::getInstance()->getClaimManager()->removeCreator($player->getName());

            foreach ($player->getInventory()->getContents() as $slot => $i) {
                if ($i->getNamedTag()->getTag('claim_type')) {
                    $player->getInventory()->clear($slot);
                    break;
                }
            }
        }
        return;
    }

    if ($message == "cancel") {
        $event->cancel();
        
        if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($player->getName())) !== null) {
            $creator->deleteCorners($player);
            HCFLoader::getInstance()->getClaimManager()->removeCreator($player->getName());
            $player->sendMessage(TextFormat::colorize('§7[§b!§7] §7You have canceled the claim'));
        } else {
            $player->sendMessage(TextFormat::colorize('§7[§b!§7] §cYou are not in claim mode yet'));
        }
        return;
    }
}


public function handleBreak(BlockBreakEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

        if ($event->isCancelled())
            return;

        if ($player->isGod())
            return;

        if ($claim === null) {
            if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400)
                $event->cancel();
            return;
        }

    if ($claim !== null && in_array($claim->getType(), ['spawn', 'road', 'koth', 'citadel', 'custom', 'deathzone', 'bank'])) {
        $event->cancel();
        $player->sendMessage(TextFormat::colorize('§8[§4!§8] §cYou cannot §gbreak blocks §cin this area'));
        return;
    }
    
    if ($claim !== null && !HCFLoader::getInstance()->getTimerManager()->getEotw()->isActive() && $player->getSession()->getFaction() !== $claim->getName()) {
        $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($claim->getName());

        if ($faction !== null && $faction->getDtr() > 0.00) {
            $event->cancel();
            $player->sendMessage(TextFormat::colorize('§8[§4!§8] §7You cannot §gbreak blocks §c in §a' . $claim->getName() . ' §cterritory'));
       }
    }
}
    
    private function teleportBack(Player $player): void {
    $plugin = HCFLoader::getInstance();
    $originalPosition = $player->getPosition();

    $handler = $plugin->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(
        function () use ($player, $originalPosition, &$handler): void {
            $player->teleport($originalPosition);

            static $ticks = 0;
            $ticks++;

            if ($ticks >= 0 && isset($handler)) {
                $handler->cancel();
            }
        }
    ), 1, 1);
    }
    
    public function handlePlace(BlockPlaceEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $block = $event->getBlockAgainst();
        $item = $event->getItem();
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

        if ($block->getTypeId() === VanillaBlocks::TNT()->getTypeId()){
            $event->cancel();
        }

        if ($event->isCancelled())
            return;

        if ($player->isGod())
            return;

        if ($item->getNamedTag()->getTag('pp_packages') !== null) {
            return;
        }

        if ($item->getNamedTag()->getTag('mystery_box') !== null) {
            return;
        }

        if ($item->getNamedTag()->getTag('airdrop') !== null) {
            return;
        }
  			
        if ($claim === null) {
            if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400)
                $event->cancel();
            return;
        }

        if (in_array($claim->getType(), ['spawn', 'road', 'koth', 'citadel', 'custom', 'deathzone', 'bank'])) {
            $event->cancel();
            $player->sendMessage(TextFormat::colorize('§8[§g!§8] §cYou cannot §bplace blocks §cin this area'));
            return;
        }

        if (!HCFLoader::getInstance()->getTimerManager()->getEotw()->isActive() && $player->getSession()->getFaction() !== $claim->getName()) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($claim->getName());

            if ($faction !== null && $faction->getDtr() > 0.00) {
                $event->cancel();
                $player->sendMessage(TextFormat::colorize('§8[§4!§8] §cYou cannot §bplace blocks §cin §g' . $claim->getName() . ' §cterritory'));
            }
        }
    }
      /**
     * @param PlayerBucketEmptyEvent $event
     */
  public function hadleItemInterect(PlayerBucketEmptyEvent $event): void
{
    /** @var Player $player */
    $player = $event->getPlayer();
    $block = $event->getBlockClicked();
    $spawn = $player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3();
    
    $distanceFromSpawn = $block->getPosition()->distance($spawn);
    
    if ($event->isCancelled() || $player->isGod()) {
        return;
    }
    
    if ($player->getInventory()->getItemInHand()->equals(VanillaItems::WATER_BUCKET(), false, false) && $distanceFromSpawn < 400) {
        $event->cancel();
        $player->sendMessage(TextFormat::colorize('§8[§4!§8] §cYou cannot place &3water &cin this area.'));
        return;
    }
   if ($player->getInventory()->getItemInHand()->equals(VanillaItems::LAVA_BUCKET(), false, false) && $distanceFromSpawn < 400) {
    $event->cancel();
    $player->sendMessage(TextFormat::colorize('§8[§4!§8] §cYou cannot place &6lava &cin this area.'));
    return;
   }
}

/**
 * @param EntityTeleportEvent $event
 */
public function handleTeleport(EntityTeleportEvent $event): void
{
    $entity = $event->getEntity();
    $to = $event->getTo();

    if (!$entity instanceof Player)
        return;
    $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($to);

    if ($claim === null)
        return;

    if ($entity->getSession()->getCooldown('spawn.tag') !== null) {
        if ($claim->getType() == 'spawn') {
            $event->cancel();
            $entity->sendMessage(TextFormat::colorize('§8[§g!§8] §cYou have §pSpawn Tag. §cYou cannot teleport to this location'));
            return;
        }
    } elseif ($entity->getSession()->getCooldown('pvp.timer') !== null) {
        if ($claim->getType() === 'faction' && $entity->getSession()->getFaction() !== $claim->getName()) {
            $event->cancel();
            $entity->sendMessage(TextFormat::colorize('§8[§g!§8] §cYou have §aPvPTimer. §cYou cannot teleport to this location'));
            return;
        }
    }
        $entity->setCurrentClaim($claim->getName());
    }

    /**
     * @param PlayerDropItemEvent $event
     */
 public function handleDropItem(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if (HCFLoader::getInstance()->getClaimManager()->getCreator($player->getName()) !== null) {
            if ($item->getNamedTag()->getTag('claim_type'))
                $event->cancel();
        }

        if ($player instanceof Player) {
            if ($player->getCurrentClaim() === 'Spawn') {
                $event->cancel();
                 $player->sendTip('§cYou cannot throw items at the Spawn');
            }
        }
    }

    public function handleFall(EntityDamageEvent $event) {
        $player = $event->getEntity();
        if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
            if ($player instanceof Player)
            if ($player->getCurrentClaim() === 'Spawn' || $player->getCurrentClaim() === 'DeathZone') {

                $event->cancel();
            }
        }
    }

    public function ItemUse(PlayerItemUseEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($player instanceof Player)
        if ($player->getCurrentClaim() === 'Spawn') {

            if ($item instanceof EnderPearl) {
                $event->cancel();
            }

            if ($item instanceof Snowball) {
                $event->cancel();
            }
        }

    }

    /**
     * @param PlayerInteractEvent $event
     */
    private array $claimCooldown = [];
    public function handleInteract(PlayerInteractEvent $event): void
    {
        $action = $event->getAction();
        $block = $event->getBlock();
        /** @var Player $player */
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($block->getPosition());
        
      if ($claim !== null && $claim->getType() === "faction") {
      if ($block instanceof Sign) {
        return;
            }
        }

    if ($claim !== null) {
        $tile = $player->getWorld()->getTile($block->getPosition());
        if ($tile instanceof Sign) {
            return;
        }
    }
        
        if ($claim !== null && $claim->getName() === "Bank") {
            if($block instanceof Chest || $block instanceof EnderChest || $block instanceof Door || $block instanceof FenceGate || $block instanceof Trapdoor){
                return;
            }
        }

            if (($creator = HCFLoader::getInstance()->getClaimManager()->getCreator($player->getName())) !== null) {
        if ($item->getNamedTag()->getTag('claim_type') !== null) {
            $event->cancel();

            if (isset($this->claimCooldown[$player->getName()])) return;
            $this->claimCooldown[$player->getName()] = true;
            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new \pocketmine\scheduler\ClosureTask(function() use ($player) {
                unset($this->claimCooldown[$player->getName()]);
            }), 20);

            if (($claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($block->getPosition())) !== null && ($claim->getType() !== 'koth' || $claim->getName() !== $creator->getName())) {
                $player->sendMessage(TextFormat::colorize('&7- You cannot make a claim in an area that is already &fclaiming'));
                return;
            }

            if ($creator->getType() === 'faction') {
                if ($block->getPosition()->distance($player->getServer()->getWorldManager()->getDefaultWorld()->getSafeSpawn()->asVector3()) < 400) {
                    $player->sendMessage(TextFormat::colorize('&7- You can\'t &fclaim &7in this position'));
                    return;
                }
            }

            if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
                if ($creator->getFirst() === null) {
                    $creator->calculate($block->getPosition(), $player);
                    $player->sendMessage(TextFormat::colorize('&7[&6!&7] &7You have successfully selected the &3first &7position.'));

                } else {
                    $result = $creator->calculate($block->getPosition(), $player, false);

                    if (!$result) {
                        $player->sendMessage(TextFormat::colorize('&7- The position was not selected in the same world'));
                        HCFLoader::getInstance()->getClaimManager()->removeCreator($player->getName());

                        foreach ($player->getInventory()->getContents() as $slot => $i) {
                            if ($i->getNamedTag()->getTag('claim_type')) {
                                $player->getInventory()->clear($slot);
                                break;
                            }
                        }
                        return;
                    }

                        if ($creator->calculateClaim($creator->getFirst(), $block->getPosition())) {
                            if ($creator->getType() === 'capzone') {
                                return;
                            }
                            $player->sendMessage(TextFormat::colorize('§8[§g!§8] §eThe position was selected in other faction'));
                            return;
                        }

                        $player->sendMessage(TextFormat::colorize('§8[§g!§8] §gYou have successfully selected the &3second §cposition.'));

                        if ($creator->getType() === 'faction') {
                            $player->sendMessage(TextFormat::colorize('§4» &cThe price of your claim is §a$' . $creator->calculateValue() . '. §cWrite §a"accept" §cin chat to accept the claim'));
                        }
                    }
                }
            }
        }

        if ($item instanceof EnderPearl) {
            if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $block instanceof FenceGate) {
                $event->cancel();
                $session = $player->getSession();

                if ($player->getCurrentClaim() === 'Citadel') {
                    $player->sendMessage(TextFormat::colorize('§c You can\'t use this in §dCitadel §cclaim.'));
                    return;
                }
            }
        }

        if ($player->isGod())
            return;
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($block->getPosition());

        if ($claim === null)
            return;
        
            if (!HCFLoader::getInstance()->getTimerManager()->getEotw()->isActive() && $player->getSession()->getFaction() !== $claim->getName() && $claim->getType() !== 'spawn') {
                $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($claim->getName());

                if ($faction !== null && $faction->getDtr() > 0.00 && !HCFLoader::getInstance()->getTimerManager()->getPurge()->isActive()) {
            $event->cancel();

    if ($action === PlayerInteractEvent::RIGHT_CLICK_BLOCK) {
        if ($block instanceof FenceGate) {
            if (HCFLoader::getInstance()->getTimerManager()->getPurge()->isActive()) return;

            $distance = $player->getPosition()->distance($block->getPosition());

            if ($distance <= 3 && !$block->isOpen()) {
            $this->teleportBack($player);
                    }
                }
            }
        }
    }
}
    /**
     * @param PlayerJoinEvent $event
     */
    public function handleJoin(PlayerJoinEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($player->getPosition());

        if ($claim !== null)
            $player->setCurrentClaim($claim->getName());
    }

    /**
     * @param PlayerMoveEvent $event
     * @throws WorldException
     */
    public function handleMove(PlayerMoveEvent $event): void
    {
        /** @var Player $player */
        $player = $event->getPlayer();
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($player->getPosition());

        $leaving = self::DEATHBAN;
        $entering = self::DEATHBAN;

        if ($event->isCancelled())
            return;
        if (!$this->isBorderLimit($player->getPosition())) {
            $player->teleport($this->correctPosition($player->getPosition()));
        }

        if ($claim === null) {
            if ($player->getCurrentClaim() !== null) {
                $currentClaim = HCFLoader::getInstance()->getClaimManager()->getClaim($player->getCurrentClaim());
                $leavingName = '&c' . $player->getCurrentClaim();

                if ($currentClaim !== null) {
                    if ($currentClaim->getType() === 'spawn') {
                        $leaving = self::NO_DEATHBAN;
                        $leavingName = '&a' . $player->getCurrentClaim();

                        if ($player->getSession()->getCooldown('pvp.timer') !== null && $player->getSession()->getCooldown('pvp.timer')->isPaused())
                            $player->getSession()->getCooldown('pvp.timer')->setPaused(false);
                    } elseif ($currentClaim->getType() === 'road') {
                        $leavingName = '&9' . $player->getCurrentClaim();
                    }elseif ($currentClaim->getType() === 'koth'){
                        $leavingName = '&5KoTH ' . $player->getCurrentClaim();
                    }
                }
                $player->sendTip(TextFormat::colorize('&eNow leaving: ' . $leavingName . ' ' . $leaving));
                $player->sendTip(TextFormat::colorize('&eNow entering:&c ' . ($player->getPosition()->distance($player->getWorld()->getSafeSpawn()) > 400 ? 'Wilderness' : 'Warzone') . ' ' . $entering));

            //$player->setGamemode(GameMode::SURVIVAL());

            $player->setCurrentClaim(null);
            }
            return;
        }

        if ($player->getCurrentClaim() !== null && $claim->getName() === $player->getCurrentClaim())
            return;

        if ($player->getCurrentClaim() !== null) {
            $currentClaim = HCFLoader::getInstance()->getClaimManager()->getClaim($player->getCurrentClaim());

            if ($currentClaim !== null) {
                $leaving = self::NO_DEATHBAN;
                $leavingName = '&a' . $player->getCurrentClaim();

                if ($currentClaim->getType() === 'spawn') {
                    if ($player->getSession()->getCooldown('pvp.timer') !== null && $player->getSession()->getCooldown('pvp.timer')->isPaused())
                        $player->getSession()->getCooldown('pvp.timer')->setPaused(false);
                } elseif ($currentClaim->getType() === 'road') {
                    $leavingName = '&a' . $player->getCurrentClaim();
                } elseif ($currentClaim->getType() === 'koth'){
                    $leavingName = '&4KoTH ' . $player->getCurrentClaim();
                }
                $player->sendTip(TextFormat::colorize('&eNow leaving: ' . $leavingName . ' ' . $leaving));
            }
        }
        $enteringName = '&c' . $claim->getName();

        if ($claim->getType() === 'spawn') {
            $entering = self::NO_DEATHBAN;
            $enteringName = '&a' . $claim->getName();

            if ($player->getSession()->getCooldown('spawn.tag') !== null) {
                $event->cancel();
                return;
            }

      
        $factionManager = HCFLoader::getInstance()->getFactionManager();
        $newFaction = $factionManager->getFaction($claim->getName());

        if ($newFaction && $newFaction->getDtr() <= 0) {
        
            //$player->setGamemode(GameMode::SURVIVAL());
        } else {
          
            //$player->setGamemode(GameMode::ADVENTURE());
        }

            if ($player->getSession()->getCooldown('pvp.timer') !== null && !$player->getSession()->getCooldown('pvp.timer')->isPaused())
                $player->getSession()->getCooldown('pvp.timer')->setPaused(true);
        } elseif ($claim->getType() === 'road'){
            $enteringName = '&a' . $claim->getName();
        }elseif ($claim->getType() === 'koth'){
            $enteringName = '&5KoTH ' . $claim->getName();
        }else {
       
        $factionManager = HCFLoader::getInstance()->getFactionManager();
        $newFaction = $factionManager->getFaction($claim->getName());

        if ($newFaction && $newFaction->getDtr() <= 0) {
         
            //$player->setGamemode(GameMode::SURVIVAL());
        } else {
           
            $playerFaction = $player->getSession()->getFaction();
            if ($playerFaction === $claim->getName()) {
                
                //$player->setGamemode(GameMode::SURVIVAL());
            } else {
                
                //$player->setGamemode(GameMode::ADVENTURE());
            }
        }
            if ($player->getSession()->getCooldown('pvp.timer') !== null) {
                $event->cancel();
                return;
            }

            if ($player->getSession()->getCooldown('pvp.timer') !== null && $player->getSession()->getFaction() !== $claim->getName()) {
                $event->cancel();
                return;
            }
        }
        $player->sendTip(TextFormat::colorize('&eNow entering: ' . $enteringName . ' ' . $entering));
        $player->sendTip(TextFormat::colorize('&eNow leaving:&c ' . ($player->getPosition()->distance($player->getWorld()->getSafeSpawn()) > 400 ? 'Wilderness' : 'Warzone') . ' ' . $entering));
        $player->setCurrentClaim($claim->getName());
    }

    protected function isBorderLimit(Vector3 $position): bool
    {
        $border = 1500;
        return $position->getFloorX() >= -$border && $position->getFloorX() <= $border && $position->getFloorZ() >= -$border && $position->getFloorZ() <= $border;
    }

    protected function correctPosition(Vector3 $position): Vector3
    {
        $border = 1300;

        $x = $position->getFloorX();
        $y = $position->getFloorY();
        $z = $position->getFloorZ();

        $xMin = -$border;
        $xMax = $border;

        $zMin = -$border;
        $zMax = $border;

        if ($x <= $xMin) {
            $x = $xMin + 4;
        } elseif ($x >= $xMax) {
            $x = $xMax - 4;
        }
        if ($z <= $zMin) {
            $z = $zMin + 4;
        } elseif ($z >= $zMax) {
            $z = $zMax - 4;
        }
        $y = 72;
        return new Vector3($x, $y, $z);
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function handleQuit(PlayerQuitEvent $event): void
    {
        $player = $event->getPlayer();

        if (HCFLoader::getInstance()->getClaimManager()->getCreator($player->getName()) !== null) {
            HCFLoader::getInstance()->getClaimManager()->removeCreator($player->getName());

            foreach ($player->getInventory()->getContents() as $slot => $i) {
                if ($i->getNamedTag()->getTag('claim_type')) {
                    $player->getInventory()->clear($slot);
                    break;
                }
            }
        }
    }
}
