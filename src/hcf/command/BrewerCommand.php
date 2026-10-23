<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;
use hcf\utils\time\Timer;
use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use pocketmine\item\PotionType;

class BrewerCommand extends Command
{
    public function __construct()
    {
        parent::__construct('brewer', 'Command for brewer');
        $this->setAliases(['pots']);
        $this->setPermission('player.use');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player)
            return;

        if (!$this->testPermission($sender))
            return;

        if ($sender->getSession()->getFaction() === null) {
            $sender->sendMessage("§cYou can't use this command");
            return;
        }

        if ($sender->getSession()->getFaction() !== $sender->getCurrentClaim()) {
            $sender->sendMessage("§cYou can't use this command");
            return;
        }

        // Lista de cooldowns por permiso
        $cooldowns = [
            "leviatan.cooldown" => 11 * 3600,
            "blecebu.cooldown"  => 9 * 3600,
            "amon.cooldown"     => 7 * 3600,
            "demon.cooldown"    => 5 * 3600,
            "satanic.cooldown"  => 1 * 3600,
            "brewer.cooldown"   => 24 * 3600,
        ];

        foreach ($cooldowns as $perm => $time) {

            if ($sender->hasPermission($perm)) {

                $cd = $sender->getSession()->getCooldown($perm);

                if ($cd !== null) {
                    $left = $cd->getTime();
                    $sender->sendMessage("§eCooldown §7Time: §f" . Timer::fancy($left));
                    return;
                }

                $sender->getSession()->addCooldown($perm, "", $time, false, false);
                break;
            }
        }

        $world = $sender->getWorld();
        $position = $sender->getPosition()->floor();

        $tile1 = $this->createTileChest($position, $world);
        $tile2 = $this->createTileChest($position->add(1, 0, 0), $world);
        $tile3 = $this->createTileChest($position->add(0, 1, 0), $world);
        $tile4 = $this->createTileChest($position->add(1, 1, 0), $world);
        $tile5 = $this->createTileChest($position->add(0, 2, 0), $world);
        $tile6 = $this->createTileChest($position->add(1, 2, 0), $world);

        $splash = VanillaItems::SPLASH_POTION()->setType(PotionType::STRONG_HEALING());

        for ($i = 0; $i <= 26; $i++) {
            $tile1->getInventory()->addItem($splash);
            $tile2->getInventory()->addItem($splash);
            $tile3->getInventory()->addItem($splash);
            $tile4->getInventory()->addItem(VanillaItems::POTION()->setType(PotionType::STRONG_SWIFTNESS()));
            $tile5->getInventory()->addItem(VanillaItems::POTION()->setType(PotionType::LONG_FIRE_RESISTANCE()));
            $tile6->getInventory()->addItem(VanillaItems::POTION()->setType(PotionType::LONG_INVISIBILITY()));
        }
    }

    private function createTileChest(Vector3 $position, World $world): Chest
    {
        if (!$world->isChunkLoaded($position->getFloorX(), $position->getFloorZ())) {
            $world->loadChunk($position->getFloorX(), $position->getFloorZ());
        }

        if ($world->getTileAt($position->getFloorX(), $position->getFloorY(), $position->getFloorZ()) !== null) {
            $world->setBlock($position, VanillaBlocks::AIR());
        }

        $chest = new Chest($world, $position);
        $world->addTile($chest);
        $world->setBlock($position, VanillaBlocks::CHEST());
        return $chest;
    }
}