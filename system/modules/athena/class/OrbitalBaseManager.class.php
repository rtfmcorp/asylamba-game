<?php
include_once HERMES;
include_once ZEUS;

/**
 * Orbital Base Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 02.01.14
*/

class OrbitalBaseManager extends Manager {
	protected $managerType = '_OrbitalBase';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'ob.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT 
			ob.*,
			p.position AS position,
			p.rSystem AS system,
			s.xPosition AS xSystem,
			s.yPosition AS ySystem,
			s.rSector AS sector,
			se.rColor AS sectorColor,
			se.tax AS tax,
			p.population AS planetPopulation,
			p.coefResources AS planetResources,
			p.coefHistory AS planetHistory,
			(SELECT
				MAX(bq.dEnd) 
				FROM orbitalBaseBuildingQueue AS bq 
				WHERE bq.rOrbitalBase = ob.rPlace)
				AS termDateGenerator,
			(SELECT 
				MAX(sq1.dEnd) 
				FROM orbitalBaseShipQueue AS sq1 
				WHERE sq1.rOrbitalBase = ob.rPlace AND sq1.dockType = 1) 
				AS termDateDock1,
			(SELECT 
				MAX(sq2.dEnd) 
				FROM orbitalBaseShipQueue AS sq2 
				WHERE sq2.rOrbitalBase = ob.rPlace AND sq2.dockType = 2) 
				AS termDateDock2,
			(SELECT 
				MAX(sq3.dEnd) 
				FROM orbitalBaseShipQueue AS sq3
				WHERE sq3.rOrbitalBase = ob.rPlace AND sq3.dockType = 3) 
				AS termDateDock3,
			(SELECT
				COUNT(cr.id)
				FROM commercialRoute AS cr
				WHERE (cr.rOrbitalBase = ob.rPlace OR cr.rOrbitalBaseLinked = ob.rPlace) AND cr.statement = 1)
				AS routesNumber
			FROM orbitalBase AS ob
			LEFT JOIN place AS p
				ON ob.rPlace = p.id
			LEFT JOIN system AS s
				ON p.rSystem = s.id
			LEFT JOIN sector AS se
				ON s.rSector = se.id
			' . $formatWhere . '
			' . $formatOrder . '
			' . $formatLimit
		);
		foreach($where AS $v) {
			$valuesArray[] = $v;
		}
		if(empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}
		while($aw = $qr->fetch()) {
			$b = new OrbitalBase();
			$b->setRPlace($aw['rPlace']);
			$b->setRPlayer($aw['rPlayer']);
			$b->setName($aw['name']);
			$b->typeOfBase = $aw['typeOfBase'];
			$b->setLevelGenerator($aw['levelGenerator']);
			$b->setLevelRefinery($aw['levelRefinery']);
			$b->setLevelDock1($aw['levelDock1']);
			$b->setLevelDock2($aw['levelDock2']);
			$b->setLevelDock3($aw['levelDock3']);
			$b->setLevelTechnosphere($aw['levelTechnosphere']);
			$b->setLevelCommercialPlateforme($aw['levelCommercialPlateforme']);
			$b->setLevelGravitationalModule($aw['levelGravitationalModule']);
			$b->setPoints($aw['points']);
			$b->setISchool($aw['iSchool']);
			$b->setIAntiSpy($aw['iAntiSpy']);
			$b->setAntiSpyAverage($aw['antiSpyAverage']);
			$b->setShipStorage(0 ,$aw['pegaseStorage']);
			$b->setShipStorage(1 ,$aw['satyreStorage']);
			$b->setShipStorage(2 ,$aw['sireneStorage']);
			$b->setShipStorage(3 ,$aw['dryadeStorage']);
			$b->setShipStorage(4 ,$aw['chimereStorage']);
			$b->setShipStorage(5 ,$aw['meduseStorage']);
			$b->setShipStorage(6 ,$aw['griffonStorage']);
			$b->setShipStorage(7 ,$aw['cyclopeStorage']);
			$b->setShipStorage(8 ,$aw['minotaureStorage']);
			$b->setShipStorage(9 ,$aw['hydreStorage']);
			$b->setShipStorage(10 ,$aw['cerbereStorage']);
			$b->setShipStorage(11 ,$aw['phenixStorage']);
			$b->setMotherShip($aw['motherShip']);
			$b->setIsProductionRefinery($aw['isProductionRefinery']);
			$b->setResourcesStorage($aw['resourcesStorage']);
			$b->uOrbitalBase = $aw['uOrbitalBase'];
			$b->setDCreation($aw['dCreation']);

			$b->setPosition($aw['position']);
			$b->setSystem($aw['system']);
			$b->setXSystem($aw['xSystem']);
			$b->setYSystem($aw['ySystem']);
			$b->setSector($aw['sector']);
			$b->sectorColor = $aw['sectorColor'];
			$b->setTax($aw['tax']);
			$b->setPlanetPopulation($aw['planetPopulation']);
			$b->setPlanetResources($aw['planetResources']);
			$b->setPlanetHistory($aw['planetHistory']);

			$generatorTime = strtotime($aw['termDateGenerator']) - strtotime(Utils::now());
			$b->setRemainingTimeGenerator(round($generatorTime, 1));
			$dock1Time = strtotime($aw['termDateDock1']) - strtotime(Utils::now());
			$b->setRemainingTimeDock1(round($dock1Time, 1));
			$dock2Time = strtotime($aw['termDateDock2']) - strtotime(Utils::now());
			$b->setRemainingTimeDock2(round($dock2Time, 1));
			$dock3Time = strtotime($aw['termDateDock3']) - strtotime(Utils::now());
			$b->setRemainingTimeDock3(round($dock3Time, 1));

			$b->setRoutesNumber($aw['routesNumber']);

			// BuildingQueueManager
			$oldBQMSess = ASM::$bqm->getCurrentSession();
			ASM::$bqm->newSession(ASM_UMODE);
			ASM::$bqm->load(array('rOrbitalBase' => $aw['rPlace']), array('dEnd', 'ASC'));
			$b->buildingManager = ASM::$bqm->getCurrentSession();
			$size = ASM::$bqm->size();

			$realGeneratorLevel = $aw['levelGenerator'];
			$realRefineryLevel = $aw['levelRefinery'];
			$realDock1Level = $aw['levelDock1'];
			$realDock2Level = $aw['levelDock2'];
			$realDock3Level = $aw['levelDock3'];
			$realTechnosphereLevel = $aw['levelTechnosphere'];
			$realCommercialPlateformeLevel = $aw['levelCommercialPlateforme'];
			$realGravitationalModuleLevel = $aw['levelGravitationalModule'];
			for ($i = 0; $i < $size; $i++) {
				switch (ASM::$bqm->get($i)->buildingNumber) {
					case 0 :
						$realGeneratorLevel++;
						break;
					case 1 :
						$realRefineryLevel++;
						break;
					case 2 :
						$realDock1Level++;
						break;
					case 3 :
						$realDock2Level++;
						break;
					case 4 :
						$realDock3Level++;
						break;
					case 5 :
						$realTechnosphereLevel++;
						break;
					case 6 :
						$realCommercialPlateformeLevel++;
						break;
					case 7 :
						$realGravitationalModuleLevel++;
						break;
					default :
						CTR::$alert->add('Erreur dans la base de données');
						CTR::$alert->add('dans load() de OrbitalBaseManager', ALT_BUG_ERROR);
				}
			}
			$b->setRealGeneratorLevel($realGeneratorLevel);
			$b->setRealRefineryLevel($realRefineryLevel);
			$b->setRealDock1Level($realDock1Level);
			$b->setRealDock2Level($realDock2Level);
			$b->setRealDock3Level($realDock3Level);
			$b->setRealTechnosphereLevel($realTechnosphereLevel);
			$b->setRealCommercialPlateformeLevel($realCommercialPlateformeLevel);
			$b->setRealGravitationalModuleLevel($realGravitationalModuleLevel);
			ASM::$bqm->changeSession($oldBQMSess);

			// ShipQueueManager
			$S_SQM1 = ASM::$sqm->getCurrentSession();
			ASM::$sqm->newSession(ASM_UMODE);
			ASM::$sqm->load(array('rOrbitalBase' => $aw['rPlace'], 'dockType' => 1), array('dEnd'));
			$b->dock1Manager = ASM::$sqm->getCurrentSession();
			ASM::$sqm->newSession(ASM_UMODE);
			ASM::$sqm->load(array('rOrbitalBase' => $aw['rPlace'], 'dockType' => 2), array('dEnd'));
			$b->dock2Manager = ASM::$sqm->getCurrentSession();
			ASM::$sqm->newSession(ASM_UMODE);
			ASM::$sqm->load(array('rOrbitalBase' => $aw['rPlace'], 'dockType' => 3), array('dEnd'));
			$b->dock3Manager = ASM::$sqm->getCurrentSession();
			ASM::$sqm->changeSession($S_SQM1);

			// CommercialRouteManager
			$S_CRM1 = ASM::$crm->getCurrentSession();
			ASM::$crm->newSession(ASM_UMODE);
			ASM::$crm->load(array('rOrbitalBase' => $aw['rPlace']));
			ASM::$crm->load(array('rOrbitalBaseLinked' => $aw['rPlace']));
			$b->routeManager = ASM::$crm->getCurrentSession();
			ASM::$crm->changeSession($S_CRM1);

			// TechnologyQueueManager
			include_once PROMETHEE;
			$S_TQM1 = ASM::$tqm->getCurrentSession();
			ASM::$tqm->newSession(ASM_UMODE);
			ASM::$tqm->load(array('rPlace' => $aw['rPlace']), array('dEnd'));
			$b->technoQueueManager = ASM::$tqm->getCurrentSession();
			ASM::$tqm->changeSession($S_TQM1);

			// CommercialShippingManager
			$S_CSM1 = ASM::$csm->getCurrentSession();
			ASM::$csm->newSession(ASM_UMODE);
			ASM::$csm->load(array('rBase' => $aw['rPlace']));
			ASM::$csm->load(array('rBaseDestination' => $aw['rPlace']));
			$b->shippingManager = ASM::$csm->getCurrentSession();
			ASM::$csm->changeSession($S_CSM1);

			$currentB = $this->_Add($b);

			// U mechanism
			if ($this->currentSession->getUMode()) {
				$currentB->uMethod();
			}
		}
	}

	public function add(OrbitalBase $b) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			orbitalBase(rPlace, rPlayer, name, typeOfBase, levelGenerator, levelRefinery, levelDock1, levelDock2, levelDock3, levelTechnosphere, levelCommercialPlateforme, levelGravitationalModule, points,
				iSchool, iAntiSpy, antiSpyAverage, 
				pegaseStorage, satyreStorage, sireneStorage, dryadeStorage, chimereStorage, meduseStorage, griffonStorage, cyclopeStorage, minotaureStorage, hydreStorage, cerbereStorage, phenixStorage,
				motherShip, isProductionRefinery, resourcesStorage, uOrbitalBase, dCreation)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  
				?, ?, ?, 
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
				?, ?, ?, ?, ?)');
		$qr->execute(array(
			$b->getRPlace(),
			$b->getRPlayer(),
			$b->getName(),
			$b->typeOfBase,
			$b->getLevelGenerator(),
			$b->getLevelRefinery(),
			$b->getLevelDock1(),
			$b->getLevelDock2(),
			$b->getLevelDock3(),
			$b->getLevelTechnosphere(),
			$b->getLevelCommercialPlateforme(),
			$b->getLevelGravitationalModule(),
			$b->getPoints(),

			$b->getISchool(),
			$b->getIAntiSpy(),
			$b->getAntiSpyAverage(),

			$b->getShipStorage(0),
			$b->getShipStorage(1),
			$b->getShipStorage(2),
			$b->getShipStorage(3),
			$b->getShipStorage(4),
			$b->getShipStorage(5),
			$b->getShipStorage(6),
			$b->getShipStorage(7),
			$b->getShipStorage(8),
			$b->getShipStorage(9),
			$b->getShipStorage(10),
			$b->getShipStorage(11),
			
			$b->getMotherShip(),
			$b->getIsProductionRefinery(),
			$b->getResourcesStorage(),
			$b->uOrbitalBase,
			$b->getDCreation()
		));
		$b->setId($db->lastInsertId());
		$this->_Add($b);
	}

	public function save() {
		$bases = $this->_Save();
		foreach ($bases AS $k => $b) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE orbitalBase
				SET	rPlace = ?, rPlayer = ?, name = ?, typeOfBase = ?, levelGenerator = ?, levelRefinery = ?, levelDock1 = ?, levelDock2 = ?, levelDock3 = ?, levelTechnosphere = ?, levelCommercialPlateforme = ?, levelGravitationalModule = ?, points = ?,
			iSchool = ?, iAntiSpy = ?, antiSpyAverage = ?,
			pegaseStorage = ?, satyreStorage = ?, sireneStorage = ?, dryadeStorage = ?, chimereStorage = ?, meduseStorage = ?, griffonStorage = ?, cyclopeStorage = ?, minotaureStorage = ?, hydreStorage = ?, cerbereStorage = ?, phenixStorage = ?,
			motherShip = ?, isProductionRefinery = ?, resourcesStorage = ?, uOrbitalBase = ?, dCreation = ?
				WHERE rPlace = ?');
			$qr->execute(array(
				$b->getRPlace(),
				$b->getRPlayer(),
				$b->getName(),
				$b->typeOfBase,
				$b->getLevelGenerator(),
				$b->getLevelRefinery(),
				$b->getLevelDock1(),
				$b->getLevelDock2(),
				$b->getLevelDock3(),
				$b->getLevelTechnosphere(),
				$b->getLevelCommercialPlateforme(),
				$b->getLevelGravitationalModule(),
				$b->getPoints(),
				$b->getISchool(),
				$b->getIAntiSpy(),
				$b->getAntiSpyAverage(),
				$b->getShipStorage(0),
				$b->getShipStorage(1),
				$b->getShipStorage(2),
				$b->getShipStorage(3),
				$b->getShipStorage(4),
				$b->getShipStorage(5),
				$b->getShipStorage(6),
				$b->getShipStorage(7),
				$b->getShipStorage(8),
				$b->getShipStorage(9),
				$b->getShipStorage(10),
				$b->getShipStorage(11),
				$b->getMotherShip(),
				$b->getIsProductionRefinery(),
				$b->getResourcesStorage(),
				$b->uOrbitalBase,
				$b->getDCreation(),
				$b->getRPlace()
			));
		}
	}

	public function changeOwnerById($id, $newOwner) {
		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession();
		ASM::$obm->load(array('rPlace' => $id));
		$base = ASM::$obm->get();

		if (ASM::$obm->size() > 0) {
			# attribuer le rPlayer à la Base
			$base->setRPlayer($newOwner);

			# suppression des routes commerciales
			$S_CRM1 = ASM::$crm->getCurrentSession();
			ASM::$crm->newSession();
			ASM::$crm->load(array('rOrbitalBase' => $base->getRPlace()));
			ASM::$crm->load(array('rOrbitalBaseLinked' => $base->getRPlace()));
			for ($i = 0; $i < ASM::$crm->size(); $i++) { 
				ASM::$crm->deleteById(ASM::$crm->get($i)->getId());
				# envoyer une notif
			}
			ASM::$crm->changeSession($S_CRM1);

			# ajoutet/enlever la base dans le controller
			if (CTR::$data->get('playerId') == $newOwner) {
				CTRHelper::addBase('ob', $base->getId(), $base->getName(), $base->getSector(), $base->getSystem());
			} else {
				CTRHelper::removeBase('ob', $base->getId());
			}

			# rendre déserteuses les flottes en voyage
			$S_COM2 = ASM::$com->getCurrentSession();
			ASM::$com->newSession();
			ASM::$com->load(array('c.rBase' => $id));
			for ($i = 0; $i < ASM::$com->size(); $i++) {
				if ($com->statement != Commander::DEAD) {
					$com->rPlayer = $newOwner;
				}
				if ($com->statement == Commander::MOVING) {
					$com->statement = Commander::DESERT;
				}
				$com->rDestinationPlace = NULL;
				$com->travelType = NULL;
				$com->travelLength = NULL;
				$com->rStartPlace = NULL;
				$com->dArrival = NULL;
				$com->dstart = NULL;
				$com->length = NULL;
				$com->rBase = NULL;
			}
			ASM::$com->changeSession($S_COM2);

			# applique en cascade le changement de couleur des sytèmes
			GalaxyColorManager::apply();

		} else {
			CTR::$alert->add('Cette base orbitale n\'exite pas !', ALERT_BUG_INFO);
			CTR::$alert->add('dans changeOwnerById de OrbitalBaseManager', ALERT_BUG_ERROR);

		}
		ASM::$obm->changeSession($S_OBM1);
	}
}
?>