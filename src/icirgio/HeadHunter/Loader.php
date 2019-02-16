<?php
/**
 * Created by PhpStorm.
 * User: iCirgio
 * Date: 2/16/2019
 * Time: 10:58 AM
 */
namespace icirgio\HeadHunter;

use pocketmine\utils\Config;
use pocketmine\event\Listener;
use HeadHunter\HeadHuntingEvent;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase implements Listener {

    public function onLoad()
    {
        if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI") != null) {
            $this->getServer()->getLogger()->notice("EconomyAPI has been found");
        } else {
            $this->getServer()->getLogger()->notice("EconomyAPI HAS NOT been found, this will throw errors");
        }
    }

    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . 'config.yml', Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents(new HeadHuntingEvent($this), $this);
    }
}
