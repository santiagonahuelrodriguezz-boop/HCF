<?php
declare(strict_types=1);

namespace hcf\Mrd;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\FakeBlock;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Block;
use pocketmine\block\utils\DyeColor;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\world\Position;

class WallListener implements Listener {

    private const BARRIER_RADIUS = 5;
    private const BUFFER_DISTANCE = 1;

    private Block $barrierBlock;
    private AxisAlignedBB $boundingBox;
    private string $limitWorld;
    private array $shownBlocks = [];

    public function __construct() {
        $this->barrierBlock = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED());
        $this->loadConfiguration();
    }

    private function loadConfiguration(): void {
        $config = HCFLoader::getInstance()->getConfig();
        $this->limitWorld = $config->get('limit_world', 'world');
        
        $pos1 = $this->parseCoordinates($config->get('limit_pos1', '-1200,0,-1200'));
        $pos2 = $this->parseCoordinates($config->get('limit_pos2', '1200,256,1200'));

        $this->boundingBox = new AxisAlignedBB(
            min($pos1->x, $pos2->x), 
            min($pos1->y, $pos2->y), 
            min($pos1->z, $pos2->z),
            max($pos1->x, $pos2->x), 
            max($pos1->y, $pos2->y), 
            max($pos1->z, $pos2->z)
        );
    }

    private function parseCoordinates(string $coordsStr): Position {
        $parts = explode(',', $coordsStr);
        return new Position(
            (float)$parts[0],
            (float)$parts[1],
            (float)$parts[2],
            HCFLoader::getInstance()->getServer()->getWorldManager()->getWorldByName($this->limitWorld)
        );
    }

    public function onPlayerMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        
        if ($player->getWorld()->getFolderName() !== $this->limitWorld) {
            $this->clearShownBlocks($player);
            return;
        }

        $to = $event->getTo();
        $isNearBorder = $this->isNearBorder($to);

        if ($isNearBorder) {
            $this->showBorderBlocks($player, $to);
        } else {
            $this->clearShownBlocks($player);
        }

        if ($this->isOutsideBorder($to)) {
            $event->cancel();
            $this->pushPlayerInside($player);
        }
    }

    private function isNearBorder(Position $position): bool {
        $minX = $this->boundingBox->minX;
        $maxX = $this->boundingBox->maxX;
        $minZ = $this->boundingBox->minZ;
        $maxZ = $this->boundingBox->maxZ;

        return $position->x <= ($minX + self::BARRIER_RADIUS) || 
               $position->x >= ($maxX - self::BARRIER_RADIUS) ||
               $position->z <= ($minZ + self::BARRIER_RADIUS) || 
               $position->z >= ($maxZ - self::BARRIER_RADIUS);
    }

    private function isOutsideBorder(Position $position): bool {
        return !$this->boundingBox->isVectorInside($position);
    }

    private function showBorderBlocks(Player $player, Position $playerPos): void {
        $this->clearShownBlocks($player);
        
        $world = $player->getWorld();
        $minX = $this->boundingBox->minX;
        $maxX = $this->boundingBox->maxX;
        $minZ = $this->boundingBox->minZ;
        $maxZ = $this->boundingBox->maxZ;

        for ($y = 0; $y <= $world->getMaxY(); $y++) {
            if ($playerPos->x <= ($minX + self::BARRIER_RADIUS)) {
                $this->showVerticalWall($player, $minX, $playerPos->z, $y, true);
            }
            
            if ($playerPos->x >= ($maxX - self::BARRIER_RADIUS)) {
                $this->showVerticalWall($player, $maxX, $playerPos->z, $y, true);
            }
            
            if ($playerPos->z <= ($minZ + self::BARRIER_RADIUS)) {
                $this->showVerticalWall($player, $playerPos->x, $minZ, $y, false);
            }
            
            if ($playerPos->z >= ($maxZ - self::BARRIER_RADIUS)) {
                $this->showVerticalWall($player, $playerPos->x, $maxZ, $y, false);
            }
        }
    }

    private function showVerticalWall(Player $player, float $x, float $z, int $y, bool $isXWall): void {
        $world = $player->getWorld();
        $pos = new Position($x, $y, $z, $world);
        
        if ($world->getBlock($pos)->isTransparent()) {
            FakeBlock::send($player, $this->barrierBlock, $pos);
            $this->shownBlocks[$player->getId()][] = $pos;
        }
    }

    private function clearShownBlocks(Player $player): void {
        if (!isset($this->shownBlocks[$player->getId()])) return;
        
        $world = $player->getWorld();
        foreach ($this->shownBlocks[$player->getId()] as $pos) {
            $realBlock = $world->getBlock($pos);
            FakeBlock::send($player, $realBlock, $pos);
        }
        
        unset($this->shownBlocks[$player->getId()]);
    }

    private function pushPlayerInside(Player $player): void {
        $position = $player->getPosition();
        $newPos = $position->asVector3();
        
        if ($position->x < $this->boundingBox->minX) {
            $newPos->x = $this->boundingBox->minX + self::BUFFER_DISTANCE;
        } elseif ($position->x > $this->boundingBox->maxX) {
            $newPos->x = $this->boundingBox->maxX - self::BUFFER_DISTANCE;
        }
        
        if ($position->z < $this->boundingBox->minZ) {
            $newPos->z = $this->boundingBox->minZ + self::BUFFER_DISTANCE;
        } elseif ($position->z > $this->boundingBox->maxZ) {
            $newPos->z = $this->boundingBox->maxZ - self::BUFFER_DISTANCE;
        }
        
        $player->teleport(Position::fromObject($newPos, $player->getWorld()));
    }
}