<?php

namespace hcf\extras;

use hcf\HCFLoader;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\scheduler\Task;

class AntiSpam implements Listener {
    use SingletonTrait;

    private $lastChats = [];
    private $duration;

    public function __construct() {
        $this->duration = 5; // Duración en segundos
    }

    public function onChat(PlayerChatEvent $ev): void {
        $p = $ev->getPlayer();
        if(!$p->hasPermission("antispam.bypass")) {
            if(($timDiff = time() - ($this->lastChats[$p->getName()][0] ?? 0)) < $this->duration) {
                $p->sendMessage(TextFormat::colorize("&cPlease wait another " . $this->humanizeDuration($this->duration - $timDiff) . " seconds."));
                HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new class($p, $this->duration - $timDiff) extends \pocketmine\scheduler\Task {
                    private $player;
                    private $time;
                    public function __construct(Player $player, int $time) {
                        $this->player = $player;
                        $this->time = $time;
                    }
                    
                    public function onRun(): void {
                    }
                    
                }, $this->duration - $timDiff);
                $ev->cancel();
                return;
            }
        }
        unset($this->lastChats[$p->getName()]);
        $ev->setMessage(TextFormat::clean($ev->getMessage()));
        $this->lastChats[$p->getName()] = [time(), strtolower($ev->getMessage())];
    }

    public function onQuit(PlayerQuitEvent $ev): void {
        unset($this->lastChats[$ev->getPlayer()->getName()]);
    }

    private function humanizeDuration(int $seconds): string {
        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;
        return "$seconds seconds";
    }
}