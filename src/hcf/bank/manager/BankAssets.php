<?php
    
namespace hcf\bank\manager;

use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use hcf\bank\utils\NBT;
use pocketmine\block\Chest as BlockChest;
use pocketmine\block\tile\Chest as ChestTile;
use pocketmine\math\Vector3;
use pocketmine\world\World;
use pocketmine\utils\TextFormat;
use hcf\HCFLoader;
use pocketmine\utils\SingletonTrait;
use hcf\kohi\Kohi;

class BankAssets{
use SingletonTrait;
    public function lootReset(): void {
        $claimManager = HCFLoader::getInstance()->getClaimManager();
        $claim = $claimManager->getClaim('Bank');
        if ($claim !== null) {
            $world = HCFLoader::getInstance()->getBankManager()->getWorld($claim->getWorld());
            if ($world->isLoaded()) {
                $pos1 = new Vector3($claim->getMinX(), 0, $claim->getMinZ());
                $pos2 = new Vector3($claim->getMaxX(), 255, $claim->getMaxZ());
                $this->blockVerify($pos1, $pos2, $world);
                HCFLoader::getInstance()->getServer()->broadcastMessage(         '§f███████'."\n".'§f█§1████§f██'."\n" .'§f█§1█§f███§1█§f█'."\n".'§f█§1█§f███§1█§f█'.' §1§lBANK RESET§r'."\n".
                '§f█§1███§1█§f██'.' §c» §r§7The bank is a reset '."\n".'§f█§1█§f███§1█§f█'."\n".'§f█§1█§f███§1█§f█'."\n".'§f█§1████§f██'."\n".'§f███████'
                );
$msg = new Message();
$webhookUrl = Kohi::WEBHOOK_BANK;
$webHook = new Webhook($webhookUrl);

$embed = new Embed();
$embed->setTitle("Bank Reset ");
$embed->setColor(0xFF0000);
$msg->setContent("");
$embed->setDescription("**Location:** -500 64 -500 \n"."**Time Reset:** 4h \n**World:** world\n \n> - IP: play.arespvp.fun\n> - Port: 19132\n");
$embed->setFooter("Powered by AresPvP"); 
$embed->setThumbnail(Kohi::IMAGE); 
$msg->addEmbed($embed);
$webHook->send($msg);
            } else {
                HCFLoader::getInstance()->getLogger()->error("El mundo no está cargado");
            }
        } else {
            HCFLoader::getInstance()->getLogger()->error("Claim 'Bank' not found!");
        }
    }

    public function blockVerify(Vector3 $pos1, Vector3 $pos2, World $world): void {
        $chunks = [];
        for ($x = $pos1->getX() >> 4; $x <= $pos2->getX() >> 4; $x++) {
            for ($z = $pos1->getZ() >> 4; $z <= $pos2->getZ() >> 4; $z++) {
                $chunk = $world->getChunk($x, $z);
                if ($chunk !== null) {
                    $chunks[] = $chunk;
                }
            }
        }

        foreach ($chunks as $chunk) {
            foreach ($chunk->getTiles() as $tile) {
                if ($tile instanceof ChestTile) {
                    $this->resetContents($tile);
                }
            }
        }
    }

    public function tileVerify(World $world, int $x, int $y, int $z): void {
        $block = $world->getBlockAt($x, $y, $z);
        if ($block instanceof BlockChest) {
            $tile = $world->getTileAt($x, $y, $z);
            if ($tile instanceof ChestTile) {
                $this->resetContents($tile);
            }
        }
    }

    public function resetContents(ChestTile $chest): void {
        $chest->getInventory()->setContents([]);
        
        $loots = $this->getLoots();
        
        if (!empty($loots)) {
          
            $itemsToPlace = mt_rand(3, 5);

         
            $availableSlots = range(0, $chest->getInventory()->getSize() - 1);
            shuffle($availableSlots);

            $randomLoots = [];
            for ($i = 0; $i < $itemsToPlace; $i++) {
                
                $randomIndex = mt_rand(0, count($loots) - 1);
                $randomLoots[] = $loots[$randomIndex];
            }
            
            foreach ($randomLoots as $i => $item) {
                $slot = $availableSlots[$i];
                $chest->getInventory()->setItem($slot, $item);
            }
        }
    }

    public function setLoots(array $loots): void {
        $encodedLoots = [];
        foreach ($loots as $item) {
            $encodedLoots[] = NBT::encodeItem($item);
        }
        $config = HCFLoader::getInstance()->getConfig();
        $config->set('bank.settings.loots', $encodedLoots);
        $config->save();
    }

    public function getLoots(): array {
        $config = HCFLoader::getInstance()->getConfig();
        $encodedLoots = $config->get('bank.settings.loots', []);
        $decodedLoots = [];
        foreach ($encodedLoots as $data) {
            $decodedLoots[] = NBT::decodeItem($data);
        }
        return $decodedLoots;
    }
}