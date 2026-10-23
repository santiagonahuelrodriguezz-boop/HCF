<?php

namespace hcf\customitems\airdrops\task;

use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\world\particle\AngryVillagerParticle;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\tile\Chest as ChestTile;
use hcf\customitems\airdrops\data\AirdropData;

class AirdropAnimationTask extends Task {

    private Position $position;
    private float $currentY;
    private int $currentTick = 0;

    public function __construct(Position $position) {
        $this->position = $position;
        $this->currentY = $position->y;
        $this->position->world->setBlock($this->position, VanillaBlocks::FURNACE());
    }

    public function onRun(): void {
        $world = $this->position->getWorld();
       
        $blockBelow = $world->getBlock($this->position->down());
        if (!$blockBelow->isSolid()) {
            
            $world->setBlock($this->position, VanillaBlocks::AIR());
            $this->currentY -= 0.9;
            $this->position->y = (int) $this->currentY;
            $world->setBlock($this->position, VanillaBlocks::FURNACE());

            $radius = 0.8;
            for ($i = 0; $i < 360; $i += 360 / 8) {
                $angle = deg2rad($i);
                $xOffset = $radius * cos($angle);
                $zOffset = $radius * sin($angle);
                $particleLocation = $this->position->add($xOffset, 0, $zOffset);
                $world->addParticle($particleLocation, new AngryVillagerParticle());
            }

            $this->currentTick++;
        } else {
            
            $world->setBlock($this->position, VanillaBlocks::CHEST());
            $world->addParticle($this->position, new HugeExplodeParticle());
            $world->addSound($this->position, new ExplodeSound());
            
            $tile = $world->getTile($this->position);
            if ($tile instanceof ChestTile) {
                $contents = AirdropData::getRandomLoot(5); 
                foreach ($contents as $item) {
                    $tile->getInventory()->addItem($item);
                }
            }
            $this->getHandler()->cancel();
        }
    }
}
