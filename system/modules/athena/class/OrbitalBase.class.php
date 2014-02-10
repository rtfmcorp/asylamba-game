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

class OrbitalBase {
	//ATTRIBUTES : ORBITALBASE
	private $rPlace;
	private $rPlayer;
	private $name;
	private $levelGenerator = 2;
	private $levelRefinery = 1;
	private $levelDock1 = 1;
	private $levelDock2 = 0;
	private $levelDock3 = 0;
	private $levelTechnosphere = 1;
	private $levelCommercialPlateforme = 0;
	private $levelGravitationalModule = 0;
	private $points = 0;
	private $iSchool = 1000;
	private $iUniversity = 5000;
	private $partNaturalSciences = 25;
	private $partLifeSciences = 25;
	private $partSocialPoliticalSciences = 25;
	private $partInformaticEngineering = 25;
	private $iAntiSpy = 0;
	private $antiSpyAverage = 0;
	private $shipStorage = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	private $motherShip = 0; // 1 = a motherShip level 1 is stocked, 2 = level 2, 3 = level 3
	private $isCommercialBase = -1;
	private $isProductionRefinery = 1;
	private $isProductionDock1 = 0;
	private $isProductionDock2 = 0;
	private $resourcesStorage = 5000;
	private $uResources = '';
	private $uBuildingQueue = '';
	private $uShipQueue1 = '';
	private $uShipQueue2 = '';
	private $uShipQueue3 = '';
	private $uTechnoQueue = '';
	private $uAntiSpy = '';
	private $dCreation = '';
	//ATTRIBUTES : PLACE
	private $position = 0;
	private $system = 0;
	private $xSystem = 0;
	private $ySystem = 0;
	private $sector = 0;
	private $tax = 0;
	private $planetPopulation = 0;
	private $planetResources = 0;
	private $planetHistory = 0;
	//ATTRIBUTES : OTHERS
	private $remainingTimeGenerator;
	private $remainingTimeDock1;
	private $remainingTimeDock2;
	private $remainingTimeDock3;
	private $routesNumber;
	//ATTRIBUTES : FUTURE LEVELS
	private $realGeneratorLevel;
	private $realRefineryLevel;
	private $realDock1Level;
	private $realDock2Level;
	private $realDock3Level;
	private $realTechnosphereLevel;
	private $realCommercialPlateformeLevel;
	private $realGravitationalModuleLevel;
	// PUBLIC ATTRIBUTES
	public $buildingManager;
	public $dock1Manager;
	public $dock2Manager;
	public $dock3Manager;
	public $routeManager;
	public $technoQueueManager;
	public $shippingManager;

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
	public function getLevelGravitationalModule() { return $this->levelGravitationalModule; }
	public function getPoints() { return $this->points; }
	public function getISchool() { return $this->iSchool; }
	public function getIUniversity() { return $this->iUniversity; }
	public function getPartNaturalSciences() { return $this->partNaturalSciences; }
	public function getPartLifeSciences() { return $this->partLifeSciences; }
	public function getPartSocialPoliticalSciences() { return $this->partSocialPoliticalSciences; }
	public function getPartInformaticEngineering() { return $this->partInformaticEngineering; }
	public function getIAntiSpy() { return $this->iAntiSpy; }
	public function getAntiSpyAverage() { return $this->antiSpyAverage; }
	public function getShipStorage($k = -1) {return ($k == -1) ? $this->shipStorage : $this->shipStorage[$k]; }
	public function getMotherShip() { return $this->motherShip; }
	public function getIsCommercialBase() { return $this->isCommercialBase; }
	public function getIsProductionRefinery() { return $this->isProductionRefinery; }
	public function getIsProductionDock1() { return $this->isProductionDock1; }
	public function getIsProductionDock2() { return $this->isProductionDock2; }
	public function getResourcesStorage() { return $this->resourcesStorage; }
	public function getUResources() { return $this->uResources; }
	public function getUBuildingQueue() { return $this->uBuildingQueue; }
	public function getUShipQueue1() { return $this->uShipQueue1; }
	public function getUShipQueue2() { return $this->uShipQueue2; }
	public function getUShipQueue3() { return $this->uShipQueue3; }
	public function getUTechnoQueue() { return $this->uTechnoQueue; }
	public function getUAntiSpy() { return $this->uAntiSpy; }
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
	public function getRealGravitationalModuleLevel() { return $this->realGravitationalModuleLevel; }

	public function getBuildingLevel($buildingNumber) {
		switch ($buildingNumber) {
			case 0 : return $this->levelGenerator;
			case 1 : return $this->levelRefinery;
			case 2 : return $this->levelDock1;
			case 3 : return $this->levelDock2;
			case 4 : return $this->levelDock3;
			case 5 : return $this->levelTechnosphere;
			case 6 : return $this->levelCommercialPlateforme;
			case 7 : return $this->levelGravitationalModule;
			default : 
				CTR::$alert->add('Bâtiment invalide');
				CTR::$alert->add('dans getBuildingLevel de OrbitalBase', ALERT_BUG_ERROR);
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
	public function setLevelGravitationalModule($var) { $this->levelGravitationalModule = $var; }
	public function setPoints($var) { $this->points = $var; }
	public function setISchool($var) { $this->iSchool = $var; }
	public function setIUniversity($var) { $this->iUniversity = $var; }
	public function setPartNaturalSciences($var) { $this->partNaturalSciences = $var; }
	public function setPartLifeSciences($var) { $this->partLifeSciences = $var; }
	public function setPartSocialPoliticalSciences($var) { $this->partSocialPoliticalSciences = $var; }
	public function setPartInformaticEngineering($var) { $this->partInformaticEngineering = $var; }
	public function setIAntiSpy($var) { $this->iAntiSpy = $var; }
	public function setAntiSpyAverage($var) { $this->antiSpyAverage = $var; }
	public function setShipStorage($k, $v) { $this->shipStorage[$k] = $v; }
	public function setMotherShip($var) { $this->motherShip = $var; }
	public function setIsCommercialBase($var) {
		if ($var == -1 || $var == 0 || $var == 1) {
			$this->isCommercialBase = $var;
		} else {
			CTR::$alert->add('une base doit être commerciale ou non');
			CTR::$alert->add('dans setIsCommercialBase de OrbitalBase', ALERT_BUG_ERROR);
		}
	}
	public function setIsProductionRefinery($var) { $this->isProductionRefinery = $var; }
	public function setIsProductionDock1($var) { $this->isProductionDock1 = $var; }
	public function setIsProductionDock2($var) { $this->isProductionDock2 = $var; }
	public function setResourcesStorage($var) { $this->resourcesStorage = $var; }
	public function setUResources($var) { $this->uResources = $var; }
	public function setUBuildingQueue($var) { $this->uBuildingQueue = $var; }
	public function setUShipQueue1($var) { $this->uShipQueue1 = $var; }
	public function setUShipQueue2($var) { $this->uShipQueue2 = $var; }
	public function setUShipQueue3($var) { $this->uShipQueue3 = $var; }
	public function setUTechnoQueue($var) { $this->uTechnoQueue = $var; }
	public function setUAntiSpy($var) { $this->uAntiSpy = $var; }
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
	public function setrealCommercialPlateformeLevel($var) { $this->realCommercialPlateformeLevel = $var; }
	public function setRealGravitationalModuleLevel($var) { $this->realGravitationalModuleLevel = $var; }

	public function setBuildingLevel($buildingNumber, $level) {
		switch ($buildingNumber) {
			case 0 : $this->levelGenerator = $level; break;
			case 1 : $this->levelRefinery = $level; break;
			case 2 : $this->levelDock1 = $level; break;
			case 3 : $this->levelDock2 = $level; break;
			case 4 : $this->levelDock3 = $level; break;
			case 5 : $this->levelTechnosphere = $level; break;
			case 6 : $this->levelCommercialPlateforme = $level; break;
			case 7 : $this->levelGravitationalModule = $level; break;
			default : 
				CTR::$alert->add('bâtiment invalide');
				CTR::$alert->add('dans setBuildingLevel de OrbitalBase', ALERT_BUG_ERROR);
		}
	}

	public function updatePoints() {
		$points = 0;
		for ($i=0; $i < 8; $i++) { 
			for ($j=0; $j < $this->getBuildingLevel($i); $j++) { 
				$points += OrbitalBaseResource::getBuildingInfo($i, 'level', $j + 1, 'points');
			}
		}
		$this->setPoints($points);
	}

	// UPDATE METHODS
	public function uResources($dUpdate) {
		if ($this->uResources != NULL AND $this->uResources != '0000-00-00 00:00:00') {
			$factor = Utils::interval($this->uResources, $dUpdate, 'h');
			if ($factor > 0) {
				$addResources = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(1, 'level', $this->levelRefinery, 'refiningCoefficient'), $this->planetResources);
				if ($this->isProductionRefinery == 1) {
					$addResources += $addResources * OBM_COEFPRODUCTION;
				}
				$newResources = $this->resourcesStorage + (int)($factor * $addResources);
				$maxStorage = OrbitalBaseResource::getBuildingInfo(1, 'level', $this->levelRefinery, 'storageSpace');
				if ($this->isProductionRefinery == 0) {
					$maxStorage += $maxStorage * OBM_COEFPRODUCTION;
				}
				if ($newResources > $maxStorage) {
					$this->resourcesStorage = $maxStorage;
				} else {
					$this->resourcesStorage = $newResources;
				}
				$this->uResources = $dUpdate;
				return TRUE;
			} 
			return TRUE;
		} else {
			$this->uResources = Utils::now();
			return TRUE;
		}
	}

	public function uBuildingQueue($dUpdate, $player) {
		# charger la queue
		$S_BQM2 = ASM::$bqm->getCurrentSession();
		ASM::$bqm->changeSession($this->buildingManager);

		# parcours de la queue pour analyse
		$queueToRemove = array();
		for ($i = 0; $i < ASM::$bqm->size(); $i++) { 
			$queue = ASM::$bqm->get($i);

			if ($queue->dEnd < $dUpdate) {
				$queueToRemove[] = $queue->id;
			}
		}
		
		# delete des queues terminées
		foreach ($queueToRemove as $i) {
			$queue = ASM::$bqm->getById($i);
			# update builded building
			$this->setBuildingLevel($queue->buildingNumber, ($this->getBuildingLevel($queue->buildingNumber) + 1));
			# update the points of the orbitalBase
			$this->updatePoints();
			# increase player experience
			$experience = OrbitalBaseResource::getBuildingInfo($queue->buildingNumber, 'level', $queue->targetLevel, 'points');
			$player->increaseExperience($experience);
			# alert
			if (CTR::$data->get('playerId') == $this->rPlayer) {
				CTR::$alert->add('Construction de votre ' . OrbitalBaseResource::getBuildingInfo($queue->buildingNumber, 'frenchName') . ' niveau ' . $queue->targetLevel . ' sur ' . $this->name . ' terminée. Vous gagnez ' . $experience . ' d\'expérience.', ALERT_GAM_GENERATOR);
			}
			# delete queue in database
			ASM::$bqm->deleteById($i);
		}

		ASM::$bqm->changeSession($S_BQM2);
	}

	public function uShipQueue1($dUpdate, $player) {
		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->changeSession($this->dock1Manager);
		$size = ASM::$sqm->size();
		if ($size >= 1) {
			$index = 0;
			while ($index < $size) {
				$sq = ASM::$sqm->get($index);

				if ($sq->dEnd < $dUpdate) {
					// vaisseau construit
					$this->setShipStorage($sq->shipNumber, $this->getShipStorage($sq->shipNumber) + $sq->quantity);
					//increase player experience
					$experience = $sq->quantity * ShipResource::getInfo($sq->shipNumber, 'points');
					$player->increaseExperience($experience);
					//alert
					if (CTR::$data->get('playerId') == $this->rPlayer) {
						$alt = 'Construction de ';
						if ($sq->quantity > 1) {
							$alt .= 'vos ' . $sq->quantity . ' ' . ShipResource::getInfo($sq->shipNumber, 'codeName') . 's';
						} else {
							$alt .= 'votre ' . ShipResource::getInfo($sq->shipNumber, 'codeName');
						}
						$alt .= ' sur ' . $this->name . ' terminée. Vous gagnez ' . $experience . ' d\'expérience.';
						CTR::$alert->add($alt, ALERT_GAM_DOCK1);
					}
					//delete queue in database
					ASM::$sqm->deleteById($sq->id);
					$size--;
				} else {
					break;
				}
			}
		} 
		ASM::$sqm->changeSession($S_SQM1);
		return TRUE;
	}

	public function uShipQueue2($dUpdate, $player) {
		$S_SQM1 = ASM::$sqm->getCurrentSession();
		ASM::$sqm->changeSession($this->dock2Manager);
		$size = ASM::$sqm->size();
		if ($size >= 1) {
			$index = 0;
			while ($index < $size) {
				$sq = ASM::$sqm->get($index);

				if ($sq->dEnd < $dUpdate) {
					// vaisseau construit
					$this->setShipStorage($sq->shipNumber, $this->getShipStorage($sq->shipNumber) + 1);
					//increase player experience
					$experience = ShipResource::getInfo($sq->shipNumber, 'points');
					$player->increaseExperience($experience);
					//alert
					if (CTR::$data->get('playerId') == $this->rPlayer) {
						CTR::$alert->add('Construction de votre ' . ShipResource::getInfo($sq->shipNumber, 'codeName') . ' sur ' . $this->name . ' terminée. Vous gagnez ' . $experience . ' d\'expérience.', ALERT_GAM_DOCK2);
					}
					//delete queue in database
					ASM::$sqm->deleteById($sq->id);
					$size--;
				} else {
					break;
				}
			}
		} 
		ASM::$sqm->changeSession($S_SQM1);
		return TRUE;
	}

	public function uTechnologyQueue($dUpdate, $player) {
			$S_TQM1 = ASM::$tqm->getCurrentSession();
			ASM::$tqm->changeSession($this->technoQueueManager);
			$size = ASM::$tqm->size();
			if ($size >= 1) {
				$index = 0;
				while ($index < $size) {
					$tq = ASM::$tqm->get($index);

					if ($tq->dEnd < $dUpdate) {
						// technologie construite
						$techno = new Technology($player->getId());
						$techno->setTechnology($tq->technology, $tq->targetLevel);
						//increase player experience
						$experience = TechnologyResource::getInfo($tq->technology, 'points', $tq->targetLevel);
						$player->increaseExperience($experience);
						//alert
						if (CTR::$data->get('playerId') == $this->rPlayer) {
							$alt = 'Développement de votre technologie ' . TechnologyResource::getInfo($tq->technology, 'name');
							if ($tq->targetLevel > 1) {
								$alt .= ' niveau ' . $tq->targetLevel;
							} 
							$alt .= ' terminée. Vous gagnez ' . $experience . ' d\'expérience.';
							CTR::$alert->add($alt, ALERT_GAM_TECHNO);
						}
						//delete queue in database
						ASM::$tqm->deleteById($tq->id);
						$size--;
					} else {
						break;
					}
				}
			}
			ASM::$tqm->changeSession($S_TQM1);
			return TRUE;
	}

	public function uAntiSpy($now) {
		$hInterval = Utils::interval($now, $this->uAntiSpy, 'h');
		$this->uAntiSpy = $now;

		if ($hInterval > 0) {
			if ($hInterval >= 24) {
				$this->antiSpyAverage = $this->iAntiSpy;
			} else {
				$this->antiSpyAverage = round((($this->antiSpyAverage * (24-$hInterval)) + ($this->iAntiSpy * $hInterval)) / 24);
			}
		}
	}

	public function uCommercialShipping($dUpdate) {
		$S_CSM1 = ASM::$csm->getCurrentSession();
		ASM::$csm->changeSession($this->shippingManager);
		$size = ASM::$csm->size();
		if ($size >= 1) {
			$index = 0;

			while ($index < $size) {
				$cs = ASM::$csm->get($index);
				$index++;
				switch ($cs->statement) {
					case CommercialShipping::ST_GOING :
						if (Utils::hasAlreadyHappened($cs->dArrival, $dUpdate)) {
							# shipping arrived, delivery of items to rBaseDestination
							$cs->deliver();
							# prepare commercialShipping for moving back
							$cs->statement = CommercialShipping::ST_MOVING_BACK;
							$timeToTravel = strtotime($cs->dArrival) - strtotime($cs->dDeparture);
							$cs->dDeparture = $cd->$dArrival;
							$cs->dArrival = Utils::addSecondsToDate($cs->dArrival, $timeToTravel);
						} 
						break;
					case CommercialShipping::ST_MOVING_BACK :
						if (Utils::hasAlreadyHappened($cs->dArrival, $dUpdate)) {
							# shipping arrived, release of the commercial ships
							# send notification
							$n = new Notification();
							$n->setRPlayer($cs->rPlayer);
							$n->setTitle('Retour de livraison');
							$n->addBeg()->addTxt('Vos vaisseaux commerciaux sont de retour sur votre ');
							$n->addLnk('map/base-' . $cs->rBase, 'base orbitale')->addTxt(' après avoir livré du matériel sur une autre ');
							$n->addLnk('map/place-' . $cs->rBaseDestination, 'base')->addTxt(' . ');
							$n->addSep()->addTxt('Vos ' . $cs->shipQuantity . ' vaisseaux de commerces sont à nouveau disponibles pour faire d\'autres transactions ou routes commerciales.');
							$n->addEnd();
							ASM::$ntm->add($n);
							# delete commercialShipping
							ASM::$csm->deleteById($cs->id);
							$index--;
							$size--;
						} 
						break;
					default :
						break;
				}
			}
			ASM::$csm->changeSession($S_CSM1);
			return TRUE;
		} else {
			//pas d'envoi en cours
			ASM::$csm->changeSession($S_CSM1);
			return TRUE;
		}
	}

	// OBJECT METHODS
	public function increaseResources($resources) {
		if (intval($resources)) {
			$maxStorage = OrbitalBaseResource::getBuildingInfo(1, 'level', $this->levelRefinery, 'storageSpace');
			if ($this->isProductionRefinery == 0) {
				$maxStorage += $maxStorage * OBM_COEFPRODUCTION;
			}
			$newResources = $this->resourcesStorage + abs($resources);

			if ($newResources > $maxStorage) {
				$this->resourcesStorage = $maxStorage;
			} else {
				$this->resourcesStorage = $newResources;
			}
		} else {
			CTR::$alert->add('un nombre est requis');
			CTR::$alert->add('dans increaseResources de OrbitalBase', ALERT_BUG_ERROR);
		}
	}

	public function decreaseResources($resources) {
		if (intval($resources)) {
			$this->resourcesStorage -= abs($resources);
		} else {
			CTR::$alert->add('un nombre est requis');
			CTR::$alert->add('dans decreaseResources de OrbitalBase', ALERT_BUG_ERROR);
		}
	}

	public function addShipToDock($shipId, $quantity) {
		if (OrbitalBaseResource::isAShipFromDock1($shipId) OR OrbitalBaseResource::isAShipFromDock2($shipId)) {
			self::setShipStorage($shipId, self::getShipStorage($shipId) + $quantity);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function removeShipFromDock($shipId, $quantity) {
		if (OrbitalBaseResource::isAShipFromDock1($shipId) OR OrbitalBaseResource::isAShipFromDock2($shipId)) {
			if (self::getShipStorage($shipId) >= $quantity) {
				self::setShipStorage($shipId, self::getShipStorage($shipId) - $quantity);
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
}
?>