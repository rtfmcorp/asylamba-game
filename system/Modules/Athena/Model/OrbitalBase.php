<?php

/**
 * Orbital Base
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 02.01.14
*/
namespace Asylamba\Modules\Athena\Model;

use ErrorException;

class OrbitalBase {
	# type of base
	const TYP_NEUTRAL = 0;
	const TYP_COMMERCIAL = 1;
	const TYP_MILITARY = 2;
	const TYP_CAPITAL = 3;

	const MAXCOMMANDERSTANDARD = 2;
	const MAXCOMMANDERMILITARY = 5;

	const COOL_DOWN = 12;
	const EXTRA_STOCK = 25000;

	const MAXCOMMANDERINMESS = 20;

	//ATTRIBUTES : ORBITALBASE
	public $rPlace;
	public $rPlayer;
	public $name;
	public $typeOfBase = 0;
	public $levelGenerator = 1;
	public $levelRefinery = 1;
	public $levelDock1 = 1;
	public $levelDock2 = 0;
	public $levelDock3 = 0;
	public $levelTechnosphere = 1;
	public $levelCommercialPlateforme = 0;
	public $levelStorage = 1;
	public $levelRecycling = 0;
	public $levelSpatioport = 0;
	public $points = 0;
	public $iSchool = 1000;
	public $iAntiSpy = 0;
	public $antiSpyAverage = 0;
	public $shipStorage = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $resourcesStorage = 5000;
	public $uOrbitalBase = '';
	public $dCreation = '';
	//ATTRIBUTES : PLACE
	public $position = 0;
	public $system = 0;
	public $xSystem = 0;
	public $ySystem = 0;
	public $sector = 0;
	public $sectorColor;
	public $tax = 0;
	public $planetPopulation = 0;
	public $planetResources = 0;
	public $planetHistory = 0;
	//ATTRIBUTES : OTHERS
	public $remainingTimeGenerator;
	public $remainingTimeDock1;
	public $remainingTimeDock2;
	public $remainingTimeDock3;
	public $routesNumber;
	//ATTRIBUTES : FUTURE LEVELS
	public $realGeneratorLevel;
	public $realRefineryLevel;
	public $realDock1Level;
	public $realDock2Level;
	public $realDock3Level;
	public $realTechnosphereLevel;
	public $realCommercialPlateformeLevel;
	public $realStorageLevel;
	public $realRecyclingLevel;
	public $realSpatioportLevel;
	// PUBLIC ATTRIBUTES
	public $buildingQueues = [];
	public $routeManager;
	public $technoQueueManager;
	public $commercialShippings = [];

	//GETTERS
	public function getId() { return $this->rPlace; }
	public function getRPlace() { return $this->rPlace; }
	public function getRPlayer() { return $this->rPlayer; }
	public function getName() { return $this->name; }
	public function getLevelGenerator() { return $this->levelGenerator; }
	public function getLevelRefinery() { return $this->levelRefinery; }
	public function getLevelDock1() { return $this->levelDock1; }
	public function getLevelDock2() { return $this->levelDock2; }
	public function getLevelDock3() { return $this->levelDock3; }
	public function getLevelTechnosphere() { return $this->levelTechnosphere; }
	public function getLevelCommercialPlateforme() { return $this->levelCommercialPlateforme; }
	public function getLevelStorage() { return $this->levelStorage; }
	public function getLevelRecycling() { return $this->levelRecycling; }
	public function getLevelSpatioport() { return $this->levelSpatioport; }
	public function getPoints() { return $this->points; }
	public function getISchool() { return $this->iSchool; }
	public function getIAntiSpy() { return $this->iAntiSpy; }
	public function getAntiSpyAverage() { return $this->antiSpyAverage; }
	public function getShipStorage($k = -1) {return ($k == -1) ? $this->shipStorage : $this->shipStorage[$k]; }
	public function getResourcesStorage() { return $this->resourcesStorage; }
	public function getDCreation() { return $this->dCreation; }

	public function getPosition() { return $this->position; }
	public function getSystem() { return $this->system; }
	public function getXSystem() { return $this->xSystem; }
	public function getYSystem() { return $this->ySystem; }
	public function getSector() { return $this->sector; }
	public function getTax() { return $this->tax; }
	public function getPlanetPopulation() { return $this->planetPopulation; }
	public function getPlanetResources() { return $this->planetResources; }
	public function getPlanetHistory() { return $this->planetHistory; }

	public function getRemainingTimeGenerator() { return $this->remainingTimeGenerator; }
	public function getRemainingTimeDock1() { return $this->remainingTimeDock1; }
	public function getRemainingTimeDock2() { return $this->remainingTimeDock2; }
	public function getRemainingTimeDock3() { return $this->remainingTimeDock3; }
	public function getRoutesNumber() { return $this->routesNumber; }

	public function getRealGeneratorLevel() { return $this->realGeneratorLevel; }
	public function getRealRefineryLevel() { return $this->realRefineryLevel; }
	public function getRealDock1Level() { return $this->realDock1Level; }
	public function getRealDock2Level() { return $this->realDock2Level; }
	public function getRealDock3Level() { return $this->realDock3Level; }
	public function getRealTechnosphereLevel() { return $this->realTechnosphereLevel; }
	public function getRealCommercialPlateformeLevel() { return $this->realCommercialPlateformeLevel; }
	public function getRealStorageLevel() { return $this->realStorageLevel; }
	public function getRealRecyclingLevel() { return $this->realRecyclingLevel; }
	public function getRealSpatioportLevel() { return $this->realSpatioportLevel; }

	public function getBuildingLevel($buildingNumber) {
		switch ($buildingNumber) {
			case 0 : return $this->levelGenerator;
			case 1 : return $this->levelRefinery;
			case 2 : return $this->levelDock1;
			case 3 : return $this->levelDock2;
			case 4 : return $this->levelDock3;
			case 5 : return $this->levelTechnosphere;
			case 6 : return $this->levelCommercialPlateforme;
			case 7 : return $this->levelStorage;
			case 8 : return $this->levelRecycling;
			case 9 : return $this->levelSpatioport;
			default : 
				throw new ErrorException('Bâtiment invalide dans getBuildingLevel de OrbitalBase');
				return FALSE;
		}
	}

	//SETTERS
	public function setId($var) { $this->rPlace = $var; }
	public function setRPlace($var) { $this->rPlace = $var; }
	public function setRPlayer($var) { $this->rPlayer = $var; }
	public function setName($var) { $this->name = $var; }
	public function setLevelGenerator($var) { $this->levelGenerator = $var; }
	public function setLevelRefinery($var) { $this->levelRefinery = $var; }
	public function setLevelDock1($var) { $this->levelDock1 = $var; }
	public function setLevelDock2($var) { $this->levelDock2 = $var; }
	public function setLevelDock3($var) { $this->levelDock3 = $var; }
	public function setLevelTechnosphere($var) { $this->levelTechnosphere = $var; }
	public function setLevelCommercialPlateforme($var) { $this->levelCommercialPlateforme = $var; }
	public function setLevelStorage($var) { $this->levelStorage = $var; }
	public function setLevelRecycling($var) { $this->levelRecycling = $var; }
	public function setLevelSpatioport($var) { $this->levelSpatioport = $var; }
	public function setPoints($var) { $this->points = $var; }
	public function setISchool($var) { $this->iSchool = $var; }
	public function setIAntiSpy($var) { $this->iAntiSpy = $var; }
	public function setAntiSpyAverage($var) { $this->antiSpyAverage = $var; }
	public function setShipStorage($k, $v) { $this->shipStorage[$k] = $v; }
	public function setResourcesStorage($var) { $this->resourcesStorage = $var; }
	public function setDCreation($var) { $this->dCreation = $var; }

	public function setPosition($var) { $this->position = $var; }
	public function setSystem($var) { $this->system = $var; }
	public function setXSystem($var) { $this->xSystem = $var; }
	public function setYSystem($var) { $this->ySystem = $var; }
	public function setSector($var) { $this->sector = $var; }
	public function setTax($var) { $this->tax = $var; }
	public function setPlanetPopulation($var) { $this->planetPopulation = $var; }
	public function setPlanetResources($var) { $this->planetResources = $var; }
	public function setPlanetHistory($var) { $this->planetHistory = $var; }

	public function setRemainingTimeGenerator($var) { $this->remainingTimeGenerator = $var; }
	public function setRemainingTimeDock1($var) { $this->remainingTimeDock1 = $var; }
	public function setRemainingTimeDock2($var) { $this->remainingTimeDock2 = $var; }
	public function setRemainingTimeDock3($var) { $this->remainingTimeDock3 = $var; }
	public function setRoutesNumber($var) { $this->routesNumber = $var; }
	
	public function setRealGeneratorLevel($var) { $this->realGeneratorLevel = $var; }
	public function setRealRefineryLevel($var) { $this->realRefineryLevel = $var; }
	public function setRealDock1Level($var) { $this->realDock1Level = $var; }
	public function setRealDock2Level($var) { $this->realDock2Level = $var; }
	public function setRealDock3Level($var) { $this->realDock3Level = $var; }
	public function setRealTechnosphereLevel($var) { $this->realTechnosphereLevel = $var; }
	public function setRealCommercialPlateformeLevel($var) { $this->realCommercialPlateformeLevel = $var; }
	public function setRealStorageLevel($var) { $this->realStorageLevel = $var; }
	public function setRealRecyclingLevel($var) { $this->realRecyclingLevel = $var; }
	public function setRealSpatioportLevel($var) { $this->realSpatioportLevel = $var; }

	public function setBuildingLevel($buildingNumber, $level) {
		switch ($buildingNumber) {
			case 0 : $this->levelGenerator = $level; break;
			case 1 : $this->levelRefinery = $level; break;
			case 2 : $this->levelDock1 = $level; break;
			case 3 : $this->levelDock2 = $level; break;
			case 4 : $this->levelDock3 = $level; break;
			case 5 : $this->levelTechnosphere = $level; break;
			case 6 : $this->levelCommercialPlateforme = $level; break;
			case 7 : $this->levelStorage = $level; break;
			case 8 : $this->levelRecycling = $level; break;
			case 9 : $this->levelSpatioport = $level; break;
			default : 
				throw new ErrorException('Bâtiment invalide dans setBuildingLevel de OrbitalBase');
		}
	}
	
	/**
	 * @param string $updatedAt
	 * @return OrbitalBase
	 */
	public function setUpdatedAt($updatedAt)
	{
		$this->uOrbitalBase = $updatedAt;
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getUpdatedAt()
	{
		return $this->uOrbitalBase;
	}
}
