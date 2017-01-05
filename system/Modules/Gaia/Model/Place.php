<?php
/**
 * Place
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 21.04.13
*/
namespace Asylamba\Modules\Gaia\Model;

class Place { 
	# CONSTANTS
	const TYP_EMPTY = 0;
	const TYP_MS1 = 1;
	const TYP_MS2 = 2;
	const TYP_MS3 = 3;
	const TYP_ORBITALBASE = 4;

	const COEFFMAXRESOURCE = 600;
	const COEFFRESOURCE = 2;
	const REPOPDANGER = 2;
	const COEFFPOPRESOURCE = 50;
	const COEFFDANGER = 5;

	# typeOfPlace
	const TERRESTRIAL = 1;
	const EMPTYZONE = 6; # zone vide

	# CONST PNJ COMMANDER
	const LEVELMAXVCOMMANDER = 20;
	const POPMAX 			 = 250;
	const DANGERMAX 		 = 100;

	# CONST RESULT BATTLE
	const CHANGESUCCESS 						= 10;
	const CHANGEFAIL							= 11;
	const CHANGELOST							= 12;

	const LOOTEMPTYSSUCCESS 					= 20;
	const LOOTEMPTYFAIL							= 21;
	const LOOTPLAYERWHITBATTLESUCCESS			= 22;
	const LOOTPLAYERWHITBATTLEFAIL				= 23;
	const LOOTPLAYERWHITOUTBATTLESUCCESS		= 24;
	const LOOTLOST								= 27;

	const CONQUEREMPTYSSUCCESS 					= 30;
	const CONQUEREMPTYFAIL						= 31;
	const CONQUERPLAYERWHITBATTLESUCCESS		= 32;
	const CONQUERPLAYERWHITBATTLEFAIL			= 33;
	const CONQUERPLAYERWHITOUTBATTLESUCCESS		= 34;
	const CONQUERLOST							= 37;

	const COMEBACK 								= 40;

	# constante de danger
	const DNG_CASUAL 							= 10;
	const DNG_EASY 								= 20;
	const DNG_MEDIUM 							= 50;
	const DNG_HARD 								= 75;
	const DNG_VERY_HARD 						= 100;

	// PLACE
	public $id = 0;
	public $rPlayer = NULL;
	public $rSystem = 0;
	public $typeOfPlace = 0;
	public $position = 0;
	public $population = 0;
	public $coefResources = 0;
	public $coefHistory = 0;
	public $resources = 0; 						# de la place si $typeOfBase = 0, sinon de la base
	public $danger = 0;							# danger actuel de la place (force des flottes rebelles)
	public $maxDanger = 0;						# danger max de la place (force des flottes rebelles)
	public $uPlace = '';

	// SYSTEM
	public $rSector = 0;
	public $xSystem = 0;
	public $ySystem = 0;
	public $typeOfSystem = 0;

	// SECTOR
	public $tax = 0;
	public $sectorColor = 0;

	// PLAYER
	public $playerColor = 0;
	public $playerName = '';
	public $playerAvatar = '';
	public $playerStatus = 0;
	public $playerLevel = 0;

	// BASE
	public $typeOfBase = 0;
	public $typeOfOrbitalBase;
	public $baseName = '';
	public $points = '';

	// OB
	public $levelCommercialPlateforme = 0;
	public $levelSpatioport = 0;
	public $antiSpyInvest = 0;

	// COMMANDER 
	public  $commanders = array();

	//uMode
	public $uMode = TRUE;

	public function getId() 							{ return $this->id; }
	public function getRPlayer() 						{ return $this->rPlayer; }
	public function getRSystem() 						{ return $this->rSystem; }
	public function getTypeOfPlace() 					{ return $this->typeOfPlace; }
	public function getPosition() 						{ return $this->position; }
	public function getPopulation() 					{ return $this->population; }
	public function getCoefResources() 					{ return $this->coefResources; }
	public function getCoefHistory() 					{ return $this->coefHistory; }
	public function getResources() 						{ return $this->resources; }
	public function getRSector() 						{ return $this->rSector; }
	public function getXSystem() 						{ return $this->xSystem; }
	public function getYSystem() 						{ return $this->ySystem; }
	public function getTypeOfSystem() 					{ return $this->typeOfSystem; }
	public function getTax() 							{ return $this->tax; }
	public function getSectorColor() 					{ return $this->sectorColor; }
	public function getPlayerColor() 					{ return $this->playerColor; }
	public function getPlayerName() 					{ return $this->playerName; }
	public function getPlayerAvatar() 					{ return $this->playerAvatar; }
	public function getPlayerStatus() 					{ return $this->playerStatus; }
	public function getTypeOfBase() 					{ return $this->typeOfBase; }
	public function getBaseName() 						{ return $this->baseName; }
	public function getPoints() 						{ return $this->points; }
	public function getLevelCommercialPlateforme() 		{ return $this->levelCommercialPlateforme; }
	public function getLevelSpatioport() 				{ return $this->levelSpatioport; }
	public function getAntiSpyInvest()					{ return $this->antiSpyInvest; }

	public function setId($v) 							{ $this->id = $v; }
	public function setRPlayer($v) 						{ $this->rPlayer = $v; }
	public function setRSystem($v) 						{ $this->rSystem = $v; }
	public function setTypeOfPlace($v) 					{ $this->typeOfPlace = $v; }
	public function setPosition($v) 					{ $this->position = $v; }
	public function setPopulation($v) 					{ $this->population = $v; }
	public function setCoefResources($v) 				{ $this->coefResources = $v; }
	public function setCoefHistory($v) 					{ $this->coefHistory = $v; }
	public function setResources($v) 					{ $this->resources = $v; }
	public function setRSector($v) 						{ $this->rSector = $v; }
	public function setXSystem($v) 						{ $this->xSystem = $v; }
	public function setYSystem($v) 						{ $this->ySystem = $v; }
	public function setTypeOfSystem($v) 				{ $this->typeOfSystem = $v; }
	public function setTax($v) 							{ $this->tax = $v; }
	public function setSectorColor($v) 					{ $this->sectorColor = $v; }
	public function setPlayerColor($v) 					{ $this->playerColor = $v; }
	public function setPlayerName($v) 					{ $this->playerName = $v; }
	public function setPlayerAvatar($v) 				{ $this->playerAvatar = $v; }
	public function setPlayerStatus($v) 				{ $this->playerStatus = $v; }
	public function setTypeOfBase($v) 					{ $this->typeOfBase = $v; }
	public function setBaseName($v) 					{ $this->baseName = $v; }
	public function setPoints($v) 						{ $this->points = $v; }
	public function setLevelCommercialPlateforme($v) 	{ $this->levelCommercialPlateforme = $v; }
	public function setLevelSpatioport($v) 				{ $this->levelSpatioport = $v; }
	public function setAntiSpyInvest($v)				{ $this->antiSpyInvest = $v; }
}