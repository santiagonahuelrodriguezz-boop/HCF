<?php

declare(strict_types=1);

namespace hcf\command;

use hcf\player\Player;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\TextFormat;
use ysnl\Loader; // Importamos el Loader del StaffMode

class NearCommand extends Command
{
    public function __construct()
    {
        parent::__construct('near', 'Command for near');
        $this->setPermission('near.command');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$sender instanceof Player) {
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        $players = array_filter(
            $sender->getServer()->getOnlinePlayers(),
            function ($player) use ($sender): bool {
                if (!$player instanceof Player) {
                    return false;
                }

                // Evitamos que se detecte al propio jugador
                if ($player->getId() === $sender->getId()) {
                    return false;
                }

                // Verificamos si está en StaffMode
                $session = Loader::getInstance()->getSessionManager()->getSession($player->getXuid());
                if ($session !== null && $session->hasMod()) {
                    return false; // Ignoramos si está en staffmode
                }

                // Chequeamos distancia
                return $player->getPosition()->distance($sender->getPosition()) <= 100;
            }
        );

        if (empty($players)) {
            $sender->sendMessage(TextFormat::colorize('&c× No hay jugadores cercanos ×'));
            return;
        }

        $sender->sendMessage(TextFormat::colorize('&c× Near Players ×' . "\n" . implode("\n", array_map(
            function (Player $player) use ($sender) {
                return '&f' . $player->getName() . ' &7(' . intval($sender->getPosition()->distance($player->getPosition())) . 'm)&f';
            },
            $players
        ))));
    }
}