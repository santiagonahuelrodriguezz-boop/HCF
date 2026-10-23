<?php

namespace hcf\extras;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use hcf\player\UpdateScoreTagTask;
use pocketmine\scheduler\TaskScheduler;

class ScoreTagListener implements Listener {

    /** @var TaskScheduler */
    private TaskScheduler $scheduler;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
    }

    public function onPlayerJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();

        $this->scheduler->scheduleDelayedTask(new UpdateScoreTagTask($player), 20);
    }
}