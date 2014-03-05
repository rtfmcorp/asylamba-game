<?php

/**
*CommanderInFights, septembre 2012
* @author Noé Zufferey
* copyright Expansion
* @package Ares
*/
class CommanderReport_v1 {
	// INFOS COMMANDANT
	
	public $id 						= 0;
	public $name 					= '';
	public $avatar					= '';
	public $experience				= 0;
	public $level 					= 0;
	public $palmares				= 0;
	public $rBase					= 0;
	public $playerName 				= '';
	public $statement  				= NULL;
	public $squadronsIds 			= array();
	public $rPlayer 				= 0;
	public $sexe 					= 0;
	public $age						= 0;
	public $comment 				= ''; 	        
	public $dCreation 				= '';	        
	public $dAffectation 			= '';	    
	public $dDeath 					= '';
	public $oBName					= '';
	public $pevInBegin 				= 0;
	public $armyInBegin 			= array();
	public $armyAtEnd 				= array();
	public $earnedExperience 		= 0;
	public $winner					= FALSE;
	public $isAttacker 				= NULL;

	// TABLEAU D'OBJETS SQUADRON
	public $army = array();

	//const de lineCoord
	public $CONSTLineCoord = array(1, 1, 1, 2, 2, 1, 2, 3, 3, 1, 2, 3, 4, 4, 2, 3, 4, 5, 5, 3, 4, 5, 6, 6, 4, 5, 6, 7, 7, 5, 6, 7);

	public function __construct($commander) {
		$this->setAttributs($commander);
	}

	public function setAttributs($commander) {
		$this->id = $commander->getId();
		$this->name = $commander->getName();
		$this->avatar = $commander->getAvatar();
		$this->experience	= $commander->getExperience();
		$this->level = $commander->getLevel();
		$this->palmares = $commander->getPalmares();
		$this->rBase = $commander->getRBAse();
		$this->playerName = $commander->getPlayerName();
		$this->statement = $commander->getStatement();
		$this->squadronsIds = $commander->getSquadronsIds();
		$this->rPlayer = $commander->getRPlayer();
		$this->sexe = $commander->getSexe();
		$this->age = $commander->getAge();
		$this->comment = $commander->getComment();
		$this->dCreation = $commander->getDCreation();
		$this->dAffectation = $commander->getDAffectation();
		$this->dDeath = $commander->getDDeath();	
		$this->oBName	= $commander->getOBName();
		$this->pevInBegin = $commander->getPevInBegin();
		$this->armyInBegin = $commander->getArmyInBegin();
		$this->armyAtEnd 	= $commander->getArmyAtEnd();
		$this->earnedExperience = $commander->getEarnedExperience();
		$this->winner = $commander->getWinner();
		$this->isAttacker = $commander->getIsAttacker();
	}

	public function getPev() {
		$pev = 0;
		foreach ($this->army AS $squadron) {
			$pev += $squadron->getPev();
		}
		return $pev;
	}
}
?>