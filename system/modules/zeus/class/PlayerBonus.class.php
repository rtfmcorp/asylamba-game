<?php

/**
 * Player Bonus
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Zeus
 * @update 18.07.13
*/

include_once PROMETHEE;

class PlayerBonus {
	// ATTRIBUTES
	public $rPlayer;
	private $synchronized = FALSE;
	private $technology;
	public $bonus;

	// CONSTANTS 
	const BONUS_QUANTITY = 28;
	// 28 bonus de technos
	const GENERATOR_SPEED = 0;
	const REFINERY_REFINING = 1;
	const REFINERY_STORAGE = 2;
	const DOCK1_SPEED = 3;
	const DOCK2_SPEED = 4;
	const TECHNOSPHERE_SPEED = 5;
	const COMMERCIAL_INCOME = 6;
	const GRAVIT_MODULE = 7;
	const DOCK3_SPEED = 8;
	const POPULATION_TAX = 9;
	const COMMANDER_INVEST = 10;
	const UNI_INVEST = 11;
	const ANTISPY_INVEST = 12;
	const SHIP_SPEED = 13;
	const SHIP_CONTAINER = 14;
	const BASE_QUANTITY = 15;
	const FIGHTER_SPEED = 16;
	const FIGHTER_ATTACK = 17;
	const FIGHTER_DEFENSE = 18;
	const CORVETTE_SPEED = 19;
	const CORVETTE_ATTACK = 20;
	const CORVETTE_DEFENSE = 21;
	const FRIGATE_SPEED = 22;
	const FRIGATE_ATTACK = 23;
	const FRIGATE_DEFENSE = 24;
	const DESTROYER_SPEED = 25;
	const DESTROYER_ATTACK = 26;
	const DESTROYER_DEFENSE = 27;

	public function __construct($rPlayer) {
		$this->rPlayer = $rPlayer;
		if ($rPlayer == CTR::$data->get('playerId')) {
			$this->synchronized = TRUE;
		}
		$this->bonus = new StackList();
	}

	public function initialize() {		// à faire à la connexion seulement
		# remplissage des bonus avec les technologies
		$this->technology = new Technology($this->rPlayer);
		$this->fillFromTechnology();

		# remplissage des bonus avec les cartes
		// ...

		if ($this->synchronized) {
			for ($i = 0; $i < self::BONUS_QUANTITY; $i++) { 
				CTR::$data->get('playerBonus')->add($i, $this->bonus->get($i));
			}
		}
	}

	public function load() {
		$this->technology = new Technology($this->rPlayer);
		if ($this->synchronized) {		// chargement de l'objet avec le contrôleur
			for ($i = 0; $i < self::BONUS_QUANTITY; $i++) { 
				$this->bonus->add($i, CTR::$data->get('playerBonus')->get($i));
			}
		} else {						// remplissage de l'objet normalement
			# remplissage avec les technologies
			$this->fillFromTechnology();

			# remplissage avec les cartes
			// ...
		}
	}

	private function fillFromTechnology() {
		$this->addTechnoToBonus(Technology::GENERATOR_SPEED, self::GENERATOR_SPEED);
		$this->addTechnoToBonus(Technology::REFINERY_REFINING, self::REFINERY_REFINING);
		$this->addTechnoToBonus(Technology::REFINERY_STORAGE, self::REFINERY_STORAGE);
		$this->addTechnoToBonus(Technology::DOCK1_SPEED, self::DOCK1_SPEED);
		$this->addTechnoToBonus(Technology::DOCK2_SPEED, self::DOCK2_SPEED);
		$this->addTechnoToBonus(Technology::TECHNOSPHERE_SPEED, self::TECHNOSPHERE_SPEED);
		$this->addTechnoToBonus(Technology::COMMERCIAL_INCOME, self::COMMERCIAL_INCOME);
		$this->addTechnoToBonus(Technology::GRAVIT_MODULE, self::GRAVIT_MODULE);
		$this->addTechnoToBonus(Technology::DOCK3_SPEED, self::DOCK3_SPEED);
		$this->addTechnoToBonus(Technology::POPULATION_TAX, self::POPULATION_TAX);
		$this->addTechnoToBonus(Technology::COMMANDER_INVEST, self::COMMANDER_INVEST);
		$this->addTechnoToBonus(Technology::UNI_INVEST, self::UNI_INVEST);
		$this->addTechnoToBonus(Technology::ANTISPY_INVEST, self::ANTISPY_INVEST);
		$this->addTechnoToBonus(Technology::SPACESHIPS_SPEED, self::SHIP_SPEED);
		$this->addTechnoToBonus(Technology::SPACESHIPS_CONTAINER, self::SHIP_CONTAINER);
		$this->addTechnoToBonus(Technology::BASE_QUANTITY, self::BASE_QUANTITY);
		$this->addTechnoToBonus(Technology::FIGHTER_SPEED, self::FIGHTER_SPEED);
		$this->addTechnoToBonus(Technology::FIGHTER_ATTACK, self::FIGHTER_ATTACK);
		$this->addTechnoToBonus(Technology::FIGHTER_DEFENSE, self::FIGHTER_DEFENSE);
		$this->addTechnoToBonus(Technology::CORVETTE_SPEED, self::CORVETTE_SPEED);
		$this->addTechnoToBonus(Technology::CORVETTE_ATTACK, self::CORVETTE_ATTACK);
		$this->addTechnoToBonus(Technology::CORVETTE_DEFENSE, self::CORVETTE_DEFENSE);
		$this->addTechnoToBonus(Technology::FRIGATE_SPEED, self::FRIGATE_SPEED);
		$this->addTechnoToBonus(Technology::FRIGATE_ATTACK, self::FRIGATE_ATTACK);
		$this->addTechnoToBonus(Technology::FRIGATE_DEFENSE, self::FRIGATE_DEFENSE);
		$this->addTechnoToBonus(Technology::DESTROYER_SPEED, self::DESTROYER_SPEED);
		$this->addTechnoToBonus(Technology::DESTROYER_ATTACK, self::DESTROYER_ATTACK);
		$this->addTechnoToBonus(Technology::DESTROYER_DEFENSE, self::DESTROYER_DEFENSE);
	}

	private function addTechnoToBonus($techno, $bonus) {
		$this->bonus->add($bonus, $this->technology->getTechnology($techno) * TechnologyResource::getInfo($techno, 'bonus'));
	}

	public function increment($bonusId, $increment) {
		if ($bonusId >= 0 && $bonusId < self::BONUS_QUANTITY) {
			if ($increment > 0) {
				$this->bonus->add($bonusId, $this->bonus->get($bonusId) + $increment);
				if ($this->synchronized) {
					CTR::$data->get('playerBonus')->add($bonusId, $this->bonus->get($bonusId));
				}
			} else {
				CTR::$alert->add('incrémentation de bonus impossible - l\'incrément doit être positif', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('incrémentation de bonus impossible - bonus invalide', ALERT_STD_ERROR);
		}
	}

	public function decrement($bonusId, $decrement) {
		if ($bonusId >= 0 && $bonusId < self::BONUS_QUANTITY) {
			if ($increment > 0) {
				if ($increment <= $this->bonus->get($bonusId)) {
					$this->bonus->add($bonusId, $this->bonus->get($bonusId) - $decrement);
					if ($this->synchronized) {
						CTR::$data->get('playerBonus')->add($bonusId, $this->bonus->get($bonusId));
					}
				} else {
					CTR::$alert->add('décrémentation de bonus impossible - le décrément est plus grand que le bonus', ALERT_STD_ERROR);
				}
			} else {
				CTR::$alert->add('décrémentation de bonus impossible - le décrément doit être positif', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('décrémentation de bonus impossible - bonus invalide', ALERT_STD_ERROR);
		}
	}

	public function updateTechnoBonus($techno, $level) {
		switch ($techno) {
			case Technology::GENERATOR_SPEED: $bonusId = self::GENERATOR_SPEED; break;
			case Technology::REFINERY_REFINING: $bonusId = self::REFINERY_REFINING; break;
			case Technology::REFINERY_STORAGE: $bonusId = self::REFINERY_STORAGE; break;
			case Technology::DOCK1_SPEED: $bonusId = self::DOCK1_SPEED; break;
			case Technology::DOCK2_SPEED: $bonusId = self::DOCK2_SPEED; break;
			case Technology::TECHNOSPHERE_SPEED: $bonusId = self::TECHNOSPHERE_SPEED; break;
			case Technology::COMMERCIAL_INCOME: $bonusId = self::COMMERCIAL_INCOME; break;
			case Technology::GRAVIT_MODULE: $bonusId = self::GRAVIT_MODULE; break;
			case Technology::DOCK3_SPEED: $bonusId = self::DOCK3_SPEED; break;
			case Technology::POPULATION_TAX: $bonusId = self::POPULATION_TAX; break;
			case Technology::COMMANDER_INVEST: $bonusId = self::COMMANDER_INVEST; break;
			case Technology::UNI_INVEST: $bonusId = self::UNI_INVEST; break;
			case Technology::ANTISPY_INVEST: $bonusId = self::ANTISPY_INVEST; break;
			case Technology::SPACESHIPS_SPEED: $bonusId = self::SHIP_SPEED; break;
			case Technology::SPACESHIPS_CONTAINER: $bonusId = self::SHIP_CONTAINER; break;
			case Technology::BASE_QUANTITY: $bonusId = self::BASE_QUANTITY; break;
			case Technology::FIGHTER_SPEED: $bonusId = self::FIGHTER_SPEED; break;
			case Technology::FIGHTER_ATTACK: $bonusId = self::FIGHTER_ATTACK; break;
			case Technology::FIGHTER_DEFENSE: $bonusId = self::FIGHTER_DEFENSE; break;
			case Technology::CORVETTE_SPEED: $bonusId = self::CORVETTE_SPEED; break;
			case Technology::CORVETTE_ATTACK: $bonusId = self::CORVETTE_ATTACK; break;
			case Technology::CORVETTE_DEFENSE: $bonusId = self::CORVETTE_DEFENSE; break;
			case Technology::FRIGATE_SPEED: $bonusId = self::FRIGATE_SPEED; break;
			case Technology::FRIGATE_ATTACK: $bonusId = self::FRIGATE_ATTACK; break;
			case Technology::FRIGATE_DEFENSE: $bonusId = self::FRIGATE_DEFENSE; break;
			case Technology::DESTROYER_SPEED: $bonusId = self::DESTROYER_SPEED; break;
			case Technology::DESTROYER_ATTACK: $bonusId = self::DESTROYER_ATTACK; break;
			case Technology::DESTROYER_DEFENSE: $bonusId = self::DESTROYER_DEFENSE; break;
			default:
				CTR::$alert->add('mise à jour du bonus de technologie impossible - technologie invalide', ALERT_STD_ERROR);
				break;
		}
		$this->addTechnoToBonus($techno, $bonusId);

		if ($this->synchronized) {
			CTR::$data->get('playerBonus')->add($bonusId, $this->technology->getTechnology($techno) * TechnologyResource::getInfo($techno, 'bonus'));
		}
		CTR::$alert->add('Mise à jour des technos OK', ALERT_BUG_SUCCESS);
	}
}
?>
