<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace hcf\player\disconnected;

use hcf\player\Player;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\HCFLoader;
use hcf\kohi\Kohi;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\entity\Villager;
use hcf\utils\time\Timer;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class LogoutMob extends Villager{

    private static ?Player $player = null;
    private static $inventory;
    private static $armorinventory;
    private ?Player $lastHit = null;
    private int $time = 30;

    public static function getNetworkTypeId() : string
    {
        return EntityIds::VILLAGER;
    }

    public function getName() : string{
        return "Villager";
    }

    protected function initEntity(CompoundTag $nbt) : void{
        parent::initEntity($nbt);
    }

    public static function setPlayer(Player $players) {
        self::$player = $players;
    }

    public static function setInventory(array $items) {
        self::$inventory = $items;
    }

    public static function setInventoryArmor(array $items) {
        self::$armorinventory = $items;
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array
    {
        $drops = [];
        $disconnected = self::$player;
        
        if ($disconnected !== null) {
            return array_merge(self::$inventory, self::$armorinventory);
        }
        return $drops;
    }

    public function onUpdate(int $currentTick): bool
    {
        $hasUpdate = parent::onUpdate($currentTick);
        $disconnected = self::$player;

        if ($hasUpdate) {
            if ($currentTick % 20 === 0) {
                if ($disconnected !== null) {
                    $this->time--;
                    $this->setNameTag(TextFormat::colorize('&7(Combat-Logger)&c ' . $disconnected->getName() . ' &7- &c' . Timer::convert($this->time)));

                    if ($this->time <= 0) {
                        HCFLoader::getInstance()->getDisconnectedManager()->removeDisconnected((string)$disconnected->getUniqueId());
                        $this->flagForDespawn();
                        return true;
                    }
                } else {
                    $this->flagForDespawn();
                    return true;
                }
            }
        }
        return $hasUpdate;
    }

    /**
     * @param EntityDamageEvent $source
     */
    public function attack(EntityDamageEvent $source): void
    {
        $cause = $source->getCause();
        $disconnected = self::$player;

        if ($disconnected !== null) {
            $session = $disconnected->getSession();

            if ($session !== null) {
                if ($cause !== EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
                    $source->cancel();
                    return;
                }

                if ($source instanceof EntityDamageByEntityEvent) {
                    $damager = $source->getDamager();

                    if ($damager instanceof Player) {
                        if ($damager->getName() === $session->getName()) {
                            $source->cancel();
                            return;
                        }

                        if ($damager->getCurrentClaim() === 'Spawn') {
                            $source->cancel();
                            return;
                        }

                        if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                            $source->cancel();
                            return;
                        }

                        if ($session->getFaction() !== null && $damager->getSession()->getFaction() !== null) {
                            if ($session->getFaction() === $damager->getSession()->getFaction()) {
                                $source->cancel();
                                return;
                            }
                        }

                        $this->lastHit = $damager;
                        $this->time = 15;

                        $session->addCooldown('spawn.tag', ' §rCombat Tag: §7', 30);
                        $damager->getSession()->addCooldown('spawn.tag', ' §rCombat Tag: §7', 30);
                    }
                }
            }
        }
        parent::attack($source);
    }

    protected function onDeath(): void
    {
        parent::onDeath();
        $disconnected = self::$player;

        if ($disconnected === null)
            return;
        $session = $disconnected->getSession();
        $killerXuid = null;
        $killer = null;
        $itemInHand = null;
        $message = '';
        $damager = $this->lastHit;

        if ($damager instanceof Player) {
            $killerXuid = (string)$damager->getUniqueId();
            $killer = $damager->getName();
            $itemInHand = $damager->getInventory()->getItemInHand();

            $damager->getSession()->addKill();
            $damager->getSession()->addKillStreak();

            if ($damager->getSession()->getKillStreak() > $damager->getSession()->getHighestKillStreak())
                $damager->getSession()->addHighestKillStreak();

            if ($damager->getSession()->getFaction() !== null) {
                $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($damager->getSession()->getFaction());
                $faction->setPoints($faction->getPoints() + 1);
            }
        }
        $session->setMobKilled(true);
        $session->removeCooldown('spawn.tag');
        $session->addDeath();
        $session->setKillStreak(0);
        $session->addCooldown('pvp.timer', '§r §aPvP Timer: §7', 60 * 60, true);

        if ($session->getFaction() !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($session->getFaction());

            $faction->setPoints($faction->getPoints() - 1);
            $faction->setDtr($faction->getDtr() - 1.0);
            $faction->announce(TextFormat::colorize('&cMember Death: &f' . $session->getName() . "\n" . '&cDTR: &f' . $faction->getDtr()));

            # Faction Raid
            if ($faction->getDtr() < 0.00 && !$faction->isRaidable()) {
                $faction->setRaidable(true);
                $faction->setPoints($faction->getPoints() - 5);
            
    $announcement = TextFormat::colorize('&bThe &gfaction &c&l' . $faction->getName() . ' &r&bHa quedado raideable!!');
    $announcement .= TextFormat::colorize("\n&l&7█&f█████&7█\n");
    $announcement .= TextFormat::colorize("&l&f███&4█&f███\n");
    $announcement .= TextFormat::colorize("&l&f███&4█&f███  &cRaid Announce\n");
    $announcement .= TextFormat::colorize("&l&f███&4█&f███  &r&6[&c" . $faction->getName() . "&6] is now raideable\n");
    $announcement .= TextFormat::colorize("&l&f████&f███  &r&a[Use /f focus " . $faction->getName() . "]\n");
    $announcement .= TextFormat::colorize("&l&f███&4█&f███\n");
    $announcement .= TextFormat::colorize("&l&7█&f█████&7█");

    Server::getInstance()->broadcastMessage($announcement);
            
            $homeCoords = $faction->getHome();
            $location = "Not set";
    if ($homeCoords !== null) {
        $location = "X: " . $homeCoords->getFloorX() . ", Y: " . $homeCoords->getFloorY() . ", Z: " . $homeCoords->getFloorZ();
    }
            $name = $faction->getName();
            
$msg = new Message();
$webhookUrl = Kohi::RAID_ANNOUNCE;
$webHook = new Webhook($webhookUrl);

$embed = new Embed();
$embed->setTitle("``🚨`` FACTION RAID ``🚨``");
$embed->setColor(0xFF0000);
$msg->setContent("@everyone");
$embed->setDescription("**Faction:** {$name} \n**Location:** {$location} \n**World:** HCF\n \n> - IP: play.deryxmc.xyz\n> - Port: 19132\n");
$embed->setFooter("Powered by DeryxMC MCPE"); 
$embed->setThumbnail(Kohi::IMAGE); 
$msg->addEmbed($embed);
$webHook->send($msg);

                if ($killerXuid !== null) {
                    $session = HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid);

                    if ($session !== null && $session->getFaction()) {
                        $fac = HCFLoader::getInstance()->getFactionManager()->getFaction($session->getFaction());

                        if ($fac !== null) {
                            $fac->setPoints($fac->getPoints() + 3);
                          //  $fac->announce(TextFormat::colorize('&cThe faction &l' . $faction->getName() . ' &r&cis now Rideable!'));
                        }
                    }
                }
            }

            # Regen time
            if (!$faction->isRaidable()) {
                $faction->setTimeRegeneration(10 * 60);
            } else {
                $regenTime = $faction->getTimeRegeneration();
                $value = $regenTime + (5 * 60);

                $faction->setTimeRegeneration($value < 10 * 60 ? $value : 35 * 60);
            }

            # Setup scoretag for team members
            foreach ($faction->getOnlineMembers() as $member)
                $member->setScoreTag(TextFormat::colorize('&6[&c' . $faction->getName() . ' &c' . $faction->getDtr() . '■&6]'));
        }

        if ($killer === null) {
            $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &edied';
            $webhook = $session->getName() . '[' . $session->getKills() . '] died';
        } else {
            if (!$itemInHand->isNull() && $itemInHand instanceof Tool) {
                $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &ewas slain by &c' . $killer . '&4[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . '] &cusing ' . $itemInHand->getName();
                $webhook = '`' . $session->getName() . '[' . $session->getKills() . '] was slain by ' . $killer . '[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']`';
            } else {
                $message = '&c' . $session->getName() . '&4[' . $session->getKills() . '] &ewas slain by &c' . $killer . '&4[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']';
                $webhook = '`' . $session->getName() . '[' . $session->getKills() . '] was slain by ' . $killer . '[' . HCFLoader::getInstance()->getSessionManager()->getSession($killerXuid)->getKills() . ']`';
            }
        }
        $webHook = new Webhook(Kohi::WEBHOOK_KILL);
        $msg = new Message();
        $msg->setContent($webhook);
        $webHook->send($msg);
        Server::getInstance()->broadcastMessage(TextFormat::colorize($message));
    }
}