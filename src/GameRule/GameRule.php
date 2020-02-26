<?php

/*
 *       _       _        ___ _____ _  ___
 *   __ _| |_   _(_)_ __  / _ \___ // |/ _ \
 * / _` | \ \ / / | '_ \| | | ||_ \| | (_) |
 * | (_| | |\ V /| | | | | |_| |__) | |\__, |
 *  \__,_|_| \_/ |_|_| |_|\___/____/|_|  /_/
 *
 * Copyright (C) 2019 alvin0319
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);
namespace GameRule;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class GameRule extends PluginBase implements Listener{

	/** @var Config */
	protected $config;

	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->config = new Config($this->getDataFolder() . "GameRulesData.yml", Config::YAML, [
			"showcoordinates" => true,
			"doimmedaterespawn" => true,
			"keepinventory" => false,
			"naturalRegeneration" => true,
			"language" => "en",
			"lang" => [
				"ko" => [
					"message-set-value" => "게임 규칙 {%0}이(가) {%1}로 업데이트 되었습니다.",
					"message-not-boolen" => "게임 규칙 {%0}은 오직 논리값만 허용합니다.",
					"message-list" => "사용법: /gamerule <gameRule> <value>\n사용 가능한 규칙: showcoordinates, doimmedaterespawn, keepinventory, naturalRegeneration"
				],
				"en" => [
					"message-set-value" => "GameRule {%0} was updated to {%1}.",
					"message-not-boolen" => "GameRule {%0} is only accept boolen.",
					"message-list" => "Usage: /gamerule <gameRule> <value>\nCan use GameRules: showcoordinates, doimmedaterespawn, keepinventory, naturalRegeneration"
				]
			]
		]);

	}

	public function onDisable(){
		$this->config->save();
	}

	public function handlePlayerJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		$this->updateGameRules($player);
	}

	public function updateGameRules(Player $player){
		$pk = new GameRulesChangedPacket();
		$pk->gameRules["showcoordinates"] = [1, (bool) $this->config->getNested("showcoordinates", true)];
		$pk->gameRules["doimmediaterespawn"] = [1, (bool) $this->config->getNested("doimmedaterespawn", true)];
		$player->sendDataPacket($pk);
	}

	public function handleRegeneration(EntityRegainHealthEvent $event){
		$entity = $event->getEntity();
		if($entity instanceof Player){
			if(!$this->config->getNested("naturalRegeneration", true)){
				$event->setCancelled(true);
			}
		}
	}

	public function handleDeath(PlayerDeathEvent $event){
		if($this->config->getNested("keepinventory", true)){
			$event->setKeepInventory(true);
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($args[0] ?? "x"){
			case "showcoordinates":
				if(!isset($args[1])){
					$sender->sendMessage("/gamerule showcoordinates [true/false]");
					break;
				}

				switch($args[1]){
					case "true":
						$this->config->setNested("showcoordinates", true);
						$sender->sendMessage($this->translateString("message-set-value", ["showcoordinates", "true"]));
						break;
					case "false":
						$this->config->setNested("showcoordinates", false);
						$sender->sendMessage($this->translateString("message-set-value", ["showcoordinates", "false"]));
						break;
					default:
						$sender->sendMessage("/gamerule showcoordinates [true/false]");
				}
				break;
			case "keepinventory":
				if(!isset($args[1])){
					$sender->sendMessage("/gamerule keepinventory [true/false]");
					break;
				}

				switch($args[1]){
					case "true":
						$this->config->setNested("keepinventory", true);
						$sender->sendMessage($this->translateString("message-set-value", ["keepinventory", "true"]));
						break;
					case "false":
						$this->config->setNested("keepinventory", false);
						$sender->sendMessage($this->translateString("message-set-value", ["keepinventory", "false"]));
						break;
					default:
						$sender->sendMessage("/gamerule keepinventory [true/false]");
				}
				break;
			case "doimmedaterespawn":
				if(!isset($args[1])){
					$sender->sendMessage("/gamerule doimmedaterespawn [true/false]");
					break;
				}

				switch($args[1]){
					case "true":
						$this->config->setNested("doimmedaterespawn", true);
						$sender->sendMessage($this->translateString("message-set-value", ["doimmedaterespawn", "true"]));
						break;
					case "false":
						$this->config->setNested("doimmedaterespawn", false);
						$sender->sendMessage($this->translateString("message-set-value", ["doimmedaterespawn", "false"]));
						break;
					default:
						$sender->sendMessage("/gamerule doimmedaterespawn [true/false]");
				}
				break;
			case "naturalRegeneration":
				if(!isset($args[1])){
					$sender->sendMessage("/gamerule naturalRegeneration [true/false]");
					break;
				}

				switch($args[1]){
					case "true":
						$this->config->setNested("doimmedaterespawn", true);
						$sender->sendMessage($this->translateString("message-set-value", ["naturalRegeneration", "true"]));
						break;
					case "false":
						$this->config->setNested("doimmedaterespawn", false);
						$sender->sendMessage($this->translateString("message-set-value", ["naturalRegeneration", "false"]));
						break;
					default:
						$sender->sendMessage("/gamerule naturalRegeneration [true/false]");
				}
				break;

			default:
				$sender->sendMessage($this->translateString("message-list"));
		}

		foreach($this->getServer()->getOnlinePlayers() as $player){
			$this->updateGameRules($player); // need to update gamerules to player
		}

		return true;
	}

	public function translateString(string $str, array $optional = []) : string{
		$lang = $this->config->getNested("language", "ko");
		$string = $this->config->getAll()["lang"] [$lang] [$str] ?? "";
		foreach($optional as $i => $value){
			$string = str_replace("{%{$i}}", $value, $string);
		}
		return $string;
	}
}