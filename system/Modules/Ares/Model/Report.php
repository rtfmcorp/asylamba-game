<?php

/**
 * Report
 *
 * @author Noé Zufferey
 * @copyright Asylamba - le jeu
 *
 * @package Arès
 * @update 01.06.14
*/

#TODO 
/*
avatar
name
level
exp avant le combat
nbr victoires
*/

namespace Asylamba\Modules\Ares\Model;

use Asylamba\Modules\Athena\Resource\ShipResource;

class Report {
	const STANDARD = 0;
	const ARCHIVED = 1;
	const DELETED = 2;

	const ILLEGAL = 0;
	const LEGAL = 1;
	
	public $id					= 0;
	public $rPlayerAttacker		= 0;
	public $rPlayerDefender		= 0;
	public $rPlayerWinner		= 0;
	public $avatarA				= '';
	public $avatarD				= '';
	public $nameA				= '';
	public $nameD				= '';
	public $levelA				= 0;
	public $levelD				= 0;
	public $experienceA			= 0;
	public $experienceD			= 0;
	public $palmaresA			= 0;
	public $palmaresD			= 0;
	public $resources			= 0;
	public $expCom				= 0;
	public $expPlayerA			= 0;
	public $expPlayerD			= 0;
	public $rPlace				= 0;
	public $type				= 0;
	public $isLegal 			= 1;
	public $hasBeenPunished 	= 0;
	public $round				= 0;
	public $importance			= 0;
	public $pevInBeginA			= 0;
	public $pevInBeginD			= 0;
	public $pevAtEndA			= 0;
	public $pevAtEndD			= 0;
	public $statementAttacker	= 0;
	public $statementDefender	= 0;
	public $dFight				= '';
	public $placeName			= '';

	public $colorA				= 0;
	public $colorD				= 0;
	public $playerNameA			= '';
	public $playerNameD			= '';

	public $squadrons = array();

	public $armyInBeginA = array();
	public $armyInBeginD = array();
	public $armyAtEndA = array();
	public $armyAtEndD = array();

	public $fight = array();

	public $totalInBeginA = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $totalInBeginD = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $totalAtEndA = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $totalAtEndD = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $diferenceA = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $diferenceD = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

	private $setArmiesDone = FALSE;

	public function getId() { return $this->id; }

	public function getTypeOfReport($playerColor) {
		$place = '<a href="/' . 'map/place-' . $this->rPlace . '">' . $this->placeName . '</a>';

		if ($this->colorA == $playerColor) {
			if ($this->rPlayerWinner == $this->rPlayerAttacker) {
				if ($this->type == Commander::LOOT) {
					$title = 'Pillage de ' . $place;
					$img = 'loot.png';
				} else {
					$title = $this->rPlayerDefender == 0
						? 'Colonisation réussie'
						: 'Conquête de ' . $place;
					$img = 'colo.png';
				}
			} else {
				if ($this->type == Commander::LOOT) {
					$title = 'Pillage raté de ' . $place;
					$img = 'loot.png';
				} else {
					$title = $this->rPlayerDefender == 0
						? 'Colonisation ratée'
						: 'Conquête ratée de ' . $place;
					$img = 'colo.png';
				}
			}
		} else {
			if ($this->rPlayerWinner == $this->rPlayerDefender) {
				$title = $this->type == Commander::LOOT
					? 'Pillage repoussé'
					: 'Conquête repoussée';
				$img = 'shield.png';
			} else {
				$title = $this->type == Commander::LOOT
					? 'Défense ratée lors d\'un pillage'
					: 'Défense ratée lors d\'une conquête';
				$img = 'shield.png';
			}
		}

		return [$title, $img];
	}

	public function setPev() {
		for ($i = 0; $i < 12; $i++) {
			$this->pevInBeginA += ShipResource::getInfo($i, 'pev') * $this->totalInBeginA[$i];
			$this->pevInBeginD += ShipResource::getInfo($i, 'pev') * $this->totalInBeginD[$i];
			$this->pevAtEndA += ShipResource::getInfo($i, 'pev') * $this->totalAtEndA[$i];
			$this->pevAtEndD += ShipResource::getInfo($i, 'pev') * $this->totalAtEndD[$i];
		}
	}

	public function setArmies() {
		if ($this->setArmiesDone == FALSE) {
			# squadron(id, pos, rReport, round, rCommander, ship0, ..., ship11)

			$rCommanderA = $this->squadrons[0][4];

			foreach ($this->squadrons AS $sq) {
				if ($sq[3] == 0) {
					if ($sq[4] == $rCommanderA) {
						$this->armyInBeginA[] = $sq;
					} else {
						$this->armyInBeginD[] = $sq;
					}
				} elseif ($sq[3] > 0) {
					$this->fight[] = $sq;
				} else {
					if ($sq[4] == $rCommanderA) {
						$this->armyAtEndA[] = $sq;
					} else {
						$this->armyAtEndD[] = $sq;
					}
				}
			}

			foreach ($this->armyInBeginA AS $sq) {
				for ($i = 5; $i <= 16; $i++) {
					$this->totalInBeginA[$i - 5] += $sq[$i];
				}
			}
			foreach ($this->armyInBeginD AS $sq) {
				for ($i = 5; $i <= 16; $i++) {
					$this->totalInBeginD[$i - 5] += $sq[$i];
				}
			}
			foreach ($this->armyAtEndA AS $sq) {
				for ($i = 5; $i <= 16; $i++) {
					$this->totalAtEndA[$i - 5] += $sq[$i];
				}
			}
			foreach ($this->armyAtEndD AS $sq) {
				for ($i = 5; $i <= 16; $i++) {
					$this->totalAtEndD[$i - 5] += $sq[$i];
				}
			}

			for ($i = 0; $i < 12; $i++) {
				$this->diferenceA[$i] = $this->totalInBeginA[$i] - $this->totalAtEndA[$i];
			}
			for ($i = 0; $i < 12; $i++) {
				$this->diferenceD[$i] = $this->totalInBeginD[$i] - $this->totalAtEndD[$i];
			}

			$this->setArmiesDone = TRUE;
		}
	}
}
