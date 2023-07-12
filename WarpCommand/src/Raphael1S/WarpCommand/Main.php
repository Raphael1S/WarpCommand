<?php

namespace Raphael1S\WarpCommand;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase {

        /** @var array */
        private $warps;

    public function onEnable(): void {
        // Load saved warps
        
        $this->warps = (new Config($this->getDataFolder() . "warps.yml", Config::YAML))->getAll();
        
        // Register the /delwarp command
        $this->getServer()->getCommandMap()->register("WarpCommand", new DelWarpCommand($this));

        // Register the /setwarp command
        $this->getServer()->getCommandMap()->register("WarpCommand", new SetWarpCommand($this));

        // Register the /warps command
        $this->getServer()->getCommandMap()->register("WarpCommand", new WarpsCommand($this));
        
        // Register the /updatewarp command
        $this->getServer()->getCommandMap()->register("WarpCommand", new UpdateWarpCommand($this));
        
        // Register existing warp commands
        $this->registerWarpCommands();
    }

    public function registerWarpCommands(): void {
        foreach ($this->warps as $warpName => $warpData) {
            $commandName = strtolower($warpName);
            $warpDescription = $warpData["description"];
            $this->getServer()->getCommandMap()->register("WarpCommand", new WarpCommand($this, $warpName, $warpDescription));
       }
    }
}
