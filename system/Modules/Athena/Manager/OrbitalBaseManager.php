<?php

/**
 * Orbital Base Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 02.01.14
*/
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Container\Session;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Database\Database;

use Asylamba\Modules\Gaia\Manager\GalaxyColorManager;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Athena\Manager\BuildingQueueManager;
use Asylamba\Modules\Athena\Manager\ShipQueueManager;
use Asylamba\Modules\Promethee\Manager\TechnologyQueueManager;
use Asylamba\Modules\Athena\Manager\CommercialShippingManager;
use Asylamba\Modules\Athena\Manager\CommercialRouteManager;

use Asylamba\Classes\Container\Alert;

class OrbitalBaseManager extends Manager {
	protected $managerType = '_OrbitalBase';
	/** @var BuildingQueueManager **/
	protected $buildingQueueManager;
	/** @var ShipQueueManager **/
	protected $shipQueueManager;
	/** @var TechnologyQueueManager **/
	protected $technologyQueueManager;
	/** @var CommercialShippingManager **/
	protected $commercialShippingManager;
	/** @var CommercialRouteManager **/
	protected $commercialRouteManager;
	/** @var TransactionManager **/
	protected $transactionManager;
	/** @var GalaxyColorManager **/
	protected $galaxyColorManager;
	/** @var PlayerManager **/
	protected $playerManager;
	/** @var Alert **/
	protected $alert;
	/** @var Session **/
	protected $session;
	
	/**
	 * @param Database $database
	 * @param BuildingQueueManager $buildingQueueManager
	 * @param ShipQueueManager $shipQueueManager
	 * @param TechnologyQueueManager $technologyQueueManager
	 * @param CommercialShippingManager $commercialShippingManager
	 * @param CommercialRouteManager $commercialRouteManager
	 * @param TransactionManager $transactionManager
	 * @param GalaxyColorManager $galaxyColorManager
	 * @param PlayerManager $playerManager
	 * @param Alert $alert
	 * @param Session $session
	 */
	public function __construct(
		Database $database,
		BuildingQueueManager $buildingQueueManager,
		ShipQueueManager $shipQueueManager,
		TechnologyQueueManager $technologyQueueManager,
		CommercialShippingManager $commercialShippingManager,
		CommercialRouteManager $commercialRouteManager,
		TransactionManager $transactionManager,
		GalaxyColorManager $galaxyColorManager,
		PlayerManager $playerManager,
		Alert $alert,
		Session $session
	) {
		parent::__construct($database);
		$this->buildingQueueManager = $buildingQueueManager;
		$this->shipQueueManager = $shipQueueManager;
		$this->technologyQueueManager = $technologyQueueManager;
		$this->commercialShippingManager = $commercialShippingManager;
		$this->commercialRouteManager = $commercialRouteManager;
		$this->transactionManager = $transactionManager;
		$this->galaxyColorManager = $galaxyColorManager;
		$this->playerManager = $playerManager;
		$this->alert = $alert;
		$this->session = $session;
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'ob.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT 
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

		$this->fill($qr);
	}

	public function search($search, $order = array(), $limit = array()) {
		$search = '%' . $search . '%';
		
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT 
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
			WHERE LOWER(name) LIKE LOWER(?)
			' . $formatOrder . '
			' . $formatLimit
		);

		$qr->execute(array($search));

		$this->fill($qr);
	}

	protected function fill($qr) {
		while ($aw = $qr->fetch()) {
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
			$b->setLevelStorage($aw['levelStorage']);
			$b->setLevelRecycling($aw['levelRecycling']);
			$b->setLevelSpatioport($aw['levelSpatioport']);
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

			# BuildingQueueManager
			$oldBQMSess = $this->buildingQueueManager->getCurrentSession();
			$this->buildingQueueManager->newSession(ASM_UMODE);
			$this->buildingQueueManager->load(array('rOrbitalBase' => $aw['rPlace']), array('dEnd', 'ASC'));
			$b->buildingManager = $this->buildingQueueManager->getCurrentSession();
			$size = $this->buildingQueueManager->size();

			$realGeneratorLevel = $aw['levelGenerator'];
			$realRefineryLevel = $aw['levelRefinery'];
			$realDock1Level = $aw['levelDock1'];
			$realDock2Level = $aw['levelDock2'];
			$realDock3Level = $aw['levelDock3'];
			$realTechnosphereLevel = $aw['levelTechnosphere'];
			$realCommercialPlateformeLevel = $aw['levelCommercialPlateforme'];
			$realStorageLevel = $aw['levelStorage'];
			$realRecyclingLevel = $aw['levelRecycling'];
			$realSpatioportLevel = $aw['levelSpatioport'];

			for ($i = 0; $i < $size; $i++) {
				switch ($this->buildingQueueManager->get($i)->buildingNumber) {
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
						$realStorageLevel++;
						break;
					case 8 :
						$realRecyclingLevel++;
						break;
					case 9 :
						$realSpatioportLevel++;
						break;
					default :
						$this->alert->add('Erreur dans la base de données');
						$this->alert->add('dans load() de OrbitalBaseManager', ALT_BUG_ERROR);
				}
			}

			$b->setRealGeneratorLevel($realGeneratorLevel);
			$b->setRealRefineryLevel($realRefineryLevel);
			$b->setRealDock1Level($realDock1Level);
			$b->setRealDock2Level($realDock2Level);
			$b->setRealDock3Level($realDock3Level);
			$b->setRealTechnosphereLevel($realTechnosphereLevel);
			$b->setRealCommercialPlateformeLevel($realCommercialPlateformeLevel);
			$b->setRealStorageLevel($realStorageLevel);
			$b->setRealRecyclingLevel($realRecyclingLevel);
			$b->setRealSpatioportLevel($realSpatioportLevel);
			$this->buildingQueueManager->changeSession($oldBQMSess);

			# ShipQueueManager
			$S_SQM1 = $this->shipQueueManager->getCurrentSession();
			$this->shipQueueManager->newSession(ASM_UMODE);
			$this->shipQueueManager->load(array('rOrbitalBase' => $aw['rPlace'], 'dockType' => 1), array('dEnd'));
			$b->dock1Manager = $this->shipQueueManager->getCurrentSession();
			$this->shipQueueManager->newSession(ASM_UMODE);
			$this->shipQueueManager->load(array('rOrbitalBase' => $aw['rPlace'], 'dockType' => 2), array('dEnd'));
			$b->dock2Manager = $this->shipQueueManager->getCurrentSession();
			$this->shipQueueManager->changeSession($S_SQM1);

			# CommercialRouteManager
			$S_CRM1 = $this->commercialRouteManager->getCurrentSession();
			$this->commercialRouteManager->newSession(ASM_UMODE);
			$this->commercialRouteManager->load(array('rOrbitalBase' => $aw['rPlace']));
			$this->commercialRouteManager->load(array('rOrbitalBaseLinked' => $aw['rPlace']));
			$b->routeManager = $this->commercialRouteManager->getCurrentSession();
			$this->commercialRouteManager->changeSession($S_CRM1);

			# TechnologyQueueManager
			$S_TQM1 = $this->technologyQueueManager->getCurrentSession();
			$this->technologyQueueManager->newSession(ASM_UMODE);
			$this->technologyQueueManager->load(array('rPlace' => $aw['rPlace']), array('dEnd'));
			$b->technoQueueManager = $this->technologyQueueManager->getCurrentSession();
			$this->technologyQueueManager->changeSession($S_TQM1);

			# CommercialShippingManager
			$S_CSM1 = $this->commercialShippingManager->getCurrentSession();
			$this->commercialShippingManager->newSession(ASM_UMODE);
			$this->commercialShippingManager->load(array('rBase' => $aw['rPlace']));
			$this->commercialShippingManager->load(array('rBaseDestination' => $aw['rPlace']));
			$b->shippingManager = $this->commercialShippingManager->getCurrentSession();
			$this->commercialShippingManager->changeSession($S_CSM1);

			$currentB = $this->_Add($b);

			if ($this->currentSession->getUMode()) {
				$currentB->uMethod();
			}
		}
	}

	public function add(OrbitalBase $b) {
		# prépare le rechargement de la map
		$this->galaxyColorManager->apply();

		$qr = $this->database->prepare('INSERT INTO
			orbitalBase(rPlace, rPlayer, name, typeOfBase, levelGenerator, levelRefinery, levelDock1, levelDock2, levelDock3, levelTechnosphere, levelCommercialPlateforme, levelStorage, levelRecycling, levelSpatioport, points,
				iSchool, iAntiSpy, antiSpyAverage, 
				pegaseStorage, satyreStorage, sireneStorage, dryadeStorage, chimereStorage, meduseStorage, griffonStorage, cyclopeStorage, minotaureStorage, hydreStorage, cerbereStorage, phenixStorage,
				resourcesStorage, uOrbitalBase, dCreation)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,  
				?, ?, ?, 
				?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 
				?, ?, ?)');
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
			$b->getLevelStorage(),
			$b->getLevelRecycling(),
			$b->getLevelSpatioport(),
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
			
			$b->getResourcesStorage(),
			$b->uOrbitalBase,
			$b->getDCreation()
		));
		$b->buildingManager = $this->buildingQueueManager->getFirstSession();
		$b->dock1Manager = $this->shipQueueManager->getFirstSession();
		$b->dock2Manager = $this->shipQueueManager->getFirstSession();
		$b->dock3Manager = $this->shipQueueManager->getFirstSession();
		$b->routeManager = $this->commercialRouteManager->getFirstSession();
		$b->technoQueueManager = $this->technologyQueueManager->getFirstSession();
		$b->shippingManager = $this->commercialShippingManager->getFirstSession();

		$this->_Add($b);
	}

	public function save() {
		$bases = $this->_Save();
		foreach ($bases AS $k => $b) {
			$qr = $this->database->prepare('UPDATE orbitalBase
				SET	rPlace = ?, rPlayer = ?, name = ?, typeOfBase = ?, levelGenerator = ?, levelRefinery = ?, levelDock1 = ?, levelDock2 = ?, levelDock3 = ?, levelTechnosphere = ?, levelCommercialPlateforme = ?, levelStorage = ?, levelRecycling = ?, levelSpatioport = ?, points = ?,
			iSchool = ?, iAntiSpy = ?, antiSpyAverage = ?,
			pegaseStorage = ?, satyreStorage = ?, sireneStorage = ?, dryadeStorage = ?, chimereStorage = ?, meduseStorage = ?, griffonStorage = ?, cyclopeStorage = ?, minotaureStorage = ?, hydreStorage = ?, cerbereStorage = ?, phenixStorage = ?,
			resourcesStorage = ?, uOrbitalBase = ?, dCreation = ?
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
				$b->getLevelStorage(),
				$b->getLevelRecycling(),
				$b->getLevelSpatioport(),
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
				$b->getResourcesStorage(),
				$b->uOrbitalBase,
				$b->getDCreation(),
				$b->getRPlace()
			));
		}
	}

	public function changeOwnerById($id, $base, $newOwner, $routeSession, $recyclingSession, $commanderSession) {
		if ($base->getId() != 0) {
			# changement de possesseur des offres du marché
			$S_TRM1 = $this->transactionManager->getCurrentSession();
			$this->transactionManager->newSession(FALSE);
			$this->transactionManager->load(array('rPlayer' => $base->rPlayer, 'rPlace' => $base->rPlace, 'statement' => Transaction::ST_PROPOSED));

			for ($i = 0; $i < $this->transactionManager->size(); $i++) {
				# change owner of transaction
				$this->transactionManager->get($i)->rPlayer = $newOwner;

				$S_CSM1 = $this->commercialShippingManager->getCurrentSession();
				$this->commercialShippingManager->newSession(FALSE);
				$this->commercialShippingManager->load(array('rTransaction' => $this->transactionManager->get($i)->id, 'rPlayer' => $base->rPlayer));
				# change owner of commercial shipping
				$this->commercialShippingManager->get()->rPlayer = $newOwner;
				$this->commercialShippingManager->changeSession($S_CSM1);
			}

			$this->transactionManager->changeSession($S_TRM1);

			# attribuer le rPlayer à la Base
			$oldOwner = $base->rPlayer;
			$base->setRPlayer($newOwner);

			# suppression des routes commerciales
			$S_CRM1 = $this->commercialRouteManager->getCurrentSession();
			$this->commercialRouteManager->changeSession($routeSession);
			for ($i = $this->commercialRouteManager->size()-1; $i >= 0; $i--) { 
				$this->commercialRouteManager->deleteById($this->commercialRouteManager->get($i)->getId());
				# envoyer une notif
			}

			$this->commercialRouteManager->changeSession($S_CRM1);

			# suppression des technologies en cours de développement
			$S_TQM1 = $this->technologyQueueManager->getCurrentSession();
			$this->technologyQueueManager->changeSession($base->technoQueueManager);
			for ($i = $this->technologyQueueManager->size()-1; $i >= 0; $i--) { 
				$this->technologyQueueManager->deleteById($this->technologyQueueManager->get($i)->getId());
			}
			$this->technologyQueueManager->changeSession($S_TQM1);

			# suppression des missions de recyclages ainsi que des logs de recyclages
			$S_REM1 = $this->recyclingMissionManager->getCurrentSession();
			$this->recyclingMissionManager->changeSession($recyclingSession);
			for ($i = $this->recyclingMissionManager->size() - 1; $i >= 0; $i--) {
				$this->recyclingLogManager->deleteAllFromMission($this->recyclingMissionManager->get($i)->id);
				$this->recyclingMissionManager->deleteById($this->recyclingMissionManager->get($i)->id);
			}
			$this->recyclingMissionManager->changeSession($S_REM1);

			# mise des investissements à 0
			$base->iSchool = 0;
			$base->iAntiSpy = 0;

			# mise à jour de la date de création pour qu'elle soit dans l'ordre
			$base->dCreation = Utils::now();

			# ajouter/enlever la base dans le controller
			if ($this->session->get('playerId') == $newOwner) {
				$this->session->addBase('ob', $base->getId(), $base->getName(), $base->getSector(), $base->getSystem(), '1-' . Game::getSizeOfPlanet($base->getPlanetPopulation()), $base->typeOfBase);
			} else {
				$this->session->removeBase('ob', $base->getId());
			}

			# rendre déserteuses les flottes en voyage
			$S_COM4 = $this->commanderManager->getCurrentSession();
			$this->commanderManager->changeSession($commanderSession);
			for ($i = 0; $i < $this->commanderManager->size(); $i++) {
				if (in_array($this->commanderManager->get($i)->statement, [Commander::INSCHOOL, Commander::ONSALE, Commander::RESERVE])) {
					$this->commanderManager->get($i)->rPlayer = $newOwner;
				} else if ($this->commanderManager->get($i)->statement == Commander::MOVING) {
					$this->commanderManager->get($i)->statement = Commander::RETIRED;
				} else {
					$this->commanderManager->get($i)->statement = Commander::DEAD;		
				}
			}
			$this->commanderManager->changeSession($S_COM4);

			# vérifie si le joueur n'a plus de planète, si c'est le cas, il est mort, on lui redonne une planète
			$S_OBM2 = $this->getCurrentSession();
			$this->newSession(FALSE); # FALSE obligatory
			$this->load(array('rPlayer' => $oldOwner));
			if ($this->size() == 0 || ($this->get()->rPlace == $id && $this->size() == 1)) {
				$this->playerManager->reborn($oldOwner);
			}
			$this->changeSession($S_OBM2);

			# applique en cascade le changement de couleur des sytèmes
			$this->galaxyColorManager->apply();
		} else {
			$this->alert->add('Cette base orbitale n\'exite pas !', ALERT_BUG_INFO);
			$this->alert->add('dans changeOwnerById de OrbitalBaseManager', ALERT_BUG_ERROR);
		}
	}
}