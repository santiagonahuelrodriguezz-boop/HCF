<?php

declare(strict_types=1);

namespace hcf\claim;

use hcf\HCFLoader;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\world\Position;

class ClaimCreator extends Claim
{
    private int $minY, $maxY;
    private ?Position $first = null, $second = null;

    public function __construct(string $name, string $type)
    {
        parent::__construct($name, $type, -1, -1, -1, -1, '-1');
        $this->minY = -1;
        $this->maxY = -1;
    }

    public function getMinY(): int
    {
        return $this->minY;
    }

    public function getMaxY(): int
    {
        return $this->maxY;
    }

    public function getFirst(): ?Position
    {
        return $this->first;
    }

    public function getSecond(): ?Position
    {
        return $this->second;
    }

    public function setFirstPosition(int $x, int $z): void
    {
        $this->first = ['x' => $x, 'z' => $z];
    }

    public function setSecondPosition(int $x, int $z): void
    {
        $this->second = ['x' => $x, 'z' => $z];
    }

    public function calculate(Position $position, ?Player $player = null, bool $first = true): bool
    {
        if ($first) {
            if ($player !== null) $this->createCorner($player, $position, $first);
            $this->first = $position;
        } else {
            if ($player !== null) $this->createCorner($player, $position, $first);
            $this->second = $position;
        }

        if ($this->first !== null && $this->second !== null) {
            $this->minX = min($this->first->getFloorX(), $this->second->getFloorX());
            $this->maxX = max($this->first->getFloorX(), $this->second->getFloorX());

            $this->minY = min($this->first->getFloorY(), $this->second->getFloorY());
            $this->maxY = max($this->first->getFloorY(), $this->second->getFloorY());

            $this->minZ = min($this->first->getFloorZ(), $this->second->getFloorZ());
            $this->maxZ = max($this->first->getFloorZ(), $this->second->getFloorZ());

            // 🚫 Límite de tamaño máximo: 50x50
            $claimWidth = $this->maxX - $this->minX + 1;
            $claimLength = $this->maxZ - $this->minZ + 1;

            if ($claimWidth > 20000 || $claimLength > 20000) {
                if ($player !== null) {
                    $player->sendMessage("§cNo puedes crear un claim mayor a 200x200 bloques.");
                }
                return false;
            }

            if ($this->first->getWorld()->getFolderName() !== $this->second->getWorld()->getFolderName()) {
                return false;
            }

            $this->world = $this->first->getWorld()->getFolderName();
            return true;
        }
        return false;
    }

    public function calculateValue(): int
    {
        if ($this->first !== null && $this->second !== null) {
            [$minX, $maxX, $minZ, $maxZ] = [$this->getMinX(), $this->getMaxX(), $this->getMinZ(), $this->getMaxZ()];
            $minValue = (($maxX - $minX) + ($maxZ - $minZ)) / 4;
            $minValue *= 90;

            return (int) round(abs($minValue));
        }
        return 0;
    }

    public function calculateClaim(Position $first, Position $second): bool
    {
        $minX = min($first->getX(), $second->getX());
        $maxX = max($first->getX(), $second->getX());
        $minZ = min($first->getZ(), $second->getZ());
        $maxZ = max($first->getZ(), $second->getZ());

        for ($x = $minX; $x <= $maxX; $x++) {
            for ($z = $minZ; $z <= $maxZ; $z++) {
                $position = new Position($x, 0, $z, $first->getWorld());
                if (HCFLoader::getInstance()->getClaimManager()->insideClaim($position) !== null) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isValid(): bool
    {
        return $this->getWorld() !== '-1' &&
               $this->getMinX() !== -1 &&
               $this->getMaxX() !== -1 &&
               $this->getMinY() !== -1 &&
               $this->getMaxY() !== -1 &&
               $this->getMinZ() !== -1 &&
               $this->getMaxZ() !== -1;
    }

    public function deleteCorners(Player $player): void
    {
       /* $first = $this->getFirst();
        if ($first !== null) {
            for ($y = $first->getFloorY(); $y <= 127; $y++) {
                $player->getNetworkSession()->sendDataPacket($this->sendFakeBlock(
                    new Position($first->getFloorX(), $y, $first->getFloorZ(), $first->getWorld()), VanillaBlocks::AIR()
                ));
            }
        }

        $second = $this->getSecond();
        if ($second !== null) {
            for ($y = $second->getFloorY(); $y <= 127; $y++) {
                $player->getNetworkSession()->sendDataPacket($this->sendFakeBlock(
                    new Position($second->getFloorX(), $y, $second->getFloorZ(), $second->getWorld()), VanillaBlocks::AIR()
                ));
            }
        }*/
    }

    private function createCorner(Player $player, Position $position, bool $first = true): void
    {
       /* if ($first) {
            for ($y = $position->getFloorY(); $y <= 127; $y++) {
                $player->getNetworkSession()->sendDataPacket($this->sendFakeBlock(
                    new Position($position->getFloorX(), $y, $position->getFloorZ(), $position->getWorld()),
                    $y % 3 === 0 ? VanillaBlocks::EMERALD() : VanillaBlocks::GLASS()
                ));
            }
        } else {
            $second = $this->getSecond();
            if ($second !== null && !$second->equals($position)) {
                for ($y = $second->getFloorY(); $y <= 127; $y++) {
                    $player->getNetworkSession()->sendDataPacket($this->sendFakeBlock(
                        new Position($second->getFloorX(), $y, $second->getFloorZ(), $second->getWorld()), VanillaBlocks::AIR()
                    ));
                }
            }

            for ($y = $position->getFloorY(); $y <= 127; $y++) {
                $player->getNetworkSession()->sendDataPacket($this->sendFakeBlock(
                    new Position($position->getFloorX(), $y, $position->getFloorZ(), $position->getWorld()),
                    $y % 3 === 0 ? VanillaBlocks::EMERALD() : VanillaBlocks::GLASS()
                ));
            }
        }*/
    }

    private function sendFakeBlock(Position $position, Block $block): UpdateBlockPacket
    {
        $pos = BlockPosition::fromVector3($position->asVector3());
        $id = TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId($block->getStateId());

        return UpdateBlockPacket::create($pos, $id, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
    }
}