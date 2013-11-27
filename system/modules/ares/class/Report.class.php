<?php

/**
 * Report
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 21.04.13
*/

class Report {
	// public $commanders;
	public $id;
	public $resources;
	public $expCom = 0;
	public $expPlayerA = 0;
	public $expPlayerD = 0;
	public $rPlayerWinner = 0;
	public $round;
	public $rPlayerAttacker;
	public $rPlayerDefender;
	public $rPlace;
	public $type;
	public $importance = 0;
	public $dFight;
	public $statementAttacker = 0;
	public $statementDefender = 0;

	public $rBigReport = 0;
	public $fight = NULL;
	public $commanders = NULL;
	public $deletedOnce = NULL;

	public $placeName = '';

	public $finalCommanders = array();

	public function isCurrentPlayerWinner() {
		if (CTR::$data->get('playerId') == $rPlayerWinner) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getResourcesLooted() {
		return $this->resources;
	}

	//GETTERS ORIENTES JOUEUR

	private function wasPlayerInFight() {
		if ($this->rPlayerAttakcer == CTR::$data->get('playerId') OR $this->rPlayerDefender == CTR::$data->get('playerId')) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function getPlayerEarnedExperience() {
		if ($this->wasPlayerInFight()) {
			if ($this->rPlayerAttakcer == CTR::$data->get('playerId')) {
				return $this->expPlayerA;
			} else {
				return $this->expPlayerD;
			}
		} else {
			#CTR::$alert->add('Ce joueur n\'a pas participé au combat.', ALERT_STD_ERROR);
			return FALSE;
		}
	}

	public function isPlayerWinner() {
		if ($this->wasPlayerInFight()) {
			if ($this->rPlayerWinner == CTR::$data->get('playerId')) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			#CTR::$alert->add('Ce joueur n\'a pas participé au combat.', ALERT_STD_ERROR);
			return FALSE;
		}
	}

	public function isPlayerAttacker() {
		if ($this->wasPlayerInFight()) {
			if ($this->rPlayerAttakcer == CTR::$data->get('playerId')) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			#CTR::$alert->add('Ce joueur n\'a pas participé au combat.', ALERT_STD_ERROR);
			return FALSE;
		}
	}

	// GETTERS ORIENTES ATTAQUANT OU DEFENSEUR
	public function getCommanderPlayer() {
		if ($this->commanders != NULL) {
			if ($this->getPlayerIsAttacker()) {
				return $this->commanders[0];
			} else {
				return $this->commanders[1];
			}
		} else {
			#CTR::$alert->add('Ce rapport est chargé sans le Big.', ALERT_BUG_ERROR);
			return FALSE;
		}
	}

	public function getCommanderEnemy() {
		if ($this->commanders != NULL) {
			if ($this->getPlayerIsAttacker()) {
				return $this->commanders[1];
			} else {
				return $this->commanders[0];
			}
		} else {
			#CTR::$alert->add('Ce rapport est chargé sans le Big.', ALERT_BUG_ERROR);
			return FALSE;
		}
	}

	public function getShipsStateAttacker($commander) {
		return $this->resumeOfFight($commander[0]);
	}

	public function getShipsStateDefender($commander) {
		return $this->resumeOfFight($commander[1]);
	}

	private function resumeOfFight($c) {
		$shipsInBegin = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		$shipsAtEnd = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		$differenceOfShips = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);


		foreach ($c->armyInBegin AS $s) {
			for ($i = 0; $i < 12; $i++) {
				$shipsInBegin[$i] += $s[$i];
			}
		}

		foreach ($c->armyAtEnd AS $s) {
			for ($i = 0; $i < 12; $i++) {
				$shipsAtEnd[$i] += $s[$i];
			}
		}

		for ($i = 0; $i < 12; $i++) {
			$differenceOfShips[$i] = $shipsInBegin[$i] - $shipsAtEnd[$i];
		}

		return array($shipsInBegin, $shipsAtEnd, $differenceOfShips);
	}

	public function getId() { return $this->id; }

	// methode de création
	public function getFinalCommander($i) {
		return $this->finalCommanders[$i];
	}

	public function sortInformations() {
		$this->rPlayerAttacker = $this->finalCommanders[0]->getRPlayer();
		$this->rPlayerDefender = $this->finalCommanders[1]->getRPlayer();
		$this->expCom = round($this->finalCommanders[0]->getEarnedExperience());
		$this->expPlayerA = round($this->finalCommanders[0]->getEarnedExperience() / COEFFEXPPLAYER);
		$this->expPlayerD = round($this->finalCommanders[1]->getEarnedExperience() / COEFFEXPPLAYER);
	}
}
?>