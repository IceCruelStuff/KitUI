<?php

namespace KitUI;

use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use jojoe77777\FormAPI\SimpleForm;
use KitUI\Commands\KitsCommand;

class KitUI extends PluginBase {

    public $kits = [];

    public function onEnable() {
        $this->saveDefaultConfig();
        if ($this->getConfig()->get("require-operator-permission")) {
            $this->getServer()->getPluginManager()->addPermission(new Permission("kitui.kits", "Allows user to use /kits command", Permission::DEFAULT_OP));
        } else {
            $this->getServer()->getPluginManager()->addPermission(new Permission("kitui.kits", "Allows user to use /kits command", Permission::DEFAULT_TRUE));
        }
        $this->getServer()->getCommandMap()->register("kit", new KitsCommand($this));
        $kits = $this->getConfig()->get("kits");
        foreach ($kits as $key => $value) {
            $this->kits[$key] = $value;
        }
    }

    public static function sendForm(Player $player) {
        $command = new KitsCommand($this);
        $command->sendForm($player);
    }

    public function disable() {
        $this->getServer()->getPluginManager()->disablePlugin($this);
    }

}
