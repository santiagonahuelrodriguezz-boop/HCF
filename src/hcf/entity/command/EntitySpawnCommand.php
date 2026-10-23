<?php

namespace hcf\entity\command;

use hcf\entity\server\DailyEntity;
use hcf\entity\server\NetworksEntity;
use hcf\entity\server\ShopEntity;
use hcf\abilities\entity\AbilityEntity;
use hcf\entity\tops\TopKillsEntity;
use hcf\entity\tops\TopFactionsEntity;
use hcf\entity\tops\TopKDREntity;
use hcf\entity\server\TeleportEntity;
use hcf\entity\server\InfoEntity;
use hcf\entity\server\RepairEntity;
use hcf\entity\server\CoinShopEntity;
use hcf\entity\server\DeathEntity;

use hcf\support\entity\SupportEntity;
use hcf\entity\server\SpawnEntity;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EntitySpawnCommand extends Command
{

    public function __construct()
    {
        parent::__construct("entity", "§ekdr, kills, factions, teleport, coinshop, info, spawn, support, abilities, deathban, shop, links, daily.");
        $this->setPermission('op.permission');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::colorize('§cThis command can only be used in-game.'));
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage('§cThese are not sufficient arguments  subcommand, use §b/entity help');
            return;
        }

        switch (strtolower($args[0])) {
            case 'teleport':
                $entity = TeleportEntity::create($sender);
                $entity->spawnToAll();
                $sender->sendMessage('§r§aTeleport NPC created successfully!');
                break;
                  
            case 'info':
                $infoEntity = InfoEntity::create($sender);
                $infoEntity->spawnToAll();
                $sender->sendMessage('§r§aInfo NPC created successfully!');
                break;
           
           case 'abilities':
                $abilityEntity = AbilityEntity::create($sender);
                $abilityEntity->spawnToAll();
                $sender->sendMessage('§r§aAbilities NPC created successfully!');
                break;
          
          case 'deathban':
                $deathEntity = DeathEntity::create($sender);
                $deathEntity->spawnToAll();
                $sender->sendMessage('§r§aDeathBan NPC created successfully!');
                break;
          case 'repair':
                $repairEntity = RepairEntity::create($sender);
                $repairEntity->spawnToAll();
                $sender->sendMessage('§r§aRepair NPC created successfully!');
                break;
                
          case 'support':
                $infoEntity = SupportEntity::create($sender);
                $infoEntity->spawnToAll();
                $sender->sendMessage('§r§aSupport NPC created successfully!');
                break;
                
           case 'spawn':
                $spawn = SpawnEntity::create($sender);
                $spawn->spawnToAll();
                $sender->sendMessage('§r§aSpawn NPC created successfully!');
                break;
                     
           case 'kills':
                $kills = TopKillsEntity::create($sender);
                $kills->spawnToAll();
                $sender->sendMessage('§r§aKills NPC created successfully!');
                break;
                     
           case 'kdr':
                $kdr = TopKDREntity::create($sender);
                $kdr->spawnToAll();
                $sender->sendMessage('§r§aKDR NPC created successfully!');
                break;
                     
           case 'factions':
                $fac = TopFactionsEntity::create($sender);
                $fac->spawnToAll();
                $sender->sendMessage('§r§aFactions NPC created successfully!');
                break;
           case 'shop':
                $shopEntity = shopEntity::create($sender);
                $shopEntity->spawnToAll();
                $sender->sendMessage('§r§aShop NPC created successfully!');
                break;
                
            case 'links':
                $links = NetworksEntity::create($sender);
                $links->spawnToAll();
                $sender->sendMessage('§r§aLinks NPC created successfully!');
                break;
                
            case 'help': 
                $sender->sendMessage(""."\n"."§e>> §bENTITIES SPAWN: HELP §e<<<\n §e>> §cCommand: /entities [Entity or NPC] \n §e>> §cEntities: §brepair, deathban, spawn, teleport, coinshop, factions, kills, kdr, support, abilities, info \n §e>> §cExample: §b/entity repair"."\n");
                break;
                
            default:
                $sender->sendMessage('§cUnknown subcommand. Use: teleport, info, coinshop, kdr, kills, factions, spawn, support, abilities, deathban, repair, shop, links.');
                break;
        }
    }
}