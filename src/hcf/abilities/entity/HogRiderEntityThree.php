<?php

namespace hcf\abilities\entity;

use hcf\HCFLoader;
use hcf\abilities\items\HogRider;

use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\Server;
use JetBrains\PhpStorm\Pure;

class HogRiderEntityThree extends Living
{
    private $owner = null;
    private int $count_down = 50;
    private int $time = 0;
    private Position $pos;
    
    public float $height = 4.0;
    public float $width = 2.0;
    
    public function __construct(Location $location, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $nbt);
        $this->setMaxHealth(100);
        $this->setNameTagVisible();
        $this->setNameTagAlwaysVisible();
        $this->setCanSaveWithChunk(true);
        $this->setNameTag("§6Strenght");
        
        $this->setScale(20);
    }
    
    public static function getNetworkTypeId() : string
    {
        return EntityIds::PIG;
    }

    public function getName() : string{
        return "Hog";
    }

    protected function initEntity(CompoundTag $nbt) : void{
        parent::initEntity($nbt);
    }

    public function onUpdate(int $currentTick): bool
    {
        if($this->time === 0 || time() - $this->time >= 1) {
            $this->time = time();
            if ($this->owner == null) {
                $this->close();
                return parent::onUpdate($currentTick);
            }
            $owner = HCFLoader::getInstance()->getServer()->getPlayerExact($this->owner);
            foreach ($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expandedCopy(10, 10, 10)) as $p) {
                if ($p instanceof Player) {
                    if ($p->getName() == $this->owner) {
                        if(HogRider::isAllow($p)) {
                            if ($this->count_down <= (50) && $this->count_down >= (35)) {
                                $owner->getEffects()->add(new EffectInstance(VanillaEffects::STRENGTH(), 20 * 2, 1));
                            }
                        }
                    }
                }
            }
            if ($this->count_down > 0) {
                $this->count_down--;
            }
            if ($this->count_down <= 0) {
                $this->close();
            }
        }
        return parent::onUpdate($currentTick);
    
    }

    public function setOwner(Player $player): void 
    {
        $this->owner = $player->getName();
    }

    public function setPos(Position $pos): void
    {
        $this->pos = $pos;
    }
    
    public function getOwner()
    {
        return $this->owner;
    }
    
    protected function getInitialSizeInfo(): EntitySizeInfo
{
    return new EntitySizeInfo(1.0, 1.0); // Tamaño predeterminado (1.0x1.0)
}


}