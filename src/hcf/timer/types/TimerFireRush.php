<?php

declare(strict_types=1);

namespace hcf\timer\types;

use hcf\HCFLoader;
use pocketmine\player\Player;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

/**
 * Class TimerFireRush
 * @package hcf\timer\types
 */
class TimerFireRush
{
    private int $time;
    private bool $active;
    private string $format;

    public function __construct(int $time = 60 * 60, string $format = '§l§4|§r §cFireRush§r§7: &r&f', bool $active = false)
    {
        $this->time = $time;
        $this->format = $format;
        $this->active = $active;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setTime(int $time): void
    {
        $this->time = $time;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function update(): void
    {
        if (!$this->active) return;

        $this->time--;

        foreach (HCFLoader::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $this->applyStrength($player);
        }

        if ($this->time <= 0) {
            $this->active = false;
            $this->time = 60 * 60;
            HCFLoader::getInstance()->getServer()->broadcastMessage('§cFireRush has ended!');
        }
    }

    private function applyStrength(Player $player): void
    {
 
        $effect = new EffectInstance(VanillaEffects::STRENGTH(), 999999, 1, false);
        $player->getEffects()->add($effect);
    }
}