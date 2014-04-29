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
	# type of base
	const TYP_NEUTRAL = 0;
	const TYP_COMMERCIAL = 1;
	const TYP_MILITARY = 2;
	const TYP_CAPITAL = 3;

	//ATTRIBUTES : ORBITALBASE
	public $rPlace;
	public $rPlayer;
	public $name;
	public $typeOfBase = 0;
	public $levelGenerator = 2;
	public $levelRefinery = 1;
	public $levelDock1 = 1;
	public $levelDock2 = 0;
	public $levelDock3 = 0;
	public $levelTechnosphere = 1;
	public $levelCommercialPlateforme = 0;
	public $levelGravitationalModule = 0;
	public $points = 0;
	public $iSchool = 1000;
	public $iAntiSpy = 0;
	public $antiSpyAverage = 0;
	public $shipStorage = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	public $motherShip = 0; // 1 = a motherShip level 1 is stocked, 2 = level 2, 3 = level 3
	public $isProductionRefinery = 1;
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
	public $realGravitationalModuleLevel;
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
	public function getIAntiSpy() { return $this->iAntiSpy; }
	public function getAntiSpyAverage() { return $this->antiSpyAverage; }
	public function getShipStorage($k = -1) {return ($k == -1) ? $this->shipStorage : $this->shipStorage[$k]; }
	public function getMotherShip() { return $this->motherShip; }
	public function getIsProductionRefinery() { return $this->isProductionRefinery; }
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
	public function setIAntiSpy($var) { $this->iAntiSpy = $var; }
	public function setAntiSpyAverage($var) { $this->antiSpyAverage = $var; }
	public function setShipStorage($k, $v) { $this->shipStorage[$k] = $v; }
	public function setMotherShip($var) { $this->motherShip = $var; }
	public function setIsProductionRefinery($var) { $this->isProductionRefinery = $var; }
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
	public function uMethod() {
		$token = CTC::createContext();
		$now   = Utils::now();

		if (Utils::interval($this->uOrbitalBase, $now, 's') > 0) {
			# update time
			$hours = Utils::intervalDates($now, $this->uOrbitalBase);
			$this->uOrbitalBase = $now;

			# load the player
			$S_PAM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession();
			ASM::$pam->load(array('id' => $this->rPlayer));
			$player = ASM::$pam->get();
			ASM::$pam->changeSession($S_PAM1);

			# load the bonus
			$playerBonus = new PlayerBonus($this->rPlayer);
			$playerBonus->load();

			# RESOURCES
			foreach ($hours as $key => $hour) {
				CTC::add($hour, $this, 'uResources', array($playerBonus));
			}

			# ANTI-SPY
			foreach ($hours as $key => $hour) {
				CTC::add($hour, $this, 'uAntiSpy', array());
			}

			# BUILDING QUEUE
			$S_BQM2 = ASM::$bqm->getCurrentSession();
			ASM::$bqm->changeSession($this->buildingManager);
			for ($i = 0; $i < ASM::$bqm->size(); $i++) { 
				$queue = ASM::$bqm->get($i);

				if ($queue->dEnd < $now) {
					CTC::add($queue->dEnd, $this, 'uBuildingQueue', array($queue, $player));
				} else {
					break;
				}
			}
			ASM::$bqm->changeSession($S_BQM2);

			# SHIP QUEUE DOCK 1
			$S_SQM1 = ASM::$sqm->getCurrentSession();
			ASM::$sqm->changeSession($this->dock1Manager);
			for ($i = 0; $i < ASM::$sqm->size(); $i++) { 
				$sq = ASM::$sqm->get($i);

				if ($sq->dEnd < $now) {
					CTC::add($sq->dEnd, $this, 'uShipQueue1', array($sq, $player));
				} else {
					break;
				}
			} 
			ASM::$sqm->changeSession($S_SQM1);

			# SHIP QUEUE DOCK 2
			$S_SQM1 = ASM::$sqm->getCurrentSession();
			ASM::$sqm->changeSession($this->dock2Manager);
			for ($i = 0; $i < ASM::$sqm->size(); $i++) { 
				$sq = ASM::$sqm->get($i);

				if ($sq->dEnd < $now) {
					CTC::add($sq->dEnd, $this, 'uShipQueue2', array($sq, $player));
				} else {
					break;
				}
			} 
			ASM::$sqm->changeSession($S_SQM1);

			# TECHNOLOGY QUEUE
			$S_TQM1 = ASM::$tqm->getCurrentSession();
			ASM::$tqm->changeSession($this->technoQueueManager);
			for ($i = 0; $i < ASM::$tqm->size(); $i++) { 
				$tq = ASM::$tqm->get($i);

				if ($tq->dEnd < $now) {
					CTC::add($tq->dEnd, $this, 'uTechnologyQueue', array($tq, $player));
				} else {
					break;
				}
			}
			ASM::$tqm->changeSession($S_TQM1);

			# COMMERCIAL SHIPPING
			$S_CSM1 = ASM::$csm->getCurrentSession();
			ASM::$csm->changeSession($this->shippingManager);
			for ($i = 0; $i < ASM::$csm->size(); $i++) { 
				$cs = ASM::$csm->get($i);

				if ($cs->dArrival < $now) {
					CTC::add($cs->dArrival, $this, 'uCommercialShipping', array($cs));
				} 
			}
			ASM::$csm->changeSession($S_CSM1);
		}

		CTC::applyContext($token);
	}

	public function uResources($playerBonus) {
		$addResources = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(1, 'level', $this->levelRefinery, 'refiningCoefficient'), $this->planetResources);
		if ($this->isProductionRefinery == 1) {
			$modeBonus = $addResources * OBM_COEFPRODUCTION;
			$technoBonus = $addResources * $playerBonus->bonus->get(PlayerBonus::REFINERY_REFINING) / 100;
			$addResources += $modeBonus + $technoBonus;
		}
		$newResources = $this->resourcesStorage + (int) $addResources;
		$maxStorage = OrbitalBaseResource::getBuildingInfo(1, 'level', $this->levelRefinery, 'storageSpace');
		if ($this->isProductionRefinery == 0) {
			$modeBonus = $maxStorage * OBM_COEFPRODUCTION;
			$technoBonus = $maxStorage * 
			$playerBonus->bonus->get(PlayerBonus::REFINERY_STORAGE) / 100;
			$maxStorage += $modeBonus + $technoBonus;
		}
		if ($newResources > $maxStorage) {
			$this->resourcesStorage = $maxStorage;
		} else {
			$this->resourcesStorage = $newResources;
		}
	}

	public function uAntiSpy() {
		$this->antiSpyAverage = round((($this->antiSpyAverage * (24 - 1)) + ($this->iAntiSpy)) / 24);
	}

	public function uBuildingQueue($queue, $player) {
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
		ASM::$bqm->deleteById($queue->id);
	}

	public function uShipQueue1($sq, $player) {
		# vaisseau construit
		$this->setShipStorage($sq->shipNumber, $this->getShipStorage($sq->shipNumber) + $sq->quantity);
		# increase player experience
		$experience = $sq->quantity * ShipResource::getInfo($sq->shipNumber, 'points');
		$player->increaseExperience($experience);
		# alert
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
		# delete queue in database
		ASM::$sqm->deleteById($sq->id);
	}

	public function uShipQueue2($sq, $player) {
		# vaisseau construit
		$this->setShipStorage($sq->shipNumber, $this->getShipStorage($sq->shipNumber) + 1);
		# increase player experience
		$experience = ShipResource::getInfo($sq->shipNumber, 'points');
		$player->increaseExperience($experience);
		# alert
		if (CTR::$data->get('playerId') == $this->rPlayer) {
			CTR::$alert->add('Construction de votre ' . ShipResource::getInfo($sq->shipNumber, 'codeName') . ' sur ' . $this->name . ' terminée. Vous gagnez ' . $experience . ' d\'expérience.', ALERT_GAM_DOCK2);
		}
		# delete queue in database
		ASM::$sqm->deleteById($sq->id);
	}

	public function uTechnologyQueue($tq, $player) {
		# technologie construite
		$techno = new Technology($player->getId());
		$techno->setTechnology($tq->technology, $tq->targetLevel);
		# increase player experience
		$experience = TechnologyResource::getInfo($tq->technology, 'points', $tq->targetLevel);
		$player->increaseExperience($experience);
		# alert
		if (CTR::$data->get('playerId') == $this->rPlayer) {
			$alt = 'Développement de votre technologie ' . TechnologyResource::getInfo($tq->technology, 'name');
			if ($tq->targetLevel > 1) {
				$alt .= ' niveau ' . $tq->targetLevel;
			} 
			$alt .= ' terminée. Vous gagnez ' . $experience . ' d\'expérience.';
			CTR::$alert->add($alt, ALERT_GAM_TECHNO);
		}
		# delete queue in database
		ASM::$tqm->deleteById($tq->id);
	}

	public function uCommercialShipping($cs) {
		switch ($cs->statement) {
			case CommercialShipping::ST_GOING :
				# shipping arrived, delivery of items to rBaseDestination
				$cs->deliver();
				# prepare commercialShipping for moving back
				$cs->statement = CommercialShipping::ST_MOVING_BACK;
				$timeToTravel = strtotime($cs->dArrival) - strtotime($cs->dDeparture);
				$cs->dDeparture = $cs->dArrival;
				$cs->dArrival = Utils::addSecondsToDate($cs->dArrival, $timeToTravel);
				break;
			case CommercialShipping::ST_MOVING_BACK :
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
				break;
			default :
				break;
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

	# OLD METHODS :
	/*public function uResources($dUpdate) {
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
	}*/

	/*public function uAntiSpy($now) {
		$hInterval = Utils::interval($now, $this->uAntiSpy, 'h');
		$this->uAntiSpy = $now;

		if ($hInterval > 0) {
			if ($hInterval >= 24) {
				$this->antiSpyAverage = $this->iAntiSpy;
			} else {
				$this->antiSpyAverage = round((($this->antiSpyAverage * (24-$hInterval)) + ($this->iAntiSpy * $hInterval)) / 24);
			}
		}
	}*/

	/*public function uBuildingQueue($dUpdate, $player) {
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
	}*/

	/*public function uShipQueue1($dUpdate, $player) {
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
	}*/

	/*public function uShipQueue2($dUpdate, $player) {
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
	}*/

	/*public function uTechnologyQueue($dUpdate, $player) {
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
	}*/

	/*public function uCommercialShipping($dUpdate) {
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
	}*/
}
?>