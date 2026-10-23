<?php

declare(strict_types=1);

namespace hcf\module;

use hcf\HCFLoader;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\World;

final class AlwaysDay implements Listener {

    private HCFLoader $plugin;

    /** @var int */
    private int $time = World::TIME_DAY; // 1000 (día)

    public function __construct(HCFLoader $plugin){
        $this->plugin = $plugin;
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);

        // Aplicar al inicio a todos los mundos ya cargados
        foreach($plugin->getServer()->getWorldManager()->getWorlds() as $world){
            $this->setDay($world);
        }

        // Repetir cada 20 ticks para que nunca cambie
        $plugin->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void {
            foreach($this->plugin->getServer()->getWorldManager()->getWorlds() as $world){
                $this->setDay($world);
            }
        }), 20);
    }

    public function onWorldLoad(WorldLoadEvent $event) : void {
        $this->setDay($event->getWorld());
    }

    public function onPlayerJoin(PlayerJoinEvent $event) : void {
        $this->setDay($event->getPlayer()->getWorld());
    }

    private function setDay(World $world) : void {
        $world->setTime($this->time);
        $world->stopTime(); // Detener ciclo del tiempo
    }
}