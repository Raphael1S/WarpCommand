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

class UpdateWarpCommand extends Command implements PluginOwned {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("updatewarp", "The command to update an existing warp.");
        $this->setPermission("warpcommand.updatewarp");
        $this->plugin = $plugin;
    }
    
    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {          
            $sender->sendMessage(TextFormat::RED . "This command can only be executed by a player.");
            return false;
        }
        
        if (empty($args[0])) {
            $sender->sendMessage(TextFormat::RED . "Please enter the name of the warp to update.");
            return false;
        }

        $warpName = strtolower($args[0]);

        $position = $sender->getPosition();
        $world = $position->getWorld()->getFolderName();

        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
        if (!$config->exists($warpName)) {
            $sender->sendMessage(TextFormat::RED . "The warp {$warpName} does not exist.");
            return false;
        }

        $warpData = $config->get($warpName);
        $warpData["x"] = $position->getX();
        $warpData["y"] = $position->getY();
        $warpData["z"] = $position->getZ();
        $warpData["world"] = $world;

        $config->set($warpName, $warpData);
        $config->save();
        $sender->sendMessage(TextFormat::GREEN . "The warp {$warpName} has been updated!");

        return true;
    }
}
