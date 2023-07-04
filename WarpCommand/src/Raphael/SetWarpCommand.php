<?php

namespace Raphael;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;

class SetWarpCommand extends Command {

    private Teleporte $plugin;

    public function __construct(private Teleporte $plugin) {
        parent::__construct("setwarp", "Define uma nova warp");
        $this->setPermission("setwarp.setwarp");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (empty($args[0])) {
        $sender->sendMessage("Por favor, insira um nome para Warp.");
        return false;
        }

        $warpName = strtolower($args[0]);

        $position = $sender->getPosition();
        $world = $position->getWorld()->getFolderName();

        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
        $config->set($warpName, [
            "x" => $position->getX(),
            "y" => $position->getY(),
            "z" => $position->getZ(),
            "world" => $world,
        ]);
        $config->save();
        $sender->sendMessage(TextFormat::GREEN . "A warp {$warpName} foi criada!");

        // Criar comando de warp
        $this->createWarpCommand($warpName);

        return true;
    }

    private function createWarpCommand(string $warpName) {
        $commandName = strtolower($warpName);

        $this->plugin->getServer()->getCommandMap()->register($commandName, new WarpCommand($this->plugin, $warpName));
    }
}
