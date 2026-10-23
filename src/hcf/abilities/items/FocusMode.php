<?php

namespace hcf\abilities\items;

use hcf\player\Player;
use hcf\HCFLoader;
use hcf\utils\time\Timer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\scheduler\ClosureTask;

class FocusMode implements Listener
{
    private array $focus = [];

    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $damager = $event->getDamager();
        if ($damager instanceof Player) {
            $item = $damager->getInventory()->getItemInHand();
            if ($item->getNamedTag()->getTag("Abilities") !== null) {
                if ($item->getNamedTag()->getString("Abilities") === "FocusMode") {
                    $event->cancel();
                    if ($damager->getSession()->getCooldown('ability.FocusMode') === null) {
                        if ($damager->getSession()->getCooldown('ability.global') === null) {
                            if ($damager->getSession()->getCooldown('starting.timer') !== null || $damager->getSession()->getCooldown('pvp.timer') !== null) {
                                return;
                            }

                            if ($damager->getCurrentClaim() === 'Spawn') {
                                return;
                            }
                            $damager->sendMessage("§6You have activated §gFocus Mode");
                            $damager->sendMessage(TextFormat::colorize("&cCooldown active of &l&Focus Mode &r&cby 2 minute"));
                            $damager->getSession()->addCooldown('ability.FocusMode', '§cFocus Mode&r&7: &c', 120);
                            $damager->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);
                            $item = $damager->getInventory()->getItemInHand();
                            $item->pop();
                            $damager->getInventory()->setItemInHand($item);
                            $damager->sendMessage("§cHas activated §l§6Focus Mode§r§c, will do 40% more damage for 7 seconds");
                            $damagerName = $damager->getName();
                            $this->focus[$damagerName] = true;
                            HCFLoader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($damagerName): void {
                                $this->focus[$damagerName] = false;
                            }), 20 * 7);
                        } else {
                            $damager->sendMessage("§c§6you have cooldown of §dPartner Item §f" . Timer::format($damager->getSession()->getCooldown("ability.global")->getTime()));
                        }
                    } else {
                        $damager->sendMessage("§cNo puedes usar el §gFocusMode§c por §l" . Timer::format($damager->getSession()->getCooldown("ability.FocusMode")->getTime()));
                    }
                }
            }
        }
    }

    public function onEntity(EntityDamageEvent $event): void
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                $damagerName = $damager->getName();
                if (isset($this->focus[$damagerName]) && $this->focus[$damagerName] === true) {
                    $event->setBaseDamage($event->getBaseDamage() + 1.2);
                }
            }
        }
    }
}
