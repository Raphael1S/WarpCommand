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

class SetWarpCommand extends Command implements PluginOwned {

    private Main $plugin;

    public function __construct(Main $plugin) {
        parent::__construct("setwarp", "The command to create a new warp.");
        $this->setPermission("warpcommand.setwarp");
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
        
        if (empty($args[1])) {
            $sender->sendMessage(TextFormat::RED . "Please enter a permission for the Warp.");
        return false;
        }

        $warpName = strtolower($args[0]);
        $warpPerm = strtolower($args[1]);

        $position = $sender->getPosition();
        $world = $position->getWorld()->getFolderName();
        $description = "Warp to {$warpName}";

        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
        $config->set($warpName, [
            "x" => $position->getX(),
            "y" => $position->getY(),
            "z" => $position->getZ(),
            "world" => $world,
            "description" => $description,
            "permission" => $warpPerm,
        ]);
        $config->save();
        $sender->sendMessage(TextFormat::GREEN . "The warp {$warpName} has been created!\n- World: §f{$world}\n§a- Description: §f{$description}\n§a- Permission: §f{$warpPerm}");

        $this->createWarpCommand($warpName, $warpPerm);

        return true;
    }

    private function createWarpCommand(string $warpName, string $warpPerm): void {
    $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
    $warpData = $config->get($warpName);
    
    if ($warpData === null) {
        return;
    }

    $warpDescription = $warpData["description"];

    $this->plugin->getServer()->getCommandMap()->register("WarpCommand", new WarpCommand($this->plugin, $warpName, $warpDescription, $warpPerm));
 }
}
