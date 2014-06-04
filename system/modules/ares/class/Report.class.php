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

class Report {
	public $id					= 0;
	public $rPlayerAttacker		= 0;
	public $rPlayerDefender		= 0;
	public $rPlayerWinner		= 0;
	public $resources			= 0;
	public $expCom				= 0;
	public $expPlayerA			= 0;
	public $expPlayerD			= 0;
	public $rPlace				= 0;
	public $type				= 0;
	public $round				= 0;
	public $importance			= 0;
	public $statementAttacker	= 0;
	public $statementDefender	= 0;
	public $dFight				= '';
	public $placeName			= '';

	public $squadrons = array();

	public $armyInBeginA = array();
	public $armyInBeginD = array();
	public $armyAtEndA = array();
	public $armyAtEndD = array();

	public $fight = array();

	public function getId() {return $this->id;}

	public function setArmies($squadrons) {
		$this->squadrons = $squadrons;
		//remplir les escadrilles dans les bons tableaux
	}
}