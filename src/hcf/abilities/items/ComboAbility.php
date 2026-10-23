<?php

namespace hcf\abilities\items;

use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\scheduler\ClosureTask;

class ComboAbility implements Listener
{
    private array $hitCount = [];
    private array $activePlayers = [];
    private HCFLoader $plugin;

    public function __construct(HCFLoader $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if ($player instanceof Player) {
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "Combo") {
                    $event->cancel();
                    if ($player->getSession()->getCooldown('ability.Combo') === null) {
                        if ($player->getSession()->getCooldown('ability.global') === null) {
                            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($player->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            
                            $player->sendMessage("§6You have activated §cComboAbility");
                            $player->getSession()->addCooldown('ability.Combo', '§cComboAbility&r&7: &c', 60);
                            $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                            $item = $event->getItem();
                            $item->pop();
                            $player->getInventory()->setItemInHand($item);
                            $this->activePlayers[$player->getName()] = true;
                            $this->hitCount[$player->getName()] = 0;

                            $this->plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player): void {
                                $this->applyComboEffect($player);
                            }), 20 * 12);

                        } else {
                            $player->sendMessage("§6you have cooldown of §dPartner Item §f" . Timer::format($player->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $player->sendMessage("§cYou haven't ComboAbility in the last " . Timer::format($player->getSession()->getCooldown("ability.Combo")->getTime()));
                    }
                }
            }
        }
    }
    
    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        if ($damager instanceof Player && isset($this->activePlayers[$damager->getName()])) {
            $this->hitCount[$damager->getName()]++;
        }
    }

    private function applyComboEffect(Player $player): void
    {
        if (!isset($this->hitCount[$player->getName()])) {
            return;
        }

        $hits = $this->hitCount[$player->getName()];
        unset($this->hitCount[$player->getName()]);
        unset($this->activePlayers[$player->getName()]);

        if ($hits > 0) {
            $effect = new EffectInstance(VanillaEffects::STRENGTH(), 20 * $hits, 1); // Fuerza 2 durante hits segundos
            $player->getEffects()->add($effect);
            $player->sendMessage("§6You have received Strength 2 for §c{$hits} seconds§6 due to your Combo Ability!");
        } else {
            $player->sendMessage("§cNo hits registered during Combo Ability period.");
        }
    }
}
