<?php

declare(strict_types=1);

namespace hcf\timer\types;

use hcf\HCFLoader;
use pocketmine\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

class TimerStarKill {

    private int $time;
    private bool $active;
    private string $format;
    private bool $protectDTR = false; 

    public function __construct(int $time = 3600, string $format = '§l§6|§r §eStarKill§r§7: &r&f', bool $active = false) {
        $this->time = $time;
        $this->format = $format;
        $this->active = $active;
    }

    public function getTime(): int {
        return $this->time;
    }

    public function getFormat(): string {
        return $this->format;
    }

    public function isActive(): bool {
        return $this->active;
    }

    public function isProtectingDTR(): bool {
        return $this->protectDTR;
    }

    public function setTime(int $time): void {
        $this->time = $time;
    }

    public function setActive(bool $active): void {
        $this->active = $active;
        $this->protectDTR = $active;
    }

    public function update(): void {
        if (!$this->active) return;

        $this->time--;

        foreach (HCFLoader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $this->applyStrength($player);
        }

        if ($this->time <= 0) {
            $this->active = false;
            $this->protectDTR = false; 
            $this->time = 3600;
            HCFLoader::getInstance()->getServer()->broadcastMessage('§cStarKill Event has ended!');
            foreach (HCFLoader::getInstance()->getServer()->getOnlinePlayers() as $player) {
                $player->getEffects()->remove(VanillaEffects::STRENGTH());
            }
        }
    }

    private function applyStrength(Player $player): void {
        $effect = new EffectInstance(VanillaEffects::STRENGTH(), 999999, 1, false);
        $player->getEffects()->add($effect);
    }
}