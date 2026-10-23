<?php

declare(strict_types=1);

namespace hcf\handler\kit\classes\presets;

use hcf\handler\kit\classes\HCFClass;
use hcf\HCFLoader;
use hcf\player\Player;
use hcf\abilities\items\IceSphere;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\TextFormat;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;

class Yeti extends HCFClass implements Listener
{
    public const YETI = 5;

    public function __construct()
    {
        parent::__construct(self::YETI);
        HCFLoader::getInstance()->getServer()->getPluginManager()->registerEvents($this, HCFLoader::getInstance());
    }

    public function getArmorItems(): array
    {
        return [
            VanillaItems::LEATHER_CAP(),
            VanillaItems::DIAMOND_CHESTPLATE(),
            VanillaItems::DIAMOND_LEGGINGS(),
            VanillaItems::LEATHER_BOOTS()
        ];
    }

    public function getEffects(): array
    {
        return [
            new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 15, 1),
            new EffectInstance(VanillaEffects::STRENGTH(), 20 * 15, 0),
            new EffectInstance(VanillaEffects::SPEED(), 20 * 15, 2)
        ];
    }

    public function handleItemUse(PlayerItemUseEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if (!$player instanceof Player || $player->getClass()?->getTypeId() !== self::YETI) {
            return;
        }

        if ($item->equals(VanillaBlocks::PACKED_ICE()->asItem(), false, false)) {
            $event->cancel();
            if ($player->getSession()->getCooldown('ability.IceSphere') !== null) {
                $player->sendMessage(TextFormat::colorize("§cNo puedes usar la §bYeti§c por §l" . \hcf\utils\time\Timer::format($player->getSession()->getCooldown("ability.IceSphere")->getTime())));
                return;
            }

            if ($player->getSession()->getCooldown('ability.global') !== null) {
                $player->sendMessage(TextFormat::colorize("§cTienes un cooldown activo de &l&5Partner Item &r§c."));
                return;
            }

            if ($player->getCurrentClaim() === 'Spawn') {
                $player->sendMessage(TextFormat::colorize("&cNo puedes usar habilidades en la zona de Spawn."));
                return;
            }

            if ($player->getSession()->getCooldown('starting.timer') !== null || $player->getSession()->getCooldown('pvp.timer') !== null) {
                $player->sendMessage(TextFormat::colorize("&cNo puedes usar habilidades mientras tengas un timer de protección."));
                return;
            }

            $player->sendMessage(TextFormat::colorize("§6Has activado tu habilidad §bIce Sphere"));
            $player->getSession()->addCooldown('ability.IceSphere', '§bYeti &r&7: &c', 50);
            $player->getSession()->addCooldown('ability.global', '&5Partner Item&r&7: &c', 10);

            (new IceSphere())->generateSphere($player);

            $item->pop();
            $player->getInventory()->setItemInHand($item);
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void
    {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item->equals(VanillaBlocks::PACKED_ICE()->asItem(), false, false)) {
            $event->cancel();
            //$player->sendMessage(TextFormat::colorize("§cNo puedes colocar hielo comprimido."));
        }
    }

    public function handleDamage(EntityDamageEvent $event): void {}
    public function handleDamageByChildEntity(EntityDamageByChildEntityEvent $event): void {}
}