<?php

namespace hcf\command\arguments;

use CortexPE\Commando\args\FloatArgument;
use pocketmine\command\CommandSender;

class MoneyArgument extends FloatArgument {

	public function getTypeName(): string {
		return "money";
	}

	public function canParse(string $testString, CommandSender $sender): bool {
		if(!parent::canParse($testString, $sender))return false;
		$val = $this->parse($testString, $sender);
		return $val > 0 && $val < PHP_FLOAT_MAX;
	}

	public function parse(string $argument, CommandSender $sender): float {
		if ($argument === 'all') return $argument;
		return round((float)$argument, 2);
	}
}