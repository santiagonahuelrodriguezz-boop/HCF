<?php

namespace hcf\command;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\block\VanillaBlocks; 
use pocketmine\block\utils\DyeColor;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use hcf\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\sound\XpLevelUpSound;
use hcf\HCFLoader;
use pocketmine\utils\CooldownTrait;

class LFFCommand extends Command 
{
    private array $cooldowns = []; 

    public function __construct()
    {
        parent::__construct('lff', 'Command to search for faction');
        $this->setPermission("player.use");
    }

    public function execute(CommandSender $sender, string $label, array $args): void 
    {
        if (!$this->testPermission($sender)) {
            $sender->sendMessage("§cYou do not have permission to use this command.");
            return;
        }

        if (!$sender instanceof Player) { 
            return;
        }

   
        $cooldown = 60; 
        $currentTime = time();
        $playerName = $sender->getName();

        if (isset($this->cooldowns[$playerName]) && ($currentTime - $this->cooldowns[$playerName]) < $cooldown) {
            $remainingTime = $cooldown - ($currentTime - $this->cooldowns[$playerName]);
            $sender->sendMessage("§b» §7Debes esperar  §g[$remainingTime] §7segundos antes de volver a usar este comando.");
            return;
        }

        $this->cooldowns[$playerName] = $currentTime;

     
        $this->openLFFMenu($sender);
    }

    private function openLFFMenu(Player $player): void 
    {
        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $menu->setName("§l§aSelect Your Class§r");

    
        $enc = new EnchantmentInstance(VanillaEnchantments::UNBREAKING());
        $glass = VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::CYAN())->asItem();
        $glass->setCustomName(" ");
        $glass->addEnchantment($enc);


        $archer = VanillaItems::BOW();
        $archer->setCustomName("§l§uArcher");
        $bard = VanillaItems::GOLDEN_CHESTPLATE();
        $bard->setCustomName("§l§gBard");
        $diamond = VanillaItems::DIAMOND_CHESTPLATE();
        $diamond->setCustomName("§l§sDiamond");
        $rogue = VanillaItems::IRON_INGOT();
        $rogue->setCustomName("§f§lRogue");

   
        for ($i = 0; $i < 27; $i++) {
            $menu->getInventory()->setItem($i, $glass);
        }

  
        $menu->getInventory()->setItem(12, $archer);
        $menu->getInventory()->setItem(14, $bard);
        $menu->getInventory()->setItem(16, $diamond);
        $menu->getInventory()->setItem(18, $rogue);

    
        $menu->setListener(function (InvMenuTransaction $transaction) use ($menu): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $itemClicked = $transaction->getItemClicked();

            if ($itemClicked->getCustomName() === "§l§sDiamond") {
                $this->broadcastClassSelection($player, "Diamond");
            } elseif ($itemClicked->getCustomName() === "§l§gBard") {
                $this->broadcastClassSelection($player, "Bard");
            } elseif ($itemClicked->getCustomName() === "§l§uArcher") {
                $this->broadcastClassSelection($player, "Archer");
            } elseif ($itemClicked->getCustomName() === "§f§lRogue") {
                $this->broadcastClassSelection($player, "Rogue");
            }

         
            $player->removeCurrentWindow();

            return $transaction->discard();
        });

        $menu->send($player);
    }

    private function broadcastClassSelection(Player $player, string $class): void
    {
        $player->getWorld()->addSound($player->getPosition(), new XpLevelUpSound(100));
        Server::getInstance()->broadcastMessage("§r§g==============================" . "\n" . "§b" . $player->getName() . "§r§e Esta buscando faction" . "\n" . "§bClass§7: §s$class" . "\n" . "§r§g==============================");
    }
}