<?php

namespace Raphael1S\WarpCommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class WarpCommand extends Command implements PluginOwned {
    
    private Main $plugin;
    private string $warpName;
    private string $warpDescription;

    public function __construct(Main $plugin, string $warpName, string $warpDescription) {
        parent::__construct($warpName, "Warp to " . $warpName);
        $this->plugin = $plugin;
        $this->warpName = $warpName;
        $this->warpDescription = $warpDescription;
        $this->setDescription($this->warpDescription);
        $this->setPermission("warpcommand.command");
    }
    
    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "This command can only be executed by one player!");
            return false;
        }

        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
        $warpData = $config->get($this->warpName);

        $worldName = $warpData["world"];
        $x = $warpData["x"];
        $y = $warpData["y"];
        $z = $warpData["z"];

        $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($worldName);
        if ($world === null) {
            $sender->sendMessage(TextFormat::RED . "Warp world {$this->warpName} not found!");
            return false;
        }

        $position = new Position($x, $y, $z, $world);

        $sender->teleport($position);
        $sender->sendMessage(TextFormat::GREEN . "You have been teleported!");
        $title = "§e× {$this->warpName} ×";
        $subtitle = "§aYou've been teleported!";
        $fadeIn = 20; // 20 ticks (1 second) for title to fade in
        $stay = 60;   // 40 ticks (2 seconds) for title to stay on screen
        $fadeOut = 20; // 20 ticks (1 second) for title to fade out
        
        $sender->sendTitle($title, $subtitle, $fadeIn, $stay, $fadeOut);
        $sound = "random.levelup";
        $volume = "1";
        $pitch = "1";
        $this->playSound($sender, $sound, $volume, $pitch);
        return true;
        }

        protected function playSound($sender, string $sound, float $volume = 0, float $pitch = 0): void{
        $packet = new PlaySoundPacket();
        $packet->soundName = $sound;
        $packet->x = $sender->getPosition()->getX();
        $packet->y = $sender->getPosition()->getY();
        $packet->z = $sender->getPosition()->getZ();
        $packet->volume = $volume;
        $packet->pitch = $pitch;
        $sender->getNetworkSession()->sendDataPacket($packet);
        }
}
