<?php

declare(strict_types=1);

namespace hcf\session;

use hcf\HCFLoader;
use hcf\player\session\TraderPlayer;

class SessionManager
{
    
    private array $sessions = [];
    
    public function __construct()
    {
        # Register players
        foreach (HCFLoader::getInstance()->getProvider()->getPlayers() as $xuid => $data)
            $this->addSession((string) $xuid, $data, false);
    }
    
    public function getSessions(): array
    {
        return $this->sessions;
    }
    
    public function getSession(string $xuid): ?Session
    {
        return $this->sessions[$xuid] ?? null;
    }

    public function addSession(string $xuid, array $data, bool $firstTime = true): void
    {
        $this->sessions[$xuid] = new Session($xuid, $data, $firstTime);
    }
}