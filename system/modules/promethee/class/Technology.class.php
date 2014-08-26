<?php

/**
 * Technology
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 04.06.13
*/

class Technology {
	// ATTRIBUTES
	public $rPlayer;

	// technologies de débloquage (0 = bloqué, 1 = débloqué)
	public $comPlatUnblock = 0;
	public $dock2Unblock = 0;
	public $dock3Unblock = 0;
	public $gravitModuleUnblock = 0;

	public $ship0Unblock = 0;
	public $ship1Unblock = 0;
	public $ship2Unblock = 0;
	public $ship3Unblock = 0;
	public $ship4Unblock = 0;
	public $ship5Unblock = 0;
	public $ship6Unblock = 0;
	public $ship7Unblock = 0;
	public $ship8Unblock = 0;
	public $ship9Unblock = 0;
	public $ship10Unblock = 0;
	public $ship11Unblock = 0;

	public $colonization = 0;
	public $conquest = 0;

	// technologies à niveau
	public $generatorSpeed = 0;
	public $refineryRefining = 0;
	public $refineryStorage = 0;
	public $dock1Speed = 0;
	public $dock2Speed = 0;
	public $technosphereSpeed = 0;
	public $commercialIncomeUp = 0;
	public $gravitModuleUp = 0;
	public $dock3Speed = 0;

	public $populationTaxUp = 0;
	public $commanderInvestUp = 0;
	public $uniInvestUp = 0;
	public $antiSpyInvestUp = 0;

	public $spaceShipsSpeed = 0;
	public $spaceShipsContainer = 0; // soute

	public $baseQuantity = 0;

	public $fighterSpeed = 0;
	public $fighterAttack = 0;
	public $fighterDefense = 0;
	public $corvetteSpeed = 0;
	public $corvetteAttack = 0;
	public $corvetteDefense = 0;
	public $frigateSpeed = 0;
	public $frigateAttack = 0;
	public $frigateDefense = 0;
	public $destroyerSpeed = 0;
	public $destroyerAttack = 0;
	public $destroyerDefense = 0;

	// CONSTANTS
	const COM_PLAT_UNBLOCK = 0;
	const DOCK2_UNBLOCK = 1;
	const DOCK3_UNBLOCK = 2;			# inactif
	const GRAVIT_MODULE_UNBLOCK = 3;	# inactif
	const SHIP0_UNBLOCK = 4;	// pegase
	const SHIP1_UNBLOCK = 5;	// satyre
	const SHIP2_UNBLOCK = 6;	// chimere
	const SHIP3_UNBLOCK = 7;	// sirene
	const SHIP4_UNBLOCK = 8;	// dryade
	const SHIP5_UNBLOCK = 9;	// meduse
	const SHIP6_UNBLOCK = 10;	// griffon
	const SHIP7_UNBLOCK = 11;	// cyclope
	const SHIP8_UNBLOCK = 12;	// minotaure
	const SHIP9_UNBLOCK = 13;	// hydre
	const SHIP10_UNBLOCK = 14;	// cerbere
	const SHIP11_UNBLOCK = 15;	// phenix
	const COLONIZATION = 16;
	const CONQUEST = 17;
	const GENERATOR_SPEED = 18;			# ok
	const REFINERY_REFINING = 19;		# ok
	const REFINERY_STORAGE = 20;		# ok
	const DOCK1_SPEED = 21;				# ok
	const DOCK2_SPEED = 22;				# ok
	const TECHNOSPHERE_SPEED = 23;		# ok
	const COMMERCIAL_INCOME = 24;		# ok
	const GRAVIT_MODULE = 25;			# inactif
	const DOCK3_SPEED = 26;				# inactif
	const POPULATION_TAX = 27;			# ok
	const COMMANDER_INVEST = 28;		# ok
	const UNI_INVEST = 29;				# ok
	const ANTISPY_INVEST = 30;
	const SPACESHIPS_SPEED = 31;
	const SPACESHIPS_CONTAINER = 32;
	const BASE_QUANTITY = 33;
	const FIGHTER_SPEED = 34;
	const FIGHTER_ATTACK = 35;
	const FIGHTER_DEFENSE = 36;
	const CORVETTE_SPEED = 37;
	const CORVETTE_ATTACK = 38;
	const CORVETTE_DEFENSE = 39;
	const FRIGATE_SPEED = 40;
	const FRIGATE_ATTACK = 41;
	const FRIGATE_DEFENSE = 42;
	const DESTROYER_SPEED = 43;
	const DESTROYER_ATTACK = 44;
	const DESTROYER_DEFENSE = 45;

	public function __construct($rPlayer) {
		$this->rPlayer = $rPlayer; 

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
			FROM technology
			WHERE rPlayer = ?');
		$qr->execute(array($rPlayer));
		while($aw = $qr->fetch()) {
			$this->setTechnology($aw['technology'], $aw['level'], TRUE);
		}
	}

	public function getTechnology($id) {
		if (TechnologyResource::isATechnology($id)) {
			switch ($id) {
				case 0 : return $this->comPlatUnblock; break;
				case 1 : return $this->dock2Unblock; break;
				case 2 : return $this->dock3Unblock; break;
				case 3 : return $this->gravitModuleUnblock; break;
				case 4 : return $this->ship0Unblock; break;
				case 5 : return $this->ship1Unblock; break;
				case 6 : return $this->ship2Unblock; break;
				case 7 : return $this->ship3Unblock; break;
				case 8 : return $this->ship4Unblock; break;
				case 9 : return $this->ship5Unblock; break;
				case 10 : return $this->ship6Unblock; break;
				case 11 : return $this->ship7Unblock; break;
				case 12 : return $this->ship8Unblock; break;
				case 13 : return $this->ship9Unblock; break;
				case 14 : return $this->ship10Unblock; break;
				case 15 : return $this->ship11Unblock; break;
				case 16 : return $this->colonization; break;
				case 17 : return $this->conquest; break;
				case 18 : return $this->generatorSpeed; break;
				case 19 : return $this->refineryRefining; break;
				case 20 : return $this->refineryStorage; break;
				case 21 : return $this->dock1Speed; break;
				case 22 : return $this->dock2Speed; break;
				case 23 : return $this->technosphereSpeed; break;
				case 24 : return $this->commercialIncomeUp; break;
				case 25 : return $this->gravitModuleUp; break;
				case 26 : return $this->dock3Speed; break;
				case 27 : return $this->populationTaxUp; break;
				case 28 : return $this->commanderInvestUp; break;
				case 29 : return $this->uniInvestUp; break;
				case 30 : return $this->antiSpyInvestUp; break;
				case 31 : return $this->spaceShipsSpeed; break;
				case 32 : return $this->spaceShipsContainer; break;
				case 33 : return $this->baseQuantity; break;
				case 34 : return $this->fighterSpeed; break;
				case 35 : return $this->fighterAttack; break;
				case 36 : return $this->fighterDefense; break;
				case 37 : return $this->corvetteSpeed; break;
				case 38 : return $this->corvetteAttack; break;
				case 39 : return $this->corvetteDefense; break;
				case 40 : return $this->frigateSpeed; break;
				case 41 : return $this->frigateAttack; break;
				case 42 : return $this->frigateDefense; break;
				case 43 : return $this->destroyerSpeed; break;
				case 44 : return $this->destroyerAttack; break;
				case 45 : return $this->destroyerDefense; break;
				default : return FALSE;
			}
		}
		return FALSE;
	}

	public function setTechnology($id, $value, $load = FALSE) { // ajouter une entrée bdd ou modifier ligne !!!
		if (TechnologyResource::isATechnology($id)) {
			switch ($id) {
				case 0 : $this->comPlatUnblock = $value; break;
				case 1 : $this->dock2Unblock = $value; break;
				case 2 : $this->dock3Unblock = $value; break;
				case 3 : $this->gravitModuleUnblock = $value; break;
				case 4 : $this->ship0Unblock = $value; break;
				case 5 : $this->ship1Unblock = $value; break;
				case 6 : $this->ship2Unblock = $value; break;
				case 7 : $this->ship3Unblock = $value; break;
				case 8 : $this->ship4Unblock = $value; break;
				case 9 : $this->ship5Unblock = $value; break;
				case 10 : $this->ship6Unblock = $value; break;
				case 11 : $this->ship7Unblock = $value; break;
				case 12 : $this->ship8Unblock = $value; break;
				case 13 : $this->ship9Unblock = $value; break;
				case 14 : $this->ship10Unblock = $value; break;
				case 15 : $this->ship11Unblock = $value; break;
				case 16 : $this->colonization = $value; break;
				case 17 : $this->conquest = $value; break;
				case 18 : $this->generatorSpeed = $value; break;
				case 19 : $this->refineryRefining = $value; break;
				case 20 : $this->refineryStorage = $value; break;
				case 21 : $this->dock1Speed = $value; break;
				case 22 : $this->dock2Speed = $value; break;
				case 23 : $this->technosphereSpeed = $value; break;
				case 24 : $this->commercialIncomeUp = $value; break;
				case 25 : $this->gravitModuleUp = $value; break;
				case 26 : $this->dock3Speed = $value; break;
				case 27 : $this->populationTaxUp = $value; break;
				case 28 : $this->commanderInvestUp = $value; break;
				case 29 : $this->uniInvestUp = $value; break;
				case 30 : $this->antiSpyInvestUp = $value; break;
				case 31 : $this->spaceShipsSpeed = $value; break;
				case 32 : $this->spaceShipsContainer = $value; break;
				case 33 : $this->baseQuantity = $value; break;
				case 34 : $this->fighterSpeed = $value; break;
				case 35 : $this->fighterAttack = $value; break;
				case 36 : $this->fighterDefense = $value; break;
				case 37 : $this->corvetteSpeed = $value; break;
				case 38 : $this->corvetteAttack = $value; break;
				case 39 : $this->corvetteDefense = $value; break;
				case 40 : $this->frigateSpeed = $value; break;
				case 41 : $this->frigateAttack = $value; break;
				case 42 : $this->frigateDefense = $value; break;
				case 43 : $this->destroyerSpeed = $value; break;
				case 44 : $this->destroyerAttack = $value; break;
				case 45 : $this->destroyerDefense = $value; break;
				default : return FALSE;
			}
			if ($load == TRUE) {
				return TRUE;
			} else {
				if ($value < 1) {
					Technology::deleteByRPlayer($this->rPlayer, $id);
				} else {
					if ($value == 1) {
						Technology::addTech($this->rPlayer, $id, $value);
					} else {
						$this->updateTech($id, $value);
					}
					if (!TechnologyResource::isAnUnblockingTechnology($id)) {
						$bonus = new PlayerBonus($this->rPlayer);
						$bonus->load();
						$bonus->updateTechnoBonus($id, $value);
					}
				}
				return TRUE;
			}
		}
		return FALSE;
	}

	public static function addTech($rPlayer, $technology, $level) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			technology(rPlayer, technology, level)
			VALUES(?, ?, ?)');
		$qr->execute(array(
			$rPlayer,
			$technology,
			$level
		));
	}

	public function updateTech($technology, $level) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('UPDATE technology
			SET	level = ?
			WHERE rPlayer = ? AND technology = ?');
		$qr->execute(array(
			$level,
			$this->rPlayer,
			$technology
		));
	}

	public function delete($techno) {
		$this->setTechnology($techno, 0);
	}

	public static function deleteByRPlayer($rPlayer, $techno) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM technology WHERE rPlayer = ? and technology = ?');
		$qr->execute(array($rPlayer, $techno));
		return TRUE;
	}
}
?>