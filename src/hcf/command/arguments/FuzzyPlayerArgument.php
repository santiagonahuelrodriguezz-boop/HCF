<?php


namespace hcf\command\arguments;


use CortexPE\Commando\args\BaseArgument;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;

final class FuzzyPlayerArgument extends BaseArgument {
	public function getNetworkType(): int {
		return AvailableCommandsPacket::ARG_TYPE_TARGET;
	}

	public function canParse(string $testString, CommandSender $sender): bool {
		return Player::isValidUserName($testString);
	}

	public function parse(string $argument, CommandSender $sender): mixed {
		$srv = Server::getInstance();
		if ($argument === '@a' || $argument === 'all') {
			return $argument;
        }
		return $srv->getPlayerByPrefix($argument) ?? $srv->getOfflinePlayer($argument);
	}

	public function getTypeName(): string {
		return "player";
	}
}