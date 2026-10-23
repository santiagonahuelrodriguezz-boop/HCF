<?php

declare(strict_types=1);

namespace hcf\handler\vkit\task;

use hcf\HCFLoader;
use hcf\player\Player;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use pocketmine\block\VanillaBlocks;

/**
 * Class vKitTask
 * @package juqn\hcf\vkit\task
 */
class vKitTask extends Task
{
    
    /** @var Player */
    private Player $player;
    /** @var string */
    private string $vKitName;
    /** @var InvMenu */
    private InvMenu $menu;
    /** @var int[] */
    private array $slots = [
        9,
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17
    ];
    /** @var int */
    private int $time, $slot;
    
    /** @var bool */
    public bool $cancel = false;
    
    /**
     * vKitTask construct.
     * @param Player $player
     * @param string $vKitName
     */
    public function __construct(Player $player, string $vKitName)
    {
        $this->player = $player;
        $this->vKitName = $vKitName;
        
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->menu->getInventory()->setContents([
            0 => VanillaBlocks::GLASS()->asItem(),//20:7
            1 => VanillaBlocks::GLASS()->asItem(),//20:7
            2 => VanillaBlocks::GLASS()->asItem(),//20:7
            3 => VanillaBlocks::GLASS()->asItem(),//20:7
            4 => VanillaBlocks::HOPPER()->asItem(), // Hopper
            5 => VanillaBlocks::GLASS()->asItem(),//20:7
            6 => VanillaBlocks::GLASS()->asItem(),//20:7
            7 => VanillaBlocks::GLASS()->asItem(),//20:7
            8 => VanillaBlocks::GLASS()->asItem(),//20:7
            9 => VanillaBlocks::QUARTZ()->asItem(),
            10 => VanillaBlocks::QUARTZ()->asItem(),
            11 => VanillaBlocks::QUARTZ()->asItem(),
            12 => VanillaBlocks::QUARTZ()->asItem(),
            13 => VanillaBlocks::QUARTZ()->asItem(),
            14 => VanillaBlocks::QUARTZ()->asItem(),
            15 => VanillaBlocks::QUARTZ()->asItem(),
            16 => VanillaBlocks::QUARTZ()->asItem(),
            17 => VanillaBlocks::QUARTZ()->asItem(),
            18 => VanillaBlocks::GLASS()->asItem(),//20:7
            19 => VanillaBlocks::GLASS()->asItem(),//20:7
            20 => VanillaBlocks::GLASS()->asItem(),//20:7
            21 => VanillaBlocks::GLASS()->asItem(),//20:7
            22 => VanillaBlocks::GLASS()->asItem(),//20:7
            23 => VanillaBlocks::GLASS()->asItem(),//20:7
            24 => VanillaBlocks::GLASS()->asItem(),//20:7
            25 => VanillaBlocks::GLASS()->asItem(),//20:7
            26 => VanillaBlocks::GLASS()->asItem(),//20:7
        ]);
        $this->menu->setListener(InvMenu::readonly());
        $this->menu->send($player, TextFormat::colorize('&r&8Unlock &b' . $vKitName . ' &r&8vKit?'));
        
        $this->slot = $this->slots[array_rand($this->slots)];
        $this->menu->getInventory()->setItem($this->slot, VanillaBlocks::EMERALD()->asItem());
        
        $chance = rand(0, 100);
        
        if ($chance <= 35)
            $this->time = 58 - $this->slot;
        else {
            $times = [56, 55, 54, 53, 52];
            $this->time = $times[array_rand($times)] - $this->slot;
        }
    }
    
    public function onRun(): void
    {
        if (!$this->player->isOnline()) {
            $this->cancel = true;
        }
        
        if ($this->cancel) {
            $this->getHandler()?->cancel();
            return;
        }
        $this->time--;
        $this->menu->getInventory()->setItem($this->slot, VanillaBlocks::QUARTZ()->asItem());
        $this->slot++;
            
        if ($this->slot === 18)
            $this->slot = 9;
        $this->menu->getInventory()->setItem($this->slot, VanillaBlocks::EMERALD()->asItem());
            
        if ($this->slot === 13)
            $this->menu->setName(TextFormat::colorize('&r&8Unlock &b' . $this->vKitName . ' &r&7vKit?'));
        else
            $this->menu->setName(TextFormat::colorize('&r&8Unlock &b' . $this->vKitName . ' &r&8vKit?'));
        
        if ($this->time === 0) {
            $this->cancel = true;
            
            $vKit = HCFLoader::getInstance()->getHandlerManager()->getvKitManager()->getvKit($this->vKitName);  
            $vKit?->giveItems($this->player);
            
            $this->player->getSession()->addCooldown('vkit.' . $this->vKitName, '    ', 259200, false, false);
            
            if ($this->slot === 13) {
                $this->player->getSession()->addCooldown('vkit.unlocked.' . $this->vKitName, '  ', 86400 * 60, true, false);
                $this->player->sendMessage(TextFormat::colorize('&7You have &3unlocked &7the &f' . $this->vKitName . ' &7vkit for 60 days'));
            }
        }
    }
}