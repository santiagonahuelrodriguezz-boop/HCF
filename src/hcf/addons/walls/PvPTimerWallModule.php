<?php

namespace hcf\addons\walls;

use CortexPE\std\AABBUtils;
use CortexPE\std\Vector3Utils;
use hcf\HCFLoader;
use hcf\claim\Claim;
use hcf\claim\ClaimManager;
use hcf\session\SessionManager;
use hcf\utils\WallBarriersTrait;
use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\DyeColor;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\event\Listener;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

final class PvPTimerWallModule implements Listener {
	use WallBarriersTrait;
	
	private array $regions = [];
    private Block $barrierBlock;
    
    public function __construct() {
    	$this->barrierRadius = 3;
        $this->barrierBlock = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED());
        $ClaimManager = HCFLoader::getInstance()->getClaimManager();
        foreach($ClaimManager->getClaims() as $name => $claim) {
        $pos1 = $claim->getMinX() . ":0:" . $claim->getMinZ(); 
        $pos2 = $claim->getMaxX() . ":256:" . $claim->getMaxZ();

        $worldName = $claim->getWorld();

        	if ($claim->getType() === 'faction') {
        	   $this->regions[$worldName][] = AABBUtils::fromCoordinates(
                  Vector3Utils::fromString((string) $pos1),
                  Vector3Utils::fromString((string) $pos2)
               )->expand(0.0001, 0, 0.0001);
            }
        }
	}
	
	protected function isVisibleTo(Player $player): bool {
        return $player->getSession()->getCooldown('starting.timer') !== null;
    }
    
    protected function calculateNearestPointOutside(Player $p, Position $pos): Position {
        foreach($this->regions[$pos->world->getFolderName()] ?? [] as $region) {
            if(!$region->isVectorInside($p->getPosition())) continue;
            $vec = AABBUtils::getNearestStraightPathOutside($region, $pos);
            $pos->x = $vec->x;
            $pos->y = $vec->y;
            $pos->z = $vec->z;
        }
        return $pos;
    }

    #[Pure] protected function isInsideProhibitedArea(Player $p, Position $pos): bool {
        if(!isset($this->regions[$fName = $pos->world->getFolderName()])) return false;
        foreach($this->regions[$fName] as $region) {
            if($region->isVectorInside($pos)) return true;
        }
        return false;
    }

    protected function getBarrierBlock(): Block {
        return $this->barrierBlock;
    }
    
    public static function showBlock(Player $player, Block $block, Vector3 $pos, bool $immediate = false) {
		$player->getNetworkSession()->sendDataPacket(self::showBlockPacket($block, $pos), $immediate);
	}

	public static function showBlockPacket(Block $block, Vector3 $pos) {
		$pos = new BlockPosition($pos->x, $pos->y, $pos->z);
		$block = TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId($block->getStateId());
		return UpdateBlockPacket::create($pos, $block, UpdateBlockPacket::FLAG_NETWORK, UpdateBlockPacket::DATA_LAYER_NORMAL);
	}
}
