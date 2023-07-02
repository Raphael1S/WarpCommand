<?php

namespace Raphael;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use Raphael\Teleporte;

class WarpCommand extends Command {
    private Teleporte $plugin;
    private string $warpName;

    public function __construct(Teleporte $plugin, string $warpName) {
        parent::__construct($warpName, "Warp para " . $warpName);
        $this->plugin = $plugin;
        $this->warpName = $warpName;
        $this->setDescription("Warp para {$warpName}");
        $this->setPermission("warp.comando");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . "Este comando só pode ser executado por um jogador!");
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
            $sender->sendMessage(TextFormat::RED . "O mundo da warp {$this->warpName} não foi encontrado!");
            return false;
        }

        $position = new Position($x, $y, $z, $world);

        $sender->teleport($position);
        $sender->sendMessage(TextFormat::GREEN . "Você foi teleportado para a warp {$this->warpName}!");
        return true;
    }
}
