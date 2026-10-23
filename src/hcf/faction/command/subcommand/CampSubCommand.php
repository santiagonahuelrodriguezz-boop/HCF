<?php

namespace hcf\faction\command\subcommand;

use hcf\faction\command\FactionSubCommand;
use hcf\HCFLoader;
use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\world\Position;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\world\WorldException;

class CampSubCommand implements FactionSubCommand
{
    public function execute(CommandSender $sender, array $args) : void 
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (count($args) < 1) {
            $sender->sendMessage("§cUsage: /f camp <faction>");
            return;
        }

        $factionName = $args[0];
        $factionManager = HCFLoader::getInstance()->getFactionManager();
        $faction = $factionManager->getFaction($factionName);

        if ($faction === null) {
            $sender->sendMessage("§cFaction §e$factionName §cdoes not exist!");
            return;
        }
        
        if ($faction->getHome() === null) {
            $sender->sendMessage("§cFaction §e$factionName §cdoes not have home!");
            return;
        }

        $claim = HCFLoader::getInstance()->getClaimManager()->getClaim($factionName);

        if ($claim === null) {
            $sender->sendMessage("§cFaction §e$factionName §cdoes not have a claim!");
            return;
        }

        if ($sender->getSession()->getCooldown('camp.tag') !== null) {
            return;
        }

        $delay = ($sender->getCurrentClaim() === 'Spawn') ? 8 : 30;
        $sender->getSession()->addCooldown('camp.tag', ' §r&eCamp&r&7: &c', $delay);

        $x = $claim->getMinX();
        $z = $claim->getMinZ();
        $world = $sender->getWorld();

        $chunkX = $x >> 4;
        $chunkZ = $z >> 4;

        if (!$world->isChunkGenerated($chunkX, $chunkZ)) {
            $sender->sendMessage("§cThe chunk for the faction's claim is not generated!");
            return;
        }

        if (!$world->isChunkLoaded($chunkX, $chunkZ)) {
            $world->loadChunk($chunkX, $chunkZ);
        }

        try {
            $y = $world->getHighestBlockAt($x, $z);
        } catch (WorldException $e) {
            $sender->sendMessage("§cFailed to get the highest block: " . $e->getMessage());
            return;
        }

        $pos = new Position($x, $y + 1, $z, $world);

        $xuid = (string) $sender->getUniqueId();
        $position = $sender->getPosition();

        /** @var TaskHandler $handler */
        $handler = HCFLoader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use (&$handler, &$sender, &$xuid, &$position, &$pos, &$factionName): void {
            $s = HCFLoader::getInstance()->getSessionManager()->getSession($xuid);

            if (!$sender->isOnline()) {
                if ($s->getCooldown('camp.tag') !== null) $s->removeCooldown('camp.tag');
                $handler->cancel();
                return;
            }

            if ($s->getCooldown('spawn.tag') !== null) {
                $s->removeCooldown('camp.tag');
                $handler->cancel();
                return;
            }

            if ($position->distance($sender->getPosition()) > 2) {
                if ($s->getCooldown('camp.tag') !== null) $s->removeCooldown('camp.tag');
                $handler->cancel();
                return;
            }

            if ($sender->getSession()->getCooldown('camp.tag') === null) {
                $sender->teleport($pos);
                $sender->sendMessage("§aTeleported near §e$factionName's claim!");
                $handler->cancel();
            }
        }), 20);
    }
}
