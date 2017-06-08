<?php

/**
 * Message Forum
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/

namespace Asylamba\Modules\Demeter\Model;

class Color {
	# Regime
	const DEMOCRATIC 				= 1;
	const ROYALISTIC 				= 2;
	const THEOCRATIC 				= 3;

	# Relation avec les autres factions
	const NEUTRAL 					= 0;
	const PEACE 					= 1;
	const ALLY 						= 2;
	const ENEMY 					= 3;

	# constantes de prestiges
	const TWO_POINTS_PER_LEVEL 		= 2;
	const FOUR_POINTS_PER_LEVEL 	= 4;
	## empire
	const POINTCONQUER				= 100;
	const POINTBUILDBIGSHIP			= 25;

	## negore
	const MIN_PRICE 				= 10000;
	const COEF_POINT_SELLING 		= 0.00002; 	# == * 50K

	## cardan
	const BONUSOUTOFSECTOR			= 20;
	const POINTDONATE				= 10;
	const COEFPOINTDONATE 			= 0.0001;

	## kovakh
	const POINTBUILDLITTLESHIP 		= 1;
	const POINTCHANGETYPE 			= 50;
	const POINT_BATTLE_WIN			= 10;
	const POINT_BATTLE_LOOSE 		= 20;

	## Synelle
	const POINTDEFEND		  		= 20;

	## Nerve
	const COEFFPOINTCONQUER			= 10;
	// POINTCHANGETYPE aussi

	## Aphéra
	const POINTSPY					= 10;
	const POINTRESEARCH				= 2;

	# const
	const NBRGOVERNMENT 			= 6;

	const CAMPAIGNTIME 				= 345600;
	const ELECTIONTIME				= 172800;
	const PUTSCHTIME 				= 25200;

	const PUTSCHPERCENTAGE			= 15;

	const ALIVE 					= 1;
	const DEAD 						= 0;

	const MANDATE		 			= 1;
	const CAMPAIGN		 			= 2;
	const ELECTION 					= 3;

	const NOT_WIN 					= 0;
	const WIN 						= 1;

	# attributs issus de la db
	public $id 						= 0;
	public $alive 					= 0;
	public $isWinner 				= 0;
	public $credits					= 0;
	public $players 				= 0;
	public $activePlayers 			= 0;
	public $rankingPoints 			= 0;
	public $points					= 0;
	public $sectors					= 0;
	public $electionStatement		= 0;
	public $isClosed				= 0;
	public $description				= 0;
	public $dClaimVictory			= '';
	public $dLastElection			= '';
	public $isInGame 				= 0;

	# attributs issus des resources
	public $officialName 			= '';
	public $popularName 			= '';
	public $government 				= '';
	public $demonym 				= '';
	public $factionPoint 			= '';
	public $status 					= [''];
	public $regime 					= 0;
	public $devise 					= '';
	public $desc1 					= '';
	public $desc2 					= '';
	public $desc3 					= '';
	public $desc4 					= '';
	public $bonus 					= []; # array des id des bonus
	public $mandateDuration 		= 0;
	public $senateDesc 				= '';
	public $campaignDesc 			= '';

	public $bonusText 				= ['']; # array des descriptions des bonus

	public $colorLink 				= [];

	public $chiefId					= 0;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

//Kern je suis pas sur du type bool pour alive et isWinner
	/**
	 * @return bool
	 */
	public function getAlive()
	{
		return $this->alive;
	}

	/**
	 * @param bool $alive
	 * @return $this
	 */
	public function setAlive($alive)
	{
		$this->alive = $alive;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsWinner()
	{
		return $this->isWinner;
	}

	/**
	 * @param bool $isWinner
	 * @return $this
	 */
	public function setIsWinner($isWinner)
	{
		$this->isWinner = $isWinner;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getCredits()
	{
	  return $this->credits;
	}

	/**
	 * @param int $credits
	 * @return $this
	 */
	public function setCredits($credits)
	{
	  $this->credits = $credits;

	  return $this;
	}
	
	//Kern je connais pas le format pour Players
	/**
	 * @return int
	 */
	public function getPlayers()
	{
	  return $this->players;
	}

	/**
	 * @param int $players
	 * @return $this
	 */
	public function setPlayers($players)
	{
	  $this->players = $players;

	  return $this;
	}
	/**
	 * @param int $idPlayer
	 * @return $this
	 */
	public function addPlayer($idPlayer)
	{
		$this->players = players;

		return $this;
	}

//Kern je connais pas le type non plus, je suppose que c'est un tableau d'int avec les id
	/**
	 * @return int
	 */
	public function getActivePlayers()
	{
	  return $this->activePlayers;
	}

	/**
	 * @param int $activePlayers
	 * @return $this
	 */
	public function setActivePlayers($activePlayers)
	{
	  $this->activePlayers = $activePlayers;

	  return $this;
	}


	/**
	 * @param int $credits
	 */
	public function increaseCredit($credit) {
		$this->credits += $credit;
	}

	/**
	 * @param int $credits
	 */
	public function decreaseCredit($credit) {
		$this->credits -= $credit;
	}
}
