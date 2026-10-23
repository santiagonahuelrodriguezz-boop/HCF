<?php

declare(strict_types=1);

namespace hcf\faction\command\subcommand;

use hcf\faction\command\FactionSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\world\Position;

class StuckSubCommand implements FactionSubCommand
{
    private function teleport(Player $player, int $attempt = 0): void
    {
        if ($attempt >= 15) return;

        $world = $player->getWorld();
        $px = $player->getPosition()->getFloorX();
        $pz = $player->getPosition()->getFloorZ();

        $x = mt_rand($px - 100, $px + 100);
        $z = mt_rand($pz - 100, $pz + 100);
        $y = $world->getHighestBlockAt($x, $z);

        $pos = new Position($x, $y, $z, $world);

        if (HCFLoader::getInstance()->getClaimManager()->insideClaim($pos) !== null) {
            $this->teleport($player, $attempt + 1);
            return;
        }

        $player->teleport($pos->add(0, 1, 0));
    }

    public function execute(CommandSender $sender, array $args): void
    {
        if (!$sender instanceof Player) return;

        // ==============================
        // DETECTAR Wilderness / Warzone
        // ==============================
        $dist = $sender->getPosition()->distance($sender->getWorld()->getSafeSpawn());
        $zoneType = ($dist > 400 ? '§7Wilderness' : '§gWarzone');

        if (in_array($zoneType, ['§7Wilderness', '§gWarzone'])) {
            $sender->sendMessage("§cNo puedes usar §e/f stuck §cen §7Wilderness §co §gWarzone§c.");
            return;
        }

        // ==============================
        // DETECTAR CLAIMS REALES
        // ==============================
        $claim = HCFLoader::getInstance()->getClaimManager()->insideClaim($sender->getPosition());

        if ($claim !== null) {
            if (in_array($claim->getType(), [
                'spawn',
                'road',
                'koth'
            ])) {
                $sender->sendMessage("§cNo puedes usar §e/f stuck §cen esta zona.");
                return;
            }
        }

        // ==============================
        // COOLDOWN
        // ==============================
        if ($sender->getSession()->getCooldown('faction.stuck') !== null) return;

        $sender->getSession()->addCooldown('faction.stuck', " §r§3Stuck§r§7: §c", 45);

        $xuid = (string)$sender->getUniqueId();
        $startPos = $sender->getPosition();

        $handler = null;

        $handler = HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(
            function () use (&$handler, &$sender, &$xuid, &$startPos): void {

                $session = HCFLoader::getInstance()->getSessionManager()->getSession($xuid);
                if ($session === null) {
                    $handler->cancel();
                    return;
                }

                if (!$sender->isOnline()) {
                    if ($session->getCooldown('faction.stuck') !== null) $session->removeCooldown('faction.stuck');
                    $handler->cancel();
                    return;
                }

                if ($session->getCooldown('spawn.tag') !== null) {
                    $session->removeCooldown('faction.stuck');
                    $handler->cancel();
                    return;
                }

                if ($startPos->distance($sender->getPosition()) > 2) {
                    if ($session->getCooldown('faction.stuck') !== null) $session->removeCooldown('faction.stuck');
                    $handler->cancel();
                    return;
                }

                if ($session->getCooldown('faction.stuck') === null) {
                    $this->teleport($sender);
                    $handler->cancel();
                }
            }
        ), 20);
    }
}