<?php

/**
 * Squadron In Fight
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 21.04.13
*/

class SquadronInFight {
	protected $id = 0;
	
	protected $relId 				= 0;
	protected $lineCoord 			= 0;
	protected $nbrOfShips 			= 0;
	protected $rCommander 			= 0;
	protected $tableId 				= 0;
	protected $arrayOfShips 		= array();
	protected $dLAstModification 	='';

	
	protected $squadron = array();

		// GETTER 

	public function getRelId()				{ return $this->relId; }
	public function getId()					{ return $this->id; }
	public function getLineCoord()			{ return $this->lineCoord; }
	public function getNbrOfShips()			{ return $this->nbrOfShips; }
	public function getArrayOfShips()		{ return $this->arrayOfShips; }
	public function getShip($key)			{ return $this->squadron[$key]; }
	public function getRCommander()			{ return $this->rCommander; }
	public function getTableId()			{ return $this->tableId; }
	public function getNbrShipByType($i)	{ return $this->arrayOfShips[$i]; }
	public function getSquadron()			{ return $this->squadron; }
	public function getDLastModification()	{ return $this->dLAstModification; }

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
	
		// SETTER

	public function setRelId($id) { $this->relId = $id; } 
		
	public function __construct($vector, $tableId, $lineCoord, $id, $rCommander, $isAttacker = NULL) {
		$this->lineCoord = $lineCoord;
		$this->id = $id;
		$this->arrayOfShips = $vector;
		$this->rCommander = $rCommander;
		$this->tableId = $tableId;
		$this->dLAstModification = $vector[12];
		
		for ($i = 0; $i < $vector[0]; $i++) {
			$this->squadron[] = new Pegase($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[1]; $i++) {
			$this->squadron[] = new Satyre($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[2]; $i++) {
			$this->squadron[] = new Chimere($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[3]; $i++) {
			$this->squadron[] = new Sirene($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[4]; $i++) {
			$this->squadron[] = new Dryade($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[5]; $i++) {
			$this->squadron[] = new Meduse($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[6]; $i++) {
			$this->squadron[] = new Griffon($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[7]; $i++) {
			$this->squadron[] = new Cyclope($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[8]; $i++) {
			$this->squadron[] = new Minotaure($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[9]; $i++) {
			$this->squadron[] = new Hydre($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[10]; $i++) {
			$this->squadron[] = new Cerbere($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
		for ($i = 0; $i < $vector[11]; $i++) {
			$this->squadron[] = new Phoenix($isAttacker);
			$this->squadron[$this->nbrOfShips]->affectId($this->nbrOfShips);
			$this->nbrOfShips++;
		}
	}
	
	public function engage($enemyCommander, $id, $idCommander, $nameCommander, $thisCommander) {
		$this->relId = $this->chooseEnemy($enemyCommander);
		if ($this->relId !== NULL) {

			$thisSquadronBefore = $this;
			$enemySquadronBefore = $enemyCommander->getSquadron($this->relId);
						
			$this->fight($enemyCommander->getSquadron($this->relId));
			$enemyCommander->getSquadron($this->relId)->setRelId($this->id);
			$enemyCommander->getSquadron($this->relId)->fight($thisCommander->getSquadron($this->id));

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
		$this->deleteOffset($this->squadron[$key]->getCodeName());
		
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
	
	protected function deleteOffset($name) {
		switch ($name) {
			case 'Pégase':
			$this->arrayOfShips[0]--;
			break;
			
			case 'Satyre':
			$this->arrayOfShips[1]--;
			break;
			
			case 'Chimère':
			$this->arrayOfShips[2]--;
			break;
			
			case 'Sirène':
			$this->arrayOfShips[3]--;
			break;
			
			case 'Dryade':
			$this->arrayOfShips[4]--;
			break;
			
			case 'Méduse':
			$this->arrayOfShips[5]--;
			break;
			
			case 'Griffon':
			$this->arrayOfShips[6]--;
			break;
			
			case 'Cyclope':
			$this->arrayOfShips[7]--;
			break;
			
			case 'Minotaure':
			$this->arrayOfShips[8]--;
			break;
			
			case 'Hydre':
			$this->arrayOfShips[9]--;
			break;
			
			case 'Cerbère':
			$this->arrayOfShips[10]--;
			break;
			
			case 'Phoenix':
			$this->arrayOfShips[11]--;
			break;
		}
	}
}
?>