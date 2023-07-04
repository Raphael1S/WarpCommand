<?php

namespace Raphael;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class WarpsCommand extends Command {

    private Teleporte $plugin;

    public function __construct(private Teleporte $plugin) {
        parent::__construct("warps", "Mostra as warps disponíveis");
        $this->setPermission("warps.warps");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        $config = new Config($this->plugin->getDataFolder() . "warps.yml", Config::YAML);
        $warps = $config->getAll();

        if (empty($warps)) {
            $sender->sendMessage(TextFormat::YELLOW . "Não existem warps no servidor.");
        } else {
            $sender->sendMessage(TextFormat::YELLOW . "Warps disponíveis:");
            foreach ($warps as $warpName => $warpData) {
                $sender->sendMessage(TextFormat::GREEN . "- " . $warpName);
            }
        }

        return true;
    }
}
