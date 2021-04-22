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

        if (isset($args[0])) {
            if (array_key_exists($args[0], $this->plugin->kits)) {
                $kit = $this->plugin->kits[$args[0]];
                foreach ($kit as $kitItem) {
                    $amount = 1;
                    if (array_key_exists("amount", $kitItem)) {
                        $amount = (int) $kit[$kitItem]["amount"];
                    }
                    $item = Item::get($kitItem["id"], 0, $amount);
                    if (isset($kitItem["enchantments"])) {
                        foreach ($kitItem["enchantments"] as $key => $value) {
                            $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantmentByName($key), $value));
                        }
                    }
                    $sender->getInventory()->addItem($item);
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "That kit doesn't exist");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "Please specify a kit");
        }
    }

}
