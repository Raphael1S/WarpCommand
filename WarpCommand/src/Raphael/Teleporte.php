<?php

namespace Raphael;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class Teleporte extends PluginBase {

    /** @var array */
    private $warps;

    public function onEnable(): void {
        // Carrega as warps salvas
        $this->warps = (new Config($this->getDataFolder() . "warps.yml", Config::YAML))->getAll();

        // Registra o comando /delwarp
        $this->getServer()->getCommandMap()->register("delwarp", new DelWarpCommand($this));
        
        // Registra o comando /setwarp
        $this->getServer()->getCommandMap()->register("setwarp", new SetWarpCommand($this));

        // Registra os comandos das warps existentes
        $this->registerWarpCommands();
        $this->getLogger()->warning("WarpCommand iniciado com sucesso! @ Raphael S.");
    }

    public function registerWarpCommands(): void {
        foreach ($this->warps as $warpName => $warpData) {
            $commandName = strtolower($warpName);

            $this->getServer()->getCommandMap()->register($commandName, new WarpCommand($this, $warpName));
        }
    }
  }
