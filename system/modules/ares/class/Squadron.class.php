<?php

/**
 * Squadron
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 13.02.14
*/

class Squadron {
	public $id = 0;
	
	public $lineCoord 			= 0;
	public $nbrships 			= 0;
	public $rCommander 			= 0;
	public $position				= 0; //position dans le tableau de l'armée
	public $arrayOfShips 		= array();
	public $relId 				= 0;
	public $nbrOfShips 			= 0;
	
	public $squadron 			= array();

		// GETTER 

	public function getId()					{ return $this->id; }
	public function getLineCoord()			{ return $this->lineCoord; }
	public function getNbrships()			{ return $this->nbrships; }
	public function getRCommander()			{ return $this->rCommander; }
	public function getPosition()			{ return $this->position; }
	public function getSquadron()			{ return $this->squadron; }
	public function getArrayOfShips()		{ return $this->arrayOfShips; }
	public function getDLastModification()	{ return $this->dLAstModification; } 

	public function getShip($key)			{ return $this->squadron[$key]; }
	public function getNbrShipByType($i)	{ return $this->arrayOfShips[$i]; }

	public function getPev() {
		$pev = 0;
		foreach ($this->squadron AS $ship) {
			$pev += $ship->getPev();
		}
		return $pev;
	}

	private function getPv() {
		$pv = 0;
		foreach ($this->squadron as $ship) {
			$pv += $ship->getLife();
		}
		return $pv;
	}

	private function howManyLostPv($squadron1, $squadron2) {
		$lostPv = abs($squadron1->getPv() - $squadron2->getPv());
		return $lostPv;
	}

	public function setId($id) { $this->id = $id; }

	public function isEmpty() {
		if ($this->getPev() == 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function setRelId($id) { $this->relId = $id; }
	
	public function __construct($vector, $id, $lineCoord, $position, $rCommander, $isAttacker = NULL) {
		for($i = 0; $i < 12; $i++) {
			$this->arrayOfShips[] = $vector[$i];
		}

		$this->dLAstModification = $vector[12];
		$this->lineCoord = $lineCoord;
		$this->id = $id;
		$this->rCommander = $rCommander;
		$this->position = $position;

		for($i = 0; $i < 12; $i++) {
			for ($j = 0; $j < $vector[$i]; $j++) {
				$this->squadron[] = new Ship($i, $isAttacker);
				$this->squadron[$this->nbrships]->affectId($this->nbrships);
				$this->nbrships++;
			}
		}
	}

	public function updateShip($shipNbrName, $nbr) {
		$this->arrayOfShips[$shipNbrName] += $nbr;
		ASM::$com->getById($this->rCommander)->hasToSave = TRUE;
	}

	public function emptySquadron() {
		for ($i = 0; $i < 12; $i++) {
			$this->arrayOfShips[$i] = 0;
		}
	}

	# méthodes de combat
	public function engage($enemyCommander, $position, $idCommander, $nameCommander, $thisCommander) {
		$this->relId = $this->chooseEnemy($enemyCommander);
		if ($this->relId !== NULL) {

			$thisSquadronBefore = $this;
			$enemySquadronBefore = $enemyCommander->getSquadron($this->relId);
						
			$this->fight($enemyCommander->getSquadron($this->relId));
			$enemyCommander->getSquadron($this->relId)->setRelId($this->position);
			$enemyCommander->getSquadron($this->relId)->fight($thisCommander->getSquadron($this->position));

			if ($thisCommander->getIsAttacker() == TRUE) {
				LiveReport::$fight[LiveReport::$halfround][] = array(
					array($thisSquadronBefore->getArrayOfShips(), $enemySquadronBefore->getArrayOfShips()), 
					array($this->arrayOfShips, $enemyCommander->getSquadron($this->relId)->getArrayOfShips()),
					array($this->howManyLostPv($thisSquadronBefore, $this), $this->howManyLostPv($enemySquadronBefore, $enemyCommander->getSquadron($this->relId)))
				);
			} else {
				LiveReport::$fight[LiveReport::$halfround][] = array(
					array($enemySquadronBefore->getArrayOfShips(), $thisSquadronBefore->getArrayOfShips()), 
					array($enemyCommander->getSquadron($this->relId)->getArrayOfShips(), $this->arrayOfShips),
					array($this->howManyLostPv($enemySquadronBefore, $enemyCommander->getSquadron($this->relId), $this->howManyLostPv($thisSquadronBefore, $this)))
				);
			}
		}
		return $enemyCommander;
	}
	
	private function chooseEnemy($enemyCommander) {
		$nbrShipsInLine = 0;
		foreach ($enemyCommander->getArmy() as $enemySquadron) {
			if ($enemySquadron->getLineCoord() * 3 <= FightController::getCurrentLine()) {
				$nbrShipsInLine += $enemySquadron->getNbrOfShips();
			}
		}
		if ($nbrShipsInLine == 0) {
			return NULL;
		} elseif ($this->relId != NULL AND $enemyCommander->getSquadron($this->relId)->getNbrOfShips() > 0) {
			return $this->relId;
		} else {
			$aleaNbr = rand(0, $enemyCommander->getLevel() - 1);
			for($i = 0; $i < $enemyCommander->getLevel(); $i++) {
				if ($enemyCommander->getSquadron($aleaNbr)->getLineCoord() * 3 <= FightController::getCurrentLine() AND $enemyCommander->getSquadron($aleaNbr)->getNbrOfShips() > 0) {
					break;
				} else {
					if ($aleaNbr == $enemyCommander->getLevel() - 1) {
						$aleaNbr = 0;
					} else {
						$aleaNbr++;
					}
				}
			}
			return $aleaNbr;
		}			
	}
	
	public function fight($enemySquadron) {
		foreach ($this->squadron as $ship) {
			if ($enemySquadron->getNbrOfShips() == 0) {
				break;
			}
			$enemySquadron = $ship->engage($enemySquadron);
		}
	}
	
	public function destructShip($key) {
		$this->deleteOffset($this->squadron[$key]->getNbrName());
		
		$this->squadron[$key] = NULL;
		$newSquadron = array();
		foreach ($this->squadron as $offset) {
			if ($offset != NULL) {
				$newSquadron[] = $offset;
			}
		}
		$this->squadron = $newSquadron;
		$this->nbrOfShips--;
	}
	
	private function deleteOffset($i) {
		$this->arrayOfShips[0]--;	
	}
}
?>