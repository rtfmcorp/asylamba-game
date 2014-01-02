<?php

/**
 * Squadron
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 21.04.13
*/

class Squadron {
	protected $id = 0;
	
	protected $lineCoord 			= 0;
	protected $nbrships 			= 0;
	protected $rCommander 			= 0;
	protected $position				= 0; //position dans le tableau de l'armée
	protected $arrayOfShips 		= array();
	protected $dLAstModification 	='';
	
	protected $squadron 			= array();

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

	public function setId($id) { $this->id = $id; }

	public function isEmpty() {
		if ($this->getPev() == 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function __construct($vector, $id, $lineCoord, $position, $rCommander, $isAttacker = NULL) {
		for($i = 0; $i < 12; $i++) {
			$this->arrayOfShips[] = $vector[$i];
		}
		$this->dLAstModification = $vector[12];
		$this->lineCoord = $lineCoord;
		$this->id = $id;
		$this->rCommander = $rCommander;
		$this->position = $position;
		
		for ($i = 0; $i < $vector[0]; $i++) {
			$this->squadron[] = new Pegase($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[1]; $i++) {
			$this->squadron[] = new Satyre($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[2]; $i++) {
			$this->squadron[] = new Chimere($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[3]; $i++) {
			$this->squadron[] = new Sirene($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[4]; $i++) {
			$this->squadron[] = new Dryade($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[5]; $i++) {
			$this->squadron[] = new Meduse($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[6]; $i++) {
			$this->squadron[] = new Griffon($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[7]; $i++) {
			$this->squadron[] = new Cyclope($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[8]; $i++) {
			$this->squadron[] = new Minotaure($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[9]; $i++) {
			$this->squadron[] = new Hydre($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[10]; $i++) {
			$this->squadron[] = new Cerbere($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
		}
		for ($i = 0; $i < $vector[11]; $i++) {
			$this->squadron[] = new Phoenix($isAttacker);
			$this->squadron[$this->nbrships]->affectId($this->nbrships);
			$this->nbrships++;
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
}
?>