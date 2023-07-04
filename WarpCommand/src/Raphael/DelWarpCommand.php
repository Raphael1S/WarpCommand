<?php

namespace Raphael;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\player\Player;

class DelWarpCommand extends Command {

    private Teleporte $plugin;

    public function __construct(Teleporte $plugin) {
        parent::__construct("delwarp", "Deleta uma warp existente");
        $this->setPermission("delwarp.delwarp");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {

        if (empty($args[0])) {
        $sender->sendMessage("Por favor, insira um nome para Warp.");
        return false;
        }

        $warpName = strtolower($args[0]);

        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);

        if ($config->exists($warpName)) {
            $config->remove($warpName);
            $config->save();
            $sender->sendMessage(TextFormat::GREEN . "A warp {$warpName} foi deletada!");
        } else {
            $sender->sendMessage(TextFormat::RED . "A warp {$warpName} não existe!");
        }

        // Remover comando de warp
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
