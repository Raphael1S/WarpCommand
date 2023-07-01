<?php

namespace Raphael;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\command\PluginCommand;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;

class SetWarpCommand extends Command {

    private Teleporte $plugin;

    public function __construct(Teleporte $plugin) {
        parent::__construct("setwarp", "Define uma nova warp");
        $this->setPermission("setwarp.setwarp");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
        if (empty($args[0])) {
            throw new InvalidCommandSyntaxException();
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
       // $permission = "warp.comando";

     //   $permissionManager = PermissionManager::getInstance();
     //   $permission = new Permission($permission);
     //   $permissionManager->addPermission($permission);
    //    $this->setPermission($permission->getName());

        $this->plugin->getServer()->getCommandMap()->register($commandName, new class($this->plugin, $warpName) extends Command {
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
    $sender->sendMessage(TextFormat::GREEN . "Você foi teleportado!");
    return true;
}

        });
    }
}
