<?php

namespace KitUI;

use pocketmine\plugin\PluginBase;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase {

    public $kits = [];

    public function onEnable() {
        $this->saveDefaultConfig();
        $kits = $this->getConfig()->get("kits");
        foreach ($kits as $key => $value) {
            $this->kits[$key] = $value;
        }
    }

}
