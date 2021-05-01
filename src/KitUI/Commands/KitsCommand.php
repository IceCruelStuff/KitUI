<?php

namespace KitUI\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use jojoe77777\FormAPI\SimpleForm;
use KitUI\Main;

class KitsCommand extends Command implements PluginIdentifiableCommand {

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
        parent::__construct(
            "kit",
            "Gives kit to player",
            "/kit <kit>"
        );
        $this->setPermission("kitui.kits");
    }

    public function getPlugin() : Plugin {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, string $label, array $args) {
        if (!$this->testPermission($sender)) {
            return;
        }

        if ($sender instanceof Player) {
            if (isset($args[0])) {
                if (array_key_exists($args[0], $this->plugin->kits)) {
                    $kit = $this->plugin->kits[$args[0]];
                    $this->giveKit($sender, $kit);
                    $sender->sendMessage(TextFormat::GREEN . "You received " . $args[0] . " kit");
                } else {
                    $sender->sendMessage(TextFormat::RED . "That kit doesn't exist");
                }
            } else {
                $this->sendForm($sender);
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "Please run this command in-game");
        }
    }

    private function giveKit($player, $kit) {
        $kitItems = null;
        if (array_key_exists("items", $kit)) {
            $kitItems = $kit["items"];
        }
        foreach ($kitItems as $kitItem) {
            $amount = 1;
            if (array_key_exists("amount", $kitItem)) {
                $amount = (int) $kitItem["amount"];
            }
            $meta = 0;
            if (array_key_exists("meta", $kitItem)) {
                $meta = (int) $kitItem["meta"];
            }
            $item = Item::get($kitItem["id"], $meta, $amount);
            if (array_key_exists("enchantments", $kitItem)) {
                foreach ($kitItem["enchantments"] as $key => $value) {
                    $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($key), $value));
                }
            }
            if (array_key_exists("name", $kitItem)) {
                $item->setCustomName($kitItem["name"]);
            }
            $player->getInventory()->addItem($item);
        }
    }

    private function sendForm($sender) {
        $form = new SimpleForm(function (Player $player, $data = null) {
            if ($data === null) {
                return;
            }

            $kits = [];
            foreach ($this->plugin->kits as $kit) {
                $kits[] = $kit;
            }
            $closeButtonIndex = count($kits) - 1;
            for ($i = 0; $i < $closeButtonIndex; $i++) {
                switch ($data) {
                    case $i:
                        $this->giveKit($player, $kit[$i]);
                        break;
                }
            }
            switch ($data) {
                case $closeButtonIndex:
                    break;
            }
        });
        $form->setTitle("Kits");
        foreach ($this->plugin->kits as $key => $value) {
            if (array_key_exists("displayName", $this->plugin->kits[$key])) {
                $form->addButton($this->plugin->kits[$key]["displayName"]);
            } else {
                $form->addButton($key);
            }
        }
        $form->addButton("Close");
        $form->sendToPlayer($sender);
    }

}
