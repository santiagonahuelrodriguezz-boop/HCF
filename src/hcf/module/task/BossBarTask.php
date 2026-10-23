<?php

declare(strict_types=1);

namespace hcf\module\task;

use hcf\player\Player;
use pocketmine\network\mcpe\protocol\types\BossBarColor;
use pocketmine\scheduler\Task;
use BossBarAPI\BossBar;
use hcf\HCFLoader;

class BossBarTask extends Task {

    private HCFLoader $plugin;
    private array $bossBars = []; 

    public function __construct(HCFLoader $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $this->sendBossBar($player);
        }
    }

    private function sendBossBar(Player $player): void {
        if (isset($this->bossBars[$player->getName()])) {
            $bossBar = $this->bossBars[$player->getName()];
            $bossBar->hideFrom([$player]);
        } else {
            $bossBar = new BossBar();
            $this->bossBars[$player->getName()] = $bossBar;
        }

        $factionName = $player->getSession()->getFaction();
        $faction = $factionName !== null ? $this->plugin->getFactionManager()->getFaction($factionName) : null;

        if ($faction !== null && ($rally = $faction->getRally()) !== null) {
            $pos = $player->getPosition();
            $x = (int)$pos->getX();
            $y = (int)$pos->getY();
            $z = (int)$pos->getZ();

            $bossBar->setTitle("§r§eRally§f: §a" . $rally[0] . " §cCoords§f: §f" . $x . ", " . $y . ", " . $z);
            $bossBar->setColor(BossBarColor::RED);
            $bossBar->setPercentage(1.0);
            $bossBar->showTo([$player]);
        } else {
            $bossBar->setTitle("§r§cplay.demonhcf.fun");
            $bossBar->setColor(BossBarColor::RED);
            $bossBar->setPercentage(1.0);
            $bossBar->showTo([$player]);
        }
    }
}