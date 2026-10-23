<?php
declare(strict_types=1);

namespace hcf\utils;

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\player\Player;
use pocketmine\network\mcpe\convert\TypeConverter;

final class FakeBlock {
    public static function send(Player $player, Block $block, Vector3 $position, bool $immediate = false): void {
        $player->getNetworkSession()->sendDataPacket(self::createPacket($block, $position), $immediate);
    }

    public static function createPacket(Block $block, Vector3 $position): UpdateBlockPacket {
        return UpdateBlockPacket::create(
            BlockPosition::fromVector3($position),
            TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId($block->getStateId()),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        );
    }
}