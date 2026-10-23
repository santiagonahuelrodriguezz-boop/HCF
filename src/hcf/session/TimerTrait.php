<?php

namespace hcf\session;

trait TimerTrait {

	/** @var SessionCooldown[] */
	private array $cooldowns = [];
    
    /**
     * @return SessionCooldown[]
     */
    public function getCooldowns(): array
    {
        return $this->cooldowns;
    }
    
    /**
     * @param string $key
     * @return SessionCooldown|null
     */
    public function getCooldown(string $key): ?SessionCooldown
    {
        return $this->cooldowns[$key] ?? null;
    }
    
    public function hasCooldown(string $key): bool {
        return ($this->getCooldown($key) !== null);
    }

	/**
     * @param string $key
     * @param string $format
     * @param int $time
     * @param bool $paused
     * @param bool $visible
     */
    public function addCooldown(string $key, string $format, int $time, bool $paused = false, bool $visible = true): void
    {
        $this->cooldowns[$key] = new SessionCooldown($format, $time, $paused, $visible);
    }
    
    /**
     * @param string $key
     */
    public function removeCooldown(string $key): void
    {
        unset($this->cooldowns[$key]);
    }

	private function updateTimers() : void {
		foreach ($this->cooldowns as $key => $cooldown) {
			$cooldown->update();
		}
	}
}