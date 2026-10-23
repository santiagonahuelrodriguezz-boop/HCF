<?php

namespace hcf\bank\command;

use muqsit\invmenu\InvMenu;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use hcf\HCFLoader;
use pocketmine\command\{Command, CommandSender};
use pocketmine\inventory\Inventory;
use hcf\bank\manager\BankAssets;

class BankCommand extends Command
{
    public function __construct()
    {
        parent::__construct('bank', 'Bank System');
        $this->setPermission('default.command.use');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        $config = HCFLoader::getInstance()->getConfig();
        
        if (empty($args[0])) {
            $coords = $config->getNested('bank.settings.coords', null);
            $sender->sendMessage(""."\n"."§bBANK INFORMATION:"."\n");

            if ($coords !== null) {
                $sender->sendMessage("§sCoordinates: §g".$coords['x'].', '. $coords['z']."\n"."");
            } else {
                $sender->sendMessage('§b» §cBank coordinates not set. Use /bank setcoords to set them.');
            }
            return;
        }

        switch ($args[0]) {
            case 'help':
                if (!$sender->hasPermission('bank.admin.permission')) {
                    $sender->sendMessage('§cYou dont have permissions');
                    return;
                }
                $sender->sendMessage(""."\n"."§sADMIN COMMANDS\n".
                "§b/bank edit - §7Edit the bank loot".
                "\n".
                "§b/bank setcoords - §7Set bank coordinates"."\n"."");
                break;

            case 'edit':
                if (!$sender->hasPermission('bank.admin.permission')) {
                    $sender->sendMessage('§cYou dont have permissions');
                    return;
                }
                $bankManager = BankAssets::getInstance();
                $chest = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
                $chest->setName(TextFormat::colorize('§l§bBANK EDIT'));
                $loots = $bankManager->getLoots();

                if ($loots !== null) {
                    foreach ($loots as $slot => $item) {
                        $chest->getInventory()->setItem($slot, $item);
                    }
                }

                $chest->setInventoryCloseListener(function (Player $player, Inventory $inventory) use ($bankManager): void {
                    $bankManager->setLoots($inventory->getContents());
                    $player->sendMessage('§b» §7The Bank loot has been modified §acorrectly');
                });
                $chest->send($sender);
                break;

            case 'setcoords':
                if (!$sender instanceof Player) {
                    $sender->sendMessage('This command can only be used in-game.');
                    return;
                }

                $x = $sender->getPosition()->getFloorX();
                $z = $sender->getPosition()->getFloorZ();

                $config->setNested('bank.settings.coords', ['x' => $x, 'z' => $z]);
                HCFLoader::getInstance()->saveConfig();

                $sender->sendMessage(TextFormat::colorize('§c» §7Bank coordinates set to: §a' . $x . ', ' . $z));
                break;

            default:
                $sender->sendMessage('§cUnknown command. Use /bank help for more information.');
                break;
        }
    }
}