<?php

namespace Raphael1S\WarpCommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;

class WarpsCommand extends Command implements PluginOwned {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("warps", "Command to see available warps.");
        $this->setPermission("warpcommand.warps");
        $this->plugin = $plugin;
    }
    
    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
        $warps = $config->getAll();

        if (empty($warps)) {
            $sender->sendMessage(TextFormat::YELLOW . "There are no warps on the server.");
        } else {
            $sender->sendMessage(TextFormat::YELLOW . "Warps available:");
            foreach ($warps as $warpName => $warpData) {
                $sender->sendMessage(TextFormat::GREEN . "- " . $warpName);
            }
        }

        return true;
    }
}
