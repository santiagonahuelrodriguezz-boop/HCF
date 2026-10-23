<?php

declare(strict_types=1);

namespace hcf\koth;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\HCFLoader;
use hcf\player\Player;
use hcf\utils\Utils;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use itoozh\crates\Main;
use hcf\kohi\Kohi;

class Koth {
    private ?Player $capturer = null;
    private string $name;
    private int $time, $progress;
    private int $points;
    private ?string $coords;
    private ?KothCapzone $capzone = null;

    public function __construct(string $name, int $time, int $points, ?string $coords, ?array $claim, ?array $capzone) {
        $this->name = $name;
        $this->time = $time;
        $this->points = $points;
        $this->progress = $time;
        $this->coords = $coords;

        if ($claim !== null) {
            HCFLoader::getInstance()->getClaimManager()->createClaim(
                $name,
                'koth',
                (int)$claim['minX'],
                (int)$claim['maxX'],
                (int)$claim['minZ'],
                (int)$claim['maxZ'],
                $claim['world']
            );
        }

        if ($capzone !== null) {
            $this->capzone = new KothCapzone(
                (int)$capzone['minX'],
                (int)$capzone['maxX'],
                (int)$capzone['minY'],
                (int)$capzone['maxY'],
                (int)$capzone['minZ'],
                (int)$capzone['maxZ'],
                $capzone['world']
            );
        }
    }

    public function getName(): string { return $this->name; }
    public function getTime(): int { return $this->time; }
    public function getProgress(): int { return $this->progress; }
    public function getPoints(): int { return $this->points; }
    public function getCoords(): ?string { return $this->coords; }
    public function getCapzone(): ?KothCapzone { return $this->capzone; }

    public function setTime(int $time): void { $this->time = $time; }
    public function setProgress(int $time): void { $this->progress = $time; }
    public function setPoints(int $points): void { $this->points = $points; }
    public function setCoords(?string $coords): void { $this->coords = $coords; }
    public function setCapzone(KothCapzone $capzone): void { $this->capzone = $capzone; }

    public function update(): void {
        if ($this->capturer === null) {
            $capzone = $this->getCapzone();

            if ($capzone === null || $capzone->getWorld() === null) {
                Server::getInstance()->getLogger()->warning("Koth Error: Capzone or world is null for {$this->getName()}");
                return;
            }

            $world = HCFLoader::getInstance()->getServer()->getWorldManager()->getWorldByName($capzone->getWorld());

            if ($world === null) {
                Server::getInstance()->getLogger()->warning("Koth Error: World '{$capzone->getWorld()}' does not exist.");
                return;
            }

            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                if ($player instanceof Player) {
                    if (
                        $capzone->inside($player->getPosition()) &&
                        $player->getSession()->getFaction() !== null &&
                        ($player->getSession()->getCooldown('pvp.timer') === null &&
                         $player->getSession()->getCooldown('starting.timer') === null)
                    ) {
                        $this->capturer = $player;

                        if ($this->getName() === "Citadel") {
                            HCFLoader::getInstance()->getServer()->broadcastMessage(
                                TextFormat::colorize('&r&6[Citadel] &r&e' . $player->getName() . ' &fis capturing &3 ' . $this->getName())
                            );
                        } elseif ($this->getName() === "End") {
                            HCFLoader::getInstance()->getServer()->broadcastMessage(
                                TextFormat::colorize('&5[End] &r&e' . $player->getName() . ' &fis capturing &d' . $this->getName())
                            );
                        } else {
                            HCFLoader::getInstance()->getServer()->broadcastMessage(
                                TextFormat::colorize('&7[&4KoTh&7] &r&g' . $player->getName() . ' &fis capturing &e' . $this->getName())
                            );
                        }
                        Utils::PlaySound($player, "note.pling", 1, 1);
                        break;
                    }
                }
            }
        } else {
            if (
                !$this->capturer->isOnline() ||
                !$this->getCapzone()->inside($this->capturer->getPosition())
            ) {
                $this->progress = $this->time;
                $this->capturer = null;
                return;
            }

            if ($this->getProgress() === 0) {
                $faction = HCFLoader::getInstance()->getFactionManager()->getFaction($this->capturer->getSession()->getFaction());
                $faction->setPoints($faction->getPoints() + $this->getPoints());
                $faction->setKothCaptures($faction->getKothCaptures() + 1);

                if ($this->getName() === "Citadel") {
                    $keys = Main::getCrate("Citadel")->getKeyItem(5);
                } elseif ($this->getName() === "End") {
                    $keys = Main::getCrate("End")->getKeyItem(4);
                } else {
                    $keys = Main::getCrate("Koth")->getKeyItem(5);
                }

                if ($this->capturer->getInventory()->canAddItem($keys)) {
                    $this->capturer->getInventory()->addItem($keys);
                } else {
                    $this->capturer->getWorld()->dropItem($this->capturer->getPosition(), $keys);
                }

                $msg = new Message();
                $totalpoints = $faction->getPoints();
                $embed = new Embed();
                $embed->setTitle("KoTH " . "{$this->getName()}" . " has finished 🌿");
                $embed->setColor(0xFF0000);
                $embed->setDescription(
                    "**Was captured by:** `{$this->capturer->getName()}`\n" .
                    "**Faction:** `{$this->capturer->getSession()->getFaction()}` \n" .
                    "**Total Points:** `{$totalpoints}` \n" .
                    "**KoTH:** `{$this->getName()}`"
                );
                $embed->setFooter("Powered by Demon Season 1");
                $embed->setThumbnail(Kohi::IMAGE);
                $msg->addEmbed($embed);

                $webhookUrl = Kohi::WEBHOOK_KOTH;
                $webHook = new Webhook($webhookUrl);
                $webHook->send($msg);

                // Mensajes distintos por tipo
                if ($this->getName() === "Citadel") {
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&6[Citadel]"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████ &r&econtrolled by"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(
                        TextFormat::colorize("&7█&5█&7█████ &r&6[&e" . $this->capturer->getSession()->getFaction() . "&6] " .
                        $this->capturer->getName() . "&e!")
                    );
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&5█&7█████"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7██&5████&7█"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
                } elseif ($this->getName() === "End") {
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5███████"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5█&d███&5█&d██"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5█&d█&5███&d█&5█ &r&d[End KoTH]"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5█&d███&5█&d██ &r&econtrolled by"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(
                        TextFormat::colorize("&5█&d█&5███&d█&5█ &r&6[&e" . $this->capturer->getSession()->getFaction() . "&6] " .
                        $this->capturer->getName() . "&e!")
                    );
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5█&d███&5█&d██"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5█&d███&5█&d██"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&5███████"));
                } else {
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7███&3█&7█"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7██&3█&7██ &r&6[KingOfTheHill]"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(
                        TextFormat::colorize("&7█&3███&7███ &r&9" . $this->getName() .
                        " &ehas been controlled by &6[&e" . $this->capturer->getSession()->getFaction() . "&6] " .
                        $this->capturer->getName() . "&e!")
                    );
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7██&3█&7██ &r&6[KingOfTheHill]"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7███&3█&7█"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7█&3█&7███&3█&7█"));
                    HCFLoader::getInstance()->getServer()->broadcastMessage(TextFormat::colorize("&7███████"));
                }

                Utils::kothcontroller();

                $this->progress = $this->time;
                $this->capturer = null;
                HCFLoader::getInstance()->getKothManager()->setKothActive(null);
                return;
            }
            $this->progress--;
        }
    }

    public function getData(): array {
        $data = [
            'time' => $this->getTime(),
            'points' => $this->getPoints(),
            'coords' => $this->getCoords(),
            'claim' => null,
            'capzone' => null
        ];

        if (($claim = HCFLoader::getInstance()->getClaimManager()->getClaim($this->getName())) !== null) {
            $data['claim'] = [
                'minX' => $claim->getMinX(),
                'maxX' => $claim->getMaxX(),
                'minZ' => $claim->getMinZ(),
                'maxZ' => $claim->getMaxZ(),
                'world' => $claim->getWorld()
            ];
        }

        if ($this->getCapzone() !== null) {
            $data['capzone'] = [
                'minX' => $this->getCapzone()->getMinX(),
                'maxX' => $this->getCapzone()->getMaxX(),
                'minY' => $this->getCapzone()->getMinY(),
                'maxY' => $this->getCapzone()->getMaxY(),
                'minZ' => $this->getCapzone()->getMinZ(),
                'maxZ' => $this->getCapzone()->getMaxZ(),
                'world' => $this->getCapzone()->getWorld()
            ];
        }

        return $data;
    }
}