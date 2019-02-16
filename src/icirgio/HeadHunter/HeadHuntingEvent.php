<?php
/**
 * Created by PhpStorm.
 * User: iCirgio
 * Date: 2/16/2019
 * Time: 11:24 AM
 */
namespace HeadHunter;

use HeadHunter\Loader;
use pocketmine\event\Listener;
use onebone\economyapi\EconomyAPI;
use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\TextFormat;

class HeadHuntingEvent implements Listener
{
    public $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onPlace(BlockPlaceEvent $event){
        //cancels head being placed...
        if($event->getItem()->getId() == 397){
            $event->setCancelled(true);
        }
    }

    public function onDeath(PlayerDeathEvent $event){
        $name = $event->getPlayer()->getName();
        $percentage = (int) $this->plugin->config->get("percentage");
        $head = Item::get(Item::SKULL, 3);
        $nametag = $head->getNamedTag();
        $nametag->setString("PlayerHead", "$name");
        $head->setNamedTag($nametag);
        $head->setCustomName(TextFormat::AQUA . $name . TextFormat::GRAY . " Head");
        $head->setLore([
            "\m" . TextFormat::AQUA . " * " . TextFormat::GRAY . "Click to redeem " . $percentage . " percent",
            TextFormat::AQUA . " * " . TextFormat::GRAY . "of" . $name . " balance"
        ]);
        $drops = $event->getDrops();
        array_push($drops, $head);
        $event->setDrops($drops);
    }

    public function onTouch(PlayerInteractEvent $event){
        $item = $event->getItem();
        $block = $event->getBlock();
        $player = $event->getPlayer();

        if($item->getId() == 397 && $block->getId() != 199){ //prevents duping from item frames
            $nametag = $item->getNamedTag();
            if($nametag->hasTag("PlayerHead", StringTag::class)){ //checks for tag
                if($nametag->getString("PlayerHead") != $player->getName()){ // prevents player from selling their own head
                    $head = $nametag->getString("PlayerHead");
                    $headmoney = EconomyAPI::getInstance()->myMoney($head);
                    $percentage = (int) $this->plugin->config->get("percentage");
                    $moneystolen = $headmoney / $percentage;
                    $player->sendMessage($this->plugin->config->get("sell-head"));
                    EconomyAPI::getInstance()->addMoney($player, $moneystolen);
                    EconomyAPI::getInstance()->reduceMoney($head, $moneystolen);
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                } else {
                    $player->sendMessage($this->plugin->config->get("own-head"));
                }
            }
        }
    }
}
