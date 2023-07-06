<?php

namespace Raphael1S\WarpCommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class DelWarpCommand extends Command implements PluginOwned {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("delwarp", "Command to delete a warp.");
        $this->setPermission("warpcommand.delwarp");
        $this->plugin = $plugin;
    }

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {          
            $sender->sendMessage(TextFormat::RED . "This command can only be executed by one player.");
            return false;
        }
        
        if (empty($args[0])) {
            $sender->sendMessage(TextFormat::RED . "Please enter a name for the Warp.");
        return false;
        }

        $warpName = strtolower($args[0]);

        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);

        if ($config->exists($warpName)) {
            $config->remove($warpName);
            $config->save();
            $sender->sendMessage(TextFormat::RED . "The warp {$warpName} has been deleted!");
        } else {
            $sender->sendMessage(TextFormat::RED . "The warp {$warpName} does not exist!");
        }
        $this->removeWarpCommand($warpName);
        return true;
    }

    private function removeWarpCommand(string $warpName) {
        $commandName = strtolower($warpName);

        $commandMap = $this->plugin->getServer()->getCommandMap();
        $command = $commandMap->getCommand($commandName);

        if ($command !== null && $command instanceof WarpCommand) {
            $commandMap->unregister($command);
        }
    }
}
