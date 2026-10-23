<?php

namespace venom\practice\server\entity;

use pocketmine\entity\effect\InstantEffect;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\player\Player;
use pocketmine\world\sound\PotionSplashSound;

class VenomPotionEntity extends SplashPotion{

	public const MAX = 1.0515;

	protected float $gravity = 0.06;
	protected float $drag = 0.0025;


	public function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->isCollided){
			$this->flagForDespawn();
		}
		return parent::entityBaseTick($tickDiff);
	}

	protected function onHit(ProjectileHitEvent $event) : void{
		$owner = $this->getOwningEntity();
		if(!$owner instanceof Player){
			$this->flagForDespawn();
			return;
		}

		$this->broadcastSound(new PotionSplashSound());

		if(count($this->getPotionEffects()) > 0){
			if($event instanceof ProjectileHitEntityEvent){
				$entityHit = $event->getEntityHit();
				if($entityHit instanceof Player){
					$entityHit->heal(new EntityRegainHealthEvent($entityHit, 1.45, EntityRegainHealthEvent::CAUSE_CUSTOM));
				}
			}
			foreach($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expand(1.85, 2.65, 1.85)) as $nearby){
				if($nearby instanceof Player){
					if($nearby->isAlive() and !$nearby->hasNoClientPredictions()){
						foreach($this->getPotionEffects() as $effect){
							if(!$effect->getType() instanceof InstantEffect){
								$newDuration = (int) round($effect->getDuration() * 0.75 * self::MAX);
								if($newDuration < 20){
									continue;
								}
								$effect->setDuration($newDuration);
								$nearby->getEffects()->add($effect);
							}else{
								$effect->getType()->applyEffect($nearby, $effect, self::MAX, $this);
							}
						}
					}
				}
			}
		}
	}
}