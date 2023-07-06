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

class WarpCommand extends Command implements PluginOwned {
    
    private Main $plugin;
    private string $warpName;

    public function __construct(Main $plugin, string $warpName) {
        parent::__construct($warpName, "Warp to " . $warpName);
        $this->plugin = $plugin;
        $this->warpName = $warpName;
        $this->setDescription("Warp to {$warpName}");
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
        return true;
    }
}
