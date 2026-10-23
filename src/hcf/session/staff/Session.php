<?php

namespace hcf\session\staff;

use pocketmine\player\Player;
use pocketmine\math\Vector3;

class Session {

    public Player $player;
	private $position;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }
	public function setPosition(Vector3 $position) {
        $this->position = $position;
    }

    
    public function getPosition(): Vector3 {
        return $this->position;
	}
}