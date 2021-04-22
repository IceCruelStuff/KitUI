<?php

namespace KitUI;

use pocketmine\permission\Permission;
use pocketmine\plugin\PluginBase;
use jojoe77777\FormAPI\SimpleForm;
use KitUI\Commands\KitsCommand;

class Main extends PluginBase {

    public $kits = [];

    public function onEnable() {
        $this->saveDefaultConfig();
        if ($this->getConfig()->get("require-operator-permission")) {
            $this->getServer()->getPluginManager()->addPermission(new Permission("kitui.kits", "Allows user to use /kits command", Permission::DEFAULT_OP));
        } else {
            $this->getServer()->getPluginManager()->addPermission(new Permission("kitui.kits", "Allows user to use /kits command", Permission::DEFAULT_TRUE));
        }
        $this->getServer()->getCommandMap()->register(new KitsCommand($this));
        $kits = $this->getConfig()->get("kits");
        foreach ($kits as $key => $value) {
            $this->kits[$key] = $value;
        }
    }

}
