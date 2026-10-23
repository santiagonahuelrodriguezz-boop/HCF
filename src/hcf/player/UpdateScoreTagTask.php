<?php

namespace hcf\player;

use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use hcf\HCFLoader;

class UpdateScoreTagTask extends Task {
    private Player $player;

    public function __construct(Player $player) {
        $this->player = $player;
    }
    
    public function onRun(): void {
        if (!$this->player->isOnline()) {
            return; 
        }

        $session = $this->player->getSession();
        $pvpTimer = $session->getCooldown('pvp.timer');
        $archerMark = $session->getCooldown('archer.mark');

        $factionT = $session->getFaction();
        $fTopPosition = "";

        if ($factionT !== null) {
            $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($factionT);
            if ($faction !== null) {
                $fTopPos = HCFLoader::getInstance()->getFactionManager()->getFactionPosition($faction->getName());

                if ($fTopPos === 1) {
                    $fTopPosition = "§g#1";
                } elseif ($fTopPos === 2) {
                    $fTopPosition = "§g#2";
                } elseif ($fTopPos === 3) {
                    $fTopPosition = "§g#3";
                } elseif ($fTopPos === 4) {
                    $fTopPosition = "§g#4";
                } elseif ($fTopPos === 5) {
                    $fTopPosition = "§g#5";
                } elseif ($fTopPos === 6) {
                    $fTopPosition = "§g#6";
                } elseif ($fTopPos === 7) {
                    $fTopPosition = "§g#7";
                } elseif ($fTopPos === 8) {
                    $fTopPosition = "§g#8";
                } elseif ($fTopPos === 9) {
                    $fTopPosition = "§g#9";
                } elseif ($fTopPos === 10) {
                    $fTopPosition = "§g#10";
                }
            }
        }

        $nameTagLines = [];

        if ($archerMark !== null) {
            $nameTagLines[] = "§7[§uArcher Mark§7]";

            foreach ($this->player->getViewers() as $p) {
                $data = clone $this->player->getNetworkProperties();
                if ($this->player->getEffects()->has(VanillaEffects::INVISIBILITY())) {
                    $data->setGenericFlag(EntityMetadataFlags::INVISIBLE, false, true);
                }
            }
        } else {
            if ($this->player->getEffects()->has(VanillaEffects::INVISIBILITY())) {
                foreach ($this->player->getViewers() as $p) {
                    $data = clone $this->player->getNetworkProperties();
                    $data->setGenericFlag(EntityMetadataFlags::INVISIBLE, true, true);
                }
            }
        }

        if ($pvpTimer !== null) {
            $nameTagLines[] = "§7[§3PvP Timer§7]";
        }

        $nameTagLines[] = "§3" . $this->player->getName() . " §7| {$fTopPosition}";

        $this->player->setNameTag(implode("\n", $nameTagLines));

        if ($factionT !== null) {
            $factionDetails = HCFLoader::getInstance()->getFactionManager()->getFaction($factionT);
            $dtrColor = ($factionDetails->getDtr() === (count($factionDetails->getMembers()) + 0.1)) ? '§3' : '§7';
            $this->player->setScoreTag(
                '§e[§3' . $factionDetails->getName() . ' §7|§3 ' . $dtrColor . $factionDetails->getDtr() . '■§e]'
            );
        } else {
            $this->player->setScoreTag('§e[§4NO FACTION§e]');
        }
    }
}
