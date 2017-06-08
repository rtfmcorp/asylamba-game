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

	/**
	 * @return array
	 */
	public function getPlayers()
	{
	  return $this->players;
	}

	/**
	 * @param array $players
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
		//Kern je sais pas s'il le faut, et je sais pas comment l'écrire mais j'hésite avec le setPplayers je sais pas si on set tout d'un coup ou si c'est plutot par ajout
		$this->players = players;

		return $this;
	}

//Kern je connais pas le type non plus, je suppose que c'est un tableau d'int avec les id
	/**
	 * @return array
	 */
	public function getActivePlayers()
	{
	  return $this->activePlayers;
	}

	/**
	 * @param array $activePlayers
	 * @return $this
	 */
	public function setActivePlayers($activePlayers)
	{
	  $this->activePlayers = $activePlayers;

	  return $this;
	}

	/**
	 * @return int
	 */
	public function getRankingPoints()
	{
	  return $this->rankingPoints;
	}

	/**
	 * @param int $rankingPoints
	 * @return $this
	 */
	public function setId($rankingPoints)
	{
	  $this->rankingPoints = $rankingPoints;

	  return $this;
	}

	/**
	 * @return int
	 */
	public function getPoints()
	{
	  return $this->points;
	}

	/**
	 * @param int $points
	 * @return $this
	 */
	public function setPoints($points)
	{
	  $this->points = $points;

	  return $this;
	}

	/**
	 * @return array
	 */
	public function getSectors()
	{
	  return $this->id;
	}

	/**
	 * @param array $sectors
	 * @return $this
	 */
	public function setSectors($sectors)
	{
	  $this->sectors = $sectors;

	  return $this;
	}

	/**
	 * @return int
	 */
	public function getElectionStatement()
	{
	  return $this->electionStatement;
	}

	/**
	 * @param int $electionStatement
	 * @return $this
	 */
	public function setElectionStatement($electionStatement)
	{
	  $this->electionStatement = $electionStatement;

	  return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsClosed()
	{
	  return $this->isClosed;
	}

	/**
	 * @param bool $isClosed
	 * @return $this
	 */
	public function setIsClosed($isClosed)
	{
	  $this->isClosed = $isClosed;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
	  return $this->description;
	}
	//Kern je me base sur les valeurs par défaut mais ça me semble bizarre : public $description= 0;
	/**
	 * @param int $description
	 * @return $this
	 */
	public function setDescription($description)
	{
	  $this->description = $description;

	  return $this;
	}
	//Kern Pareil ici : public $dClaimVictory			= '';
	/**
	 * @return string
	 */
	public function getDClaimVictory()
	{
	  return $this->dClaimVictory;
	}

	/**
	 * @param string $dClaimVictory
	 * @return $this
	 */
	public function setDClaimVictory($dClaimVictory)
	{
	  $this->dClaimVictory = $dClaimVictory;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getDLastElection()
	{
	  return $this->dLastElection;
	}

	/**
	 * @param string $dLastElection
	 * @return $this
	 */
	public function setDLastElection($dLastElection)
	{
	  $this->dLastElection = $dLastElection;

	  return $this;
	}

	/**
	 * @return int
	 */
	public function getIsInGame()
	{
	  return $this->isInGame;
	}

	/**
	 * @param int $isInGame
	 * @return $this
	 */
	public function setIsInGame($isInGame)
	{
	  $this->isInGame = $isInGame;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getOfficialName()
	{
	  return $this->officialName;
	}

	/**
	 * @param string $officialName
	 * @return $this
	 */
	public function getOfficialName($officialName)
	{
	  $this->officialName = $officialName;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getPopularName()
	{
	  return $this->popularName;
	}

	/**
	 * @param string $popularName
	 * @return $this
	 */
	public function setPopularName($popularName)
	{
	  $this->popularName = $popularName;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getGovernment()
	{
	  return $this->government;
	}

	/**
	 * @param string $government
	 * @return $this
	 */
	public function setGovernment($government)
	{
	  $this->government = $government;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getDemonym()
	{
	  return $this->demonym;
	}

	/**
	 * @param string $demonym
	 * @return $this
	 */
	public function setDemonym($demonym)
	{
	  $this->demonym = $demonym;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getFactionPoint()
	{
	  return $this->factionPoint;
	}

	/**
	 * @param string $factionPoint
	 * @return $this
	 */
	public function setFactionPoint($factionPoint)
	{
	  $this->factionPoint = $factionPoint;

	  return $this;
	}

	/**
	 * @return array
	 */
	public function getStatus()
	{
	  return $this->status;
	}

	/**
	 * @param array $status
	 * @return $this
	 */
	public function setStatus($status)
	{
	  $this->status = $status;

	  return $this;
	}

	/**
	 * @return int
	 */
	public function getRegime()
	{
	  return $this->regime;
	}

	/**
	 * @param int $regime
	 * @return $this
	 */
	public function setRegime($regime)
	{
	  $this->regime = $regime;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getDevise()
	{
	  return $this->devise;
	}

	/**
	 * @param string $devise
	 * @return $this
	 */
	public function setDevise($devise )
	{
	  $this->devise = $devise ;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getDesc1()
	{
	  return $this->desc1;
	}

	/**
	 * @param string $desc1
	 * @return $this
	 */
	public function setDesc1($desc1)
	{
	  $this->desc1 = $desc1;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getDesc2()
	{
	  return $this->desc2;
	}

	/**
	 * @param string $desc2
	 * @return $this
	 */
	public function setDesc2($desc2)
	{
	  $this->desc2 = $desc2;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getDesc3()
	{
	  return $this->desc3;
	}

	/**
	 * @param string $desc3
	 * @return $this
	 */
	public function setDesc3($desc3)
	{
	  $this->desc3 = $desc3;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getDesc4()
	{
	  return $this->desc4;
	}

	/**
	 * @param string $desc4
	 * @return $this
	 */
	public function setDesc4($desc4)
	{
	  $this->desc4 = $desc4;

	  return $this;
	}

	/**
	 * @return array
	 */
	public function getBonus()
	{
	  return $this->bonus;
	}

	/**
	 * @param array $bonus
	 * @return $this
	 */
	public function setBonus($bonus)
	{
	  $this->bonus = $bonus;

	  return $this;
	}

	/**
	 * @return int
	 */
	public function getMandateDuration()
	{
	  return $this->mandateDuration;
	}

	/**
	 * @param int $mandateDuration
	 * @return $this
	 */
	public function setMandateDuration($mandateDuration)
	{
	  $this->mandateDuration = $mandateDuration;

	  return $this;
	}

	/**
	 * @return string
	 */
	public function getSenateDesc()
	{
	  return $this->senateDesc;
	}

	/**
	 * @param string $senateDesc
	 * @return $this
	 */
	public function setSenateDesc($senateDesc)
	{
	  $this->senateDesc = $senateDesc;

	  return $this;
	}

	/**
	 * @return int
	 */
	public function getCampaignDesc()
	{
	  return $this->campaignDesc;
	}

	/**
	 * @param int $campaignDesc
	 * @return $this
	 */
	public function setCampaignDesc($campaignDesc)
	{
	  $this->campaignDesc = $campaignDesc;

	  return $this;
	}

	/**
	 * @return array
	 */
	public function getBonusText()
	{
	  return $this->bonusText;
	}

	/**
	 * @param array $bonusText
	 * @return $this
	 */
	public function SetBonusText($bonusText )
	{
	  $this->bonusText = $bonusText ;

	  return $this;
	}

	/**
	 * @return array
	 */
	public function getColorLink()
	{
	  return $this->colorLink;
	}

	/**
	 * @param array $colorLink
	 * @return $this
	 */
	public function setColorLink($colorLink)
	{
	  $this->colorLink = $colorLink;

	  return $this;
	}

	/**
	 * @return int
	 */
	public function getChiefId()
	{
	  return $this->chiefId;
	}

	/**
	 * @param int $chiefId
	 * @return $this
	 */
	public function setChiefId($chiefId)
	{
	  $this->chiefId = $chiefId;

	  return $this;
	}
	
	/**
	 * @param int $credit
	 */
	public function increaseCredit($credit) {
		$this->credits += $credit;
	}

	/**
	 * @param int $credit
	 */
	public function decreaseCredit($credit) {
		$this->credits -= $credit;
	}
}
