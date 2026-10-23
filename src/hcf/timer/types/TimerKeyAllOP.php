<?php

declare(strict_types=1);

namespace hcf\timer\types;

use hcf\HCFLoader;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

/**
 * Class TimerKeyAll
 * @package hcf\timer
 */
class TimerKeyAllOP
{

    /**
     * TimerKeyAll construct.
     * @param int $time
     * @param string $format
     * @param bool $active
     */
    public function __construct(
        private int $time = 60 * 60,
        private string $format = '§f| &3KeyAllOP&r&7: &r&7',
        private bool $active = false
    ) {}

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param int $value
     */
    public function setTime(int $value): void
    {
        $this->time = $value;
    }

    /**
     * @param bool $value
     */
    public function setActive(bool $value): void
    {
        $this->active = $value;
    }

    public function update(): void
    {
        if ($this->active) {
            if ($this->time === 1) {
                foreach (HCFLoader::getInstance()->getConfig()->get("KeyallOP") as $key => $amount) {
                    $key_name = (string) $key;
                    $key_amount = (int) $amount;
                    Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "key giveall $key_name $key_amount");
                }
                Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), TextFormat::colorize("broadcast &3KeyAllOP &fEntregado correctamente"));
            }
            $this->time--;

            if ($this->time <= 0) {
                $this->active = false;
                $this->time = 60 * 60;
            }
        }
    }
}