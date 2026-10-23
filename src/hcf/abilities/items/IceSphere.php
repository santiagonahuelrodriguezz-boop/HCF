<?php

namespace hcf\abilities\items;

use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;

class IceSphere implements Listener
{
    /** @var array<string, array<string, Block>> */
    private array $activeSpheres = [];

    public function generateSphere(Player $player): void
    {
        $radius = 5;
        $duration = 10;
        $pos = $player->getPosition();
        $world = $player->getWorld();
        $key = spl_object_hash($player);

        if (!isset($this->activeSpheres[$key])) {
            $this->activeSpheres[$key] = [];
        }

        $player->sendMessage(TextFormat::colorize("§aSe ha creado una esfera a tu alredor que durará §f{$duration}s§a!"));

        for ($x = -$radius; $x <= $radius; $x++) {
            for ($y = -$radius; $y <= $radius; $y++) {
                for ($z = -$radius; $z <= $radius; $z++) {
                    $distance = sqrt($x ** 2 + $y ** 2 + $z ** 2);
                    if ($distance <= $radius && $distance >= $radius - 1) {
                        $blockPos = $pos->add($x, $y, $z);
                        $block = $world->getBlock($blockPos);
                        $hash = $this->posHash($blockPos);

                        if ($this->isBlockProtected($hash)) continue;

                        $this->activeSpheres[$key][$hash] = clone $block;

                        if ($block->getTypeId() !== VanillaBlocks::PACKED_ICE()->getTypeId()) {
                            $world->setBlock($blockPos, VanillaBlocks::PACKED_ICE());
                        }
                    }
                }
            }
        }

        HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($world, $key): void {
            if (!isset($this->activeSpheres[$key])) return;

            foreach ($this->activeSpheres[$key] as $hash => $block) {
                $pos = $block->getPosition();
                if ($world->getBlock($pos)->getTypeId() === VanillaBlocks::PACKED_ICE()->getTypeId()) {
                    $world->setBlock($pos, $block);
                }
            }

            unset($this->activeSpheres[$key]);
        }), 20 * $duration);
    }

    private function isBlockProtected(string $hash): bool
    {
        foreach ($this->activeSpheres as $sphereBlocks) {
            if (isset($sphereBlocks[$hash])) return true;
        }
        return false;
    }

    private function posHash(Vector3 $pos): string
    {
        return $pos->getFloorX() . ':' . $pos->getFloorY() . ':' . $pos->getFloorZ();
    }

    public function onBlockBreak(BlockBreakEvent $event): void
    {
        $block = $event->getBlock();
        $pos = $block->getPosition()->asVector3();
        $hash = $this->posHash($pos);

              #ODA ES GEY AQUI->
        foreach ($this->activeSpheres as $blocks) {
            if (isset($blocks[$hash])) {
                $event->cancel();
                return;
            }
        }

            #Vdam es mi Amigo AQUI->
        if ($block->getTypeId() === VanillaBlocks::PACKED_ICE()->getTypeId()) {
            $event->cancel();
        }
    }
}