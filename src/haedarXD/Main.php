<?php

namespace haedarXD;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use Persian\PersianManager;

class Main extends PluginBase implements Listener {

    private Config $data;
    private array $freeze = [];
    private array $online = [];
    private array $loginTimeout = [];

    public function onEnable() : void{
        @mkdir($this->getDataFolder());
        $this->data = new Config($this->getDataFolder() . "players.json", Config::JSON);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $e) : void{
        $p = $e->getPlayer();
        $name = strtolower($p->getName());
        $uuid = $p->getUniqueId()->toString();
        $this->freeze[$name] = true;

        if($this->data->exists($name)){
            $acc = $this->data->get($name);
            if($acc["uuid"] !== $uuid){
                $p->kick("§cThis account does not belong to you");
                return;
            }
            if(isset($this->online[$name])){
                $p->kick("§cThis account is already logged in");
                return;
            }
            $this->openLoginMenu($p);
            $this->startLoginTimeout($p);
        } else {
            $this->registerForm($p);
            $this->startLoginTimeout($p);
        }
    }

    public function onBreak(BlockBreakEvent $e) : void{
        $p = $e->getPlayer();
        if(isset($this->freeze[strtolower($p->getName())])) $e->cancel();
    }

    public function onPlace(BlockPlaceEvent $e) : void{
        $p = $e->getPlayer();
        if(isset($this->freeze[strtolower($p->getName())])) $e->cancel();
    }

    public function onQuit(PlayerQuitEvent $e) : void{
        $name = strtolower($e->getPlayer()->getName());
        unset($this->freeze[$name], $this->online[$name], $this->loginTimeout[$name]);
    }

    public function onCmd(CommandEvent $e) : void{
        $p = $e->getSender();
        if(!$p instanceof Player) return;
        if(isset($this->freeze[strtolower($p->getName())])) $e->cancel();
    }

    public function onMove(PlayerMoveEvent $e) : void{
        $p = $e->getPlayer();
        if(isset($this->freeze[strtolower($p->getName())])) $e->cancel();
    }

    public function onDamage(EntityDamageEvent $e) : void{
        $ent = $e->getEntity();
        if($ent instanceof Player && isset($this->freeze[strtolower($ent->getName())])) $e->cancel();
    }

    public function onPickup(EntityItemPickupEvent $e) : void{
        $ent = $e->getEntity();
        if($ent instanceof Player && isset($this->freeze[strtolower($ent->getName())])) $e->cancel();
    }

    public function onInv(InventoryTransactionEvent $e) : void{
        $p = $e->getTransaction()->getSource();
        if(!$p instanceof Player) return;
        if(isset($this->freeze[strtolower($p->getName())])) $e->cancel();
    }

    private function openLoginMenu(Player $p) : void{
        $form = new SimpleForm(function(Player $player, $data){
            if($data === null){ $this->openLoginMenu($player); return; }
            switch($data){
                case 0: $this->loginForm($player); break;
                case 1: $this->changePasswordForm($player); break;
            }
        });
        $form->setTitle("§lLogin");
        $form->setContent("Select an option:");
        $form->addButton("§aLogin", 0, "textures/ui/icon_import");
        $form->addButton("§eChange Password", 0, "textures/ui/icon_setting");
        $p->sendForm($form);
    }

    private function loginForm(Player $p) : void{
        $form = new CustomForm(function(Player $player, ?array $data){
            if($data === null){ $this->openLoginMenu($player); return; }
            $name = strtolower($player->getName());
            if($this->data->get($name)["password"] !== $data[0]){
                $player->sendMessage("§cIncorrect password");
                $this->loginForm($player);
                return;
            }
            $player->sendMessage("§aLogin successful");
            $this->authSuccess($player);
        });
        $form->setTitle("§lLogin");
        $form->addInput("Enter your password");
        $p->sendForm($form);
    }

    private function changePasswordForm(Player $p) : void{
        $form = new CustomForm(function(Player $player, ?array $data){
            if($data === null){ $player->kick("§cForm should not be closed"); return; }
            $old = trim((string)$data[0]);
            $new = trim((string)$data[1]);
            $repeat = trim((string)$data[2]);
            $name = strtolower($player->getName());
            $acc = $this->data->get($name);

            if($old === ""){ $player->kick("§cOld password cannot be empty"); return; }
            if($acc["password"] !== $old){ $player->kick("§cOld password is incorrect"); return; }
            if($new === ""){ $player->kick("§cNew password cannot be empty"); return; }
            if($repeat === ""){ $player->kick("§cRepeat password cannot be empty"); return; }
            if($new !== $repeat){ $player->kick("§cPasswords do not match"); return; }

            $acc["password"] = $new;
            $this->data->set($name, $acc);
            $this->data->save();
            $player->sendMessage("§aPassword changed successfully");
            $this->openLoginMenu($player);
        });
        $form->setTitle("§lChange Password");
        $form->addInput("Old password");
        $form->addInput("New password");
        $form->addInput("Repeat new password");
        $p->sendForm($form);
    }

    private function registerForm(Player $p) : void{
        $form = new CustomForm(function(Player $player, ?array $data){
            if($data === null){ $this->registerForm($player); return; }
            if($data[0] !== $data[1]){
                $player->sendMessage("§cPasswords do not match");
                $this->registerForm($player);
                return;
            }
            $name = strtolower($player->getName());
            $this->data->set($name, [
                "password" => $data[0],
                "uuid" => $player->getUniqueId()->toString()
            ]);
            $this->data->save();
            $player->sendMessage("§aRegistration successful");
            $this->authSuccess($player);
        });
        $form->setTitle("§lRegister");
        $form->addInput("Password");
        $form->addInput("Repeat password");
        $p->sendForm($form);
    }

    private function authSuccess(Player $p) : void{
        $name = strtolower($p->getName());
        $this->online[$name] = true;
        unset($this->loginTimeout[$name]);
        $p->sendMessage("§eYou will be unfrozen in 3 seconds");

        $this->getScheduler()->scheduleDelayedTask(
            new ClosureTask(function() use ($p, $name){
                if(isset($this->freeze[$name])){
                    unset($this->freeze[$name]);
                    if($p->isOnline()){
                        $p->sendMessage("§aYou can now play!");
                    }
                }
            }),
            60
        );
    }

    private function startLoginTimeout(Player $p) : void{
        $name = strtolower($p->getName());
        if(isset($this->loginTimeout[$name])) $this->loginTimeout[$name]->cancel();

        $handler = $this->getScheduler()->scheduleDelayedTask(
            new ClosureTask(function() use ($p, $name){
                if($p->isOnline() && isset($this->freeze[$name])){
                    $p->kick("§cLogin time expired");
                }
                unset($this->loginTimeout[$name]);
            }),
            20 * 60
        );
        $this->loginTimeout[$name] = $handler;
    }
}
