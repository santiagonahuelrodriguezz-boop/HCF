<?php

declare(strict_types=1);

namespace hcf\support\utils;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\utils\DyeColor;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use hcf\support\provider\ProviderSupport;

final class SupportMenu
{
    private const CHEST_NAME = '§c§lPartner Support';
    private const EDIT_CHEST_NAME = '§aChest Edit Support';
    private const ITEM_LORE = [
        '§aThis is a support item',
        '§7Help your favorite §epartner!',
    ];
    private const COOLDOWN_ITEM = 120;
    private const PARTNER_COMMAND_TEMPLATE = 'key give Partner 10 {player}';

    public static function makeItem(string $partnerName): Item
    {
        $partner = ProviderSupport::getInstance()->getPartner($partnerName);
        
        $item = self::get(91);
        if ($item === null) {
            Server::getInstance()->getLogger()->error("Error: No se pudo obtener el item con ID 91. Se usará papel por defecto.");
            $item = VanillaItems::PAPER();
        }

        $item->setCustomName(TextFormat::colorize($partner['format']));
        
        if (is_array(self::ITEM_LORE)) {
            $item->setLore(array_map(function (string $value) {
                return TextFormat::colorize($value);
            }, self::ITEM_LORE));
        }

        $namedtag = $item->getNamedTag();
        $namedtag->setString('support_name', $partnerName);
        $item->setNamedTag($namedtag);
        
        return $item;
    }
   
    public static function openSupportChest(Player $player): void
    {
        $glass = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::YELLOW())->asItem();
        $glass->setCustomName(" ");

        // CAMBIO PRINCIPAL: Usar cofre doble en lugar de cofre simple
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName(TextFormat::colorize(self::CHEST_NAME));
        
        // CAMBIO: Llenar 54 slots en lugar de 27
        for ($i = 0; $i < 54; $i++) {
            $menu->getInventory()->setItem($i, $glass);
        }
        
        // CAMBIO: Verificar slots válidos antes de colocar items
        foreach (ProviderSupport::getInstance()->getPartners()['organization'] as $slot => $name) {
            // Verificar que el slot esté dentro del rango (0-53 para cofre doble)
            if ($slot >= 0 && $slot < 54) {
                $menu->getInventory()->setItem($slot, self::makeItem($name));
            } else {
                Server::getInstance()->getLogger()->warning("Slot {$slot} está fuera de rango para el menú de soporte. Se ignora el partner {$name}.");
            }
        }

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $player = $transaction->getPlayer();
            $item = $transaction->getItemClicked();

            if ($item->getNamedTag()->getTag('support_name')) {
                $player_data = ProviderSupport::getInstance()->getPlayer($player->getName());
                $partner = ProviderSupport::getInstance()->getPartner($item->getNamedTag()->getString('support_name'));

                if ($partner !== null) {
                    if ($player_data === null || $player_data['time'] < time()) {
                        ProviderSupport::getInstance()->setPlayer($player->getName(), self::COOLDOWN_ITEM, $item->getNamedTag()->getString('support_name'));
                        ProviderSupport::getInstance()->addSupport($item->getNamedTag()->getString('support_name'));

                        Server::getInstance()->dispatchCommand(
                            new ConsoleCommandSender($server = Server::getInstance(), $server->getLanguage()),
                            str_replace("{player}", '"' . $player->getName() . '"', self::PARTNER_COMMAND_TEMPLATE)
                        );

                        Server::getInstance()->broadcastMessage('§b» §e' . $player->getName() . ' §7has voted for your Partner. Use §a/support §7to participate too!');
                        $player->sendMessage('§bYou have supported your favorite partner successfully');
                        $player->removeCurrentWindow();
                    } else {
                        $player->sendMessage('§cYou cannot support your partner because you have already supported him or have supported another partner');
                    }
                } else {
                    $player->sendMessage('§cThis partner does not exist, please put the command again');
                    $player->removeCurrentWindow();
                }
            } else {
                $player->removeCurrentWindow();
            }
            return $transaction->discard();
        });
        
        $menu->send($player);
    }

    public static function openSupportEditChest(Player $player): void
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName(TextFormat::colorize(self::EDIT_CHEST_NAME));

        // CAMBIO: Verificar slots válidos antes de colocar items
        foreach (ProviderSupport::getInstance()->getPartners()['organization'] as $slot => $name) {
            // Verificar que el slot esté dentro del rango (0-53 para cofre doble)
            if ($slot >= 0 && $slot < 54) {
                $menu->getInventory()->setItem($slot, self::makeItem($name));
            } else {
                Server::getInstance()->getLogger()->warning("Slot {$slot} está fuera de rango para el menú de edición. Se ignora el partner {$name}.");
            }
        }

        $menu->setListener(function (InvMenuTransaction $transaction): InvMenuTransactionResult {
            $item = $transaction->getItemClickedWith();

            if (!$item->isNull() && !$item->getNamedTag()->getTag('support_name')) {
                return $transaction->discard();
            }
            return $transaction->continue();
        });

        $menu->setInventoryCloseListener(function (Player $player, $inventory): void {
            $contents = $inventory->getContents();
            ProviderSupport::getInstance()->resetSlotsOrganization();
            
            foreach ($contents as $slot => $item) {
                if ($item->getNamedTag()->getTag('support_name')) {
                    ProviderSupport::getInstance()->setSlotOrganization($slot, $item->getNamedTag()->getString('support_name'));
                }
            }
            $player->sendMessage('§bYou have successfully edited the content of the Support Partners');
        });

        $menu->send($player);
    }

    public static function get($id, $meta = 0, $count = 1): ?Item
    {
        $item = LegacyStringToItemParser::getInstance()->parse("{$id}:{$meta}");
        
        if ($item === null) {
            Server::getInstance()->getLogger()->warning("El item con ID {$id} y meta {$meta} no pudo ser encontrado.");
            return null;
        }

        return $item->setCount($count);
    }
}