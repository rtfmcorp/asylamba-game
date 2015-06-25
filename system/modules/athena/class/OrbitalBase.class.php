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
				CTR::$alert->add('Bâtiment invalide dans getBuildingLevel de OrbitalBase', ALERT_BUG_ERROR);
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
				CTR::$alert->add('Bâtiment invalide dans setBuildingLevel de OrbitalBase', ALERT_BUG_ERROR);
		}
	}

	public function updatePoints() {
		$points = 0;

		for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) { 
			for ($j = 0; $j < $this->getBuildingLevel($i); $j++) { 
				$points += OrbitalBaseResource::getBuildingInfo($i, 'level', $j + 1, 'resourcePrice') / 1000;
			}
		}

		$points = round($points);
		$this->setPoints($points);
	}

	// UPDATE METHODS
	public function uMethod() {
		$token = CTC::createContext('orbitalbase');
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

			if (count($hours)) {
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

				if ($cs->dArrival < $now AND $cs->dArrival !== '0000-00-00 00:00:00') {

					$commander = NULL;

					# load transaction (if it's not a resource shipping)
					$S_TRM1 = ASM::$trm->getCurrentSession();
					ASM::$trm->newSession();
					ASM::$trm->load(array('id' => $cs->rTransaction));
					if (ASM::$trm->size() == 1) {
						$transaction = ASM::$trm->get();

						# load commander if it's a commander shipping
						if ($transaction->type == Transaction::TYP_COMMANDER) {
							include_once ARES;
							$S_COM1 = ASM::$com->getCurrentSession();
							ASM::$com->newSession();
							ASM::$com->load(array('c.id' => $transaction->identifier));

							if (ASM::$com->size() == 1) {
								$commander = ASM::$com->get();
							}
							ASM::$com->changeSession($S_COM1);
						}
					} else {
						$transaction = NULL;
					}

					# load destination orbital base
					$S_OBM1 = ASM::$obm->getCurrentSession();
					ASM::$obm->newSession(FALSE);
					ASM::$obm->load(array('rPlace' => $cs->rBaseDestination));
					if (ASM::$obm->size() == 1) {
						$destOB = ASM::$obm->get();
					} else {
						$destOB = NULL;
					}

					CTC::add($cs->dArrival, $this, 'uCommercialShipping', array($cs, $transaction, $destOB, $commander));

					ASM::$obm->changeSession($S_OBM1);
					ASM::$trm->changeSession($S_TRM1);
				}
			}
			ASM::$csm->changeSession($S_CSM1);

			# RECYCLING MISSION
			$S_REM1 = ASM::$rem->getCurrentSession();
			ASM::$rem->newSession(ASM_UMODE);
			ASM::$rem->load(array('rBase' => $this->rPlace, 'statement' => array(RecyclingMission::ST_ACTIVE, RecyclingMission::ST_BEING_DELETED)));
			for ($i = 0; $i < ASM::$rem->size(); $i++) { 
				$mission = ASM::$rem->get($i);

				$interval = Utils::interval($mission->uRecycling, $now, 's');
				if ($interval > $mission->cycleTime) {
					# update time
					$recyclingQuantity = floor($interval / $mission->cycleTime);

					# Place
					include_once GAIA;
					$S_PLM = ASM::$plm->getCurrentSession();
					ASM::$plm->newSession(ASM_UMODE);
					ASM::$plm->load(array('id' => $mission->rTarget));
					$place = ASM::$plm->get();
					ASM::$plm->changeSession($S_PLM);

					for ($j = 0; $j < $recyclingQuantity; $j++) { 
						$dateOfUpdate = Utils::addSecondsToDate($mission->uRecycling, ($j + 1) * $mission->cycleTime);
						CTC::add($dateOfUpdate, $this, 'uRecycling', array($mission, $place, $player, $dateOfUpdate));
					}
				}
			}
			ASM::$rem->changeSession($S_REM1);
		}

		CTC::applyContext($token);
	}

	public function uResources($playerBonus) {
		$addResources = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $this->levelRefinery, 'refiningCoefficient'), $this->planetResources);
		$addResources += $addResources * $playerBonus->bonus->get(PlayerBonus::REFINERY_REFINING) / 100;
		$newResources = $this->resourcesStorage + (int) $addResources;
		$maxStorage = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $this->levelStorage, 'storageSpace');
		$maxStorage += $maxStorage * $playerBonus->bonus->get(PlayerBonus::REFINERY_STORAGE) / 100;

		if ($this->resourcesStorage < $maxStorage) {
			if ($newResources > $maxStorage) {
				$this->resourcesStorage = $maxStorage;
			} else {
				$this->resourcesStorage = $newResources;
			}
		}
	}

	public function uAntiSpy() {
		$this->antiSpyAverage = round((($this->antiSpyAverage * (24 - 1)) + ($this->iAntiSpy)) / 24);
	}

	public function uBuildingQueue($queue, $player) {
		include_once DEMETER;
		# update builded building
		$this->setBuildingLevel($queue->buildingNumber, ($this->getBuildingLevel($queue->buildingNumber) + 1));
		# update the points of the orbitalBase
		$this->updatePoints();
		# increase player experience
		$experience = OrbitalBaseResource::getBuildingInfo($queue->buildingNumber, 'level', $queue->targetLevel, 'points');
		$player->increaseExperience($experience);
		
		# alert
		if (CTR::$data->get('playerId') == $this->rPlayer) {
			CTR::$alert->add('Construction de votre <strong>' . OrbitalBaseResource::getBuildingInfo($queue->buildingNumber, 'frenchName') . ' niveau ' . $queue->targetLevel . '</strong> sur <strong>' . $this->name . '</strong> terminée. Vous gagnez ' . $experience . ' point' . Format::addPlural($experience) . ' d\'expérience.', ALERT_GAM_GENERATOR);
		}
		# delete queue in database
		ASM::$bqm->deleteById($queue->id);
	}

	public function uShipQueue1($sq, $player) {
		include_once DEMETER;
		# vaisseau construit
		$this->setShipStorage($sq->shipNumber, $this->getShipStorage($sq->shipNumber) + $sq->quantity);
		# increase player experience
		$experience = $sq->quantity * ShipResource::getInfo($sq->shipNumber, 'points');
		$player->increaseExperience($experience);

		# alert
		if (CTR::$data->get('playerId') == $this->rPlayer) {
			$alt = 'Construction de ';
			if ($sq->quantity > 1) {
				$alt .= 'vos <strong>' . $sq->quantity . ' ' . ShipResource::getInfo($sq->shipNumber, 'codeName') . 's</strong>';
			} else {
				$alt .= 'votre <strong>' . ShipResource::getInfo($sq->shipNumber, 'codeName') . '</strong>';
			}
			$alt .= ' sur <strong>' . $this->name . '</strong> terminée. Vous gagnez ' . $experience . ' point' . Format::addPlural($experience) . ' d\'expérience.';
			CTR::$alert->add($alt, ALERT_GAM_DOCK1);
		}

		# delete queue in database
		ASM::$sqm->deleteById($sq->id);
	}

	public function uShipQueue2($sq, $player) {
		include_once DEMETER;
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

	public function uCommercialShipping($cs, $transaction, $destOB, $commander) {
		switch ($cs->statement) {
			case CommercialShipping::ST_GOING :
				# shipping arrived, delivery of items to rBaseDestination
				$cs->deliver($transaction, $destOB, $commander);
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
				if ($cs->shipQuantity == 1) {
					$n->addBeg()->addTxt('Votre vaisseau commercial est de retour sur votre ');
				} else {
					$n->addBeg()->addTxt('Vos vaisseaux commerciaux sont de retour sur votre ');
				}
				$n->addLnk('map/base-' . $cs->rBase, 'base orbitale')->addTxt(' après avoir livré du matériel sur une autre ');
				$n->addLnk('map/place-' . $cs->rBaseDestination, 'base')->addTxt(' . ');
				if ($cs->shipQuantity == 1) {
					$n->addSep()->addTxt('Votre vaisseau de commerce est à nouveau disponible pour faire d\'autres transactions ou routes commerciales.');
				} else {
					$n->addSep()->addTxt('Vos ' . $cs->shipQuantity . ' vaisseaux de commerce sont à nouveau disponibles pour faire d\'autres transactions ou routes commerciales.');
				}
				$n->addEnd();
				ASM::$ntm->add($n);
				# delete commercialShipping
				ASM::$csm->deleteById($cs->id);
				break;
			default :break;
		}
	}

	public function uRecycling($mission, $targetPlace, $player, $dateOfUpdate) {
		if ($targetPlace->typeOfPlace != Place::EMPTYZONE) {
			# make the recycling : decrease resources on the target place
			$totalRecycled = $mission->recyclerQuantity * RecyclingMission::RECYCLER_CAPACTIY;
			$targetPlace->resources -= $totalRecycled;
			# if there is no more resource
			if ($targetPlace->resources <= 0) {
				# the place become an empty place
				$targetPlace->resources = 0;
				$targetPlace->typeOfPlace = Place::EMPTYZONE;

				# stop the mission
				$mission->statement = RecyclingMission::ST_DELETED;

				# send notification to the player
				$n = new Notification();
				$n->setRPlayer($player->id);
				$n->setTitle('Arrêt de mission de recyclage');
				$n->addBeg()->addTxt('Un ');
				$n->addLnk('map/place-' . $mission->rTarget, 'lieu');
				$n->addTxt(' que vous recycliez est désormais totalement dépourvu de ressources et s\'est donc transformé en lieu vide.');
				$n->addSep()->addTxt('Vos recycleurs restent donc stationnés sur votre ');
				$n->addLnk('map/base-' . $this->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
				$n->addEnd();
				ASM::$ntm->add($n);
			}

			# if the sector change its color between 2 recyclings
			if ($player->rColor != $targetPlace->sectorColor && $targetPlace->sectorColor != ColorResource::NO_FACTION) {
				# stop the mission
				$mission->statement = RecyclingMission::ST_DELETED;

				# send notification to the player
				$n = new Notification();
				$n->setRPlayer($player->id);
				$n->setTitle('Arrêt de mission de recyclage');
				$n->addBeg()->addTxt('Le secteur d\'un ');
				$n->addLnk('map/place-' . $mission->rTarget, 'lieu');
				$n->addTxt(' que vous recycliez est passé à l\'ennemi, vous ne pouvez donc plus y envoyer vos recycleurs. La mission est annulée.');
				$n->addSep()->addTxt('Vos recycleurs restent donc stationnés sur votre ');
				$n->addLnk('map/base-' . $this->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
				$n->addEnd();
				ASM::$ntm->add($n);
			}

			$creditRecycled = round($targetPlace->population * $totalRecycled / 100);
			$resourceRecycled = round($targetPlace->coefResources * $totalRecycled / 100);
			$shipRecycled = round($targetPlace->coefHistory * $totalRecycled / 100);

			# diversify a little (resource and credit)
			$percent = rand(-5, 5);
			$diffAmount = round($creditRecycled * $percent / 100);
			$creditRecycled += $diffAmount;
			$resourceRecycled -= $diffAmount;

			# convert shipRecycled to real ships
			$pointsToRecycle = round($shipRecycled * RecyclingMission::COEF_SHIP);
			$shipsArray1 = array();
			$buyShip = array();
			for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) { 
				if (floor($pointsToRecycle / ShipResource::getInfo($i, 'resourcePrice')) > 0) {
					$shipsArray1[] = array(
						'ship' => $i,
						'price' => ShipResource::getInfo($i, 'resourcePrice'));
				}
				$buyShip[] = 0;
			}

			shuffle($shipsArray1);
			$shipsArray = array();
			$onlyThree = 0;
			foreach ($shipsArray1 as $key => $value) {
				$onlyThree++;
				$shipsArray[] = $value;
				if ($onlyThree == 3) {
					break;
				}
			}
			$continue = true;
			if (count($shipsArray) > 0) {
				while($continue) {
					foreach ($shipsArray as $key => $line) {
						$nbmax = floor($pointsToRecycle / $line['price']);
						if ($nbmax < 1) {
							$continue = false;
							break;
						}
						$qty = rand(1, $nbmax);
						if ($pointsToRecycle >= $qty * $line['price']) {
							$pointsToRecycle -= $qty * $line['price'];
							$line['buy'] = 1;
							$buyShip[$line['ship']] += $qty;
						} else {
							$continue = false;
							break;
						}
					}
				}
			}

			# create a RecyclingLog
			$rl = new RecyclingLog();
			$rl->rRecycling = $mission->id;
			$rl->resources = $resourceRecycled;
			$rl->credits = $creditRecycled;
			$rl->ship0 = $buyShip[0];
			$rl->ship1 = $buyShip[1];
			$rl->ship2 = $buyShip[2];
			$rl->ship3 = $buyShip[3];
			$rl->ship4 = $buyShip[4];
			$rl->ship5 = $buyShip[5];
			$rl->ship6 = $buyShip[6];
			$rl->ship7 = $buyShip[7];
			$rl->ship8 = $buyShip[8];
			$rl->ship9 = $buyShip[9];
			$rl->ship10 = $buyShip[10];
			$rl->ship11 = $buyShip[11];
			$rl->dLog = Utils::addSecondsToDate($mission->uRecycling, $mission->cycleTime);
			ASM::$rlm->add($rl);

			# give to the orbitalBase ($this) and player what was recycled
			$this->increaseResources($resourceRecycled);
			for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) { 
				$this->addShipToDock($i, $buyShip[$i]);
			}
			$player->increaseCredit($creditRecycled);

			# add recyclers waiting to the mission
			$mission->recyclerQuantity += $mission->addToNextMission;
			$mission->addToNextMission = 0;

			# if a mission is stopped by the user, delete it
			if ($mission->statement == RecyclingMission::ST_BEING_DELETED) {
				$mission->statement = RecyclingMission::ST_DELETED;
			}

			# update u
			$mission->uRecycling = $dateOfUpdate;
		}
	}

	// OBJECT METHODS
	public function increaseResources($resources, $canGoHigher = FALSE) {
		if (intval($resources)) {
			# load the bonus
			$playerBonus = new PlayerBonus($this->rPlayer);
			$playerBonus->load();
			$bonus = $playerBonus->bonus->get(PlayerBonus::REFINERY_STORAGE);

			$newResources = $this->resourcesStorage + (int) $resources;
			
			$maxStorage = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $this->levelStorage, 'storageSpace');
			$maxStorage += $maxStorage * $bonus / 100;

			if ($canGoHigher) {
				$maxStorage += OrbitalBase::EXTRA_STOCK;
			}

			if ($this->resourcesStorage < $maxStorage || $canGoHigher) {
				if ($newResources > $maxStorage) {
					$this->resourcesStorage = $maxStorage;
				} else {
					$this->resourcesStorage = $newResources;
				}
			}
		} else {
			CTR::$alert->add('Problème dans increaseResources de OrbitalBase', ALERT_BUG_ERROR);
		}
	}

	public function decreaseResources($resources) {
		if (intval($resources)) {
			$this->resourcesStorage -= abs($resources);
		} else {
			CTR::$alert->add('Problème dans decreaseResources de OrbitalBase', ALERT_BUG_ERROR);
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
