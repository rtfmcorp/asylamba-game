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

use Asylamba\Classes\Container\Session;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Worker\CTC;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Gaia\Manager\GalaxyColorManager;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Gaia\Model\System;
use Asylamba\Modules\Athena\Manager\BuildingQueueManager;
use Asylamba\Modules\Athena\Manager\ShipQueueManager;
use Asylamba\Modules\Promethee\Manager\TechnologyQueueManager;
use Asylamba\Modules\Promethee\Manager\TechnologyManager;
use Asylamba\Modules\Athena\Manager\CommercialShippingManager;
use Asylamba\Modules\Athena\Manager\CommercialRouteManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Manager\PlayerBonusManager;
use Asylamba\Modules\Athena\Manager\RecyclingMissionManager;
use Asylamba\Modules\Athena\Manager\RecyclingLogManager;
use Asylamba\Modules\Gaia\Manager\PlaceManager;
use Asylamba\Modules\Ares\Manager\CommanderManager;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Athena\Helper\OrbitalBaseHelper;
use Asylamba\Modules\Demeter\Resource\ColorResource;

use Asylamba\Modules\Athena\Model\RecyclingMission;
use Asylamba\Modules\Athena\Model\RecyclingLog;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Hermes\Model\Notification;

use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Promethee\Helper\TechnologyHelper;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Classes\Library\Flashbag;

class OrbitalBaseManager {
	/** @var EntityManager **/
	protected $entityManager;
	/** @var BuildingQueueManager **/
	protected $buildingQueueManager;
	/** @var ShipQueueManager **/
	protected $shipQueueManager;
	/** @var TechnologyQueueManager **/
	protected $technologyQueueManager;
	/** @var TechnologyManager **/
	protected $technologyManager;
	/** @var TechnologyHelper **/
	protected $technologyHelper;
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
	/** @var PlayerBonusManager **/
	protected $playerBonusManager;
	/** @var RecyclingMissionManager **/
	protected $recyclingMissionManager;
	/** @var RecyclingLogManager **/
	protected $recyclingLogManager;
	/** @var PlaceManager **/
	protected $placeManager;
	/** @var CommanderManager **/
	protected $commanderManager;
	/** @var NotificationManager **/
	protected $notificationManager;
	/** @var OrbitalBaseHelper **/
	protected $orbitalBaseHelper;
	/** @var CTC **/
	protected $ctc;
	/** @var Session **/
	protected $session;
	
	/**
	 * @param EntityManager $entityManager
	 * @param BuildingQueueManager $buildingQueueManager
	 * @param ShipQueueManager $shipQueueManager
	 * @param TechnologyQueueManager $technologyQueueManager
	 * @param TechnologyHelper $technologyHelper
	 * @param CommercialShippingManager $commercialShippingManager
	 * @param CommercialRouteManager $commercialRouteManager
	 * @param TransactionManager $transactionManager
	 * @param GalaxyColorManager $galaxyColorManager
	 * @param PlayerManager $playerManager
	 * @param PlayerBonusManager $playerBonusManager
	 * @param RecyclingMissionManager $recyclingMissionManager
	 * @param PlaceManager $placeManager
	 * @param CommanderManager $commanderManager
	 * @param NotificationManager $notificationManager
	 * @param OrbitalBaseHelper $orbitalBaseHelper
	 * @param CTC $ctc
	 * @param Session $session
	 */
	public function __construct(
		EntityManager $entityManager,
		BuildingQueueManager $buildingQueueManager,
		ShipQueueManager $shipQueueManager,
		TechnologyQueueManager $technologyQueueManager,
		TechnologyManager $technologyManager,
		TechnologyHelper $technologyHelper,
		CommercialShippingManager $commercialShippingManager,
		CommercialRouteManager $commercialRouteManager,
		TransactionManager $transactionManager,
		GalaxyColorManager $galaxyColorManager,
		PlayerManager $playerManager,
		PlayerBonusManager $playerBonusManager,
		RecyclingMissionManager $recyclingMissionManager,
		RecyclingLogManager $recyclingLogManager,
		PlaceManager $placeManager,
		CommanderManager $commanderManager,
		NotificationManager $notificationManager,
		OrbitalBaseHelper $orbitalBaseHelper,
		CTC $ctc,
		Session $session
	) {
		$this->entityManager = $entityManager;
		$this->buildingQueueManager = $buildingQueueManager;
		$this->shipQueueManager = $shipQueueManager;
		$this->technologyQueueManager = $technologyQueueManager;
		$this->technologyManager = $technologyManager;
		$this->technologyHelper = $technologyHelper;
		$this->commercialShippingManager = $commercialShippingManager;
		$this->commercialRouteManager = $commercialRouteManager;
		$this->transactionManager = $transactionManager;
		$this->galaxyColorManager = $galaxyColorManager;
		$this->playerManager = $playerManager;
		$this->playerBonusManager = $playerBonusManager;
		$this->recyclingMissionManager = $recyclingMissionManager;
		$this->recyclingLogManager = $recyclingLogManager;
		$this->placeManager = $placeManager;
		$this->commanderManager = $commanderManager;
		$this->notificationManager = $notificationManager;
		$this->orbitalBaseHelper = $orbitalBaseHelper;
		$this->ctc = $ctc;
		$this->session = $session;
	}
	
	/**
	 * @param int $id
	 * @return OrbitalBase
	 */
	public function get($id)
	{
		if (($orbitalBase = $this->entityManager->getRepository(OrbitalBase::class)->get($id)) !== null) {
			$this->fill($orbitalBase);
		}
		return $orbitalBase;
	}
	
	/**
	 * @param int $baseId
	 * @param int $playerId
	 * @return OrbitalBase
	 */
	public function getPlayerBase($baseId, $playerId)
	{
		$orbitalBase = $this->entityManager->getRepository(OrbitalBase::class)->getPlayerBase($baseId, $playerId);
		$this->fill($orbitalBase);
		return $orbitalBase;
	}
	
	/**
	 * @param int $playerId
	 * @return array
	 */
	public function getPlayerBases($playerId)
	{
		$bases = $this->entityManager->getRepository(OrbitalBase::class)->getPlayerBases($playerId);
		foreach($bases as $base) {
			$this->fill($base);
		}
		return $bases;
	}
	
	/**
	 * @param int $sectorId
	 * @return array
	 */
	public function getSectorBases($sectorId)
	{
		$bases = $this->entityManager->getRepository(OrbitalBase::class)->getSectorBases($sectorId);
		foreach($bases as $base) {
			$this->fill($base);
		}
		return $bases;
	}
	
	/**
	 * @param System $system
	 * @return array
	 */
	public function getSystemBases(System $system)
	{
		$bases = $this->entityManager->getRepository(OrbitalBase::class)->getSystemBases($system->getId());
		foreach($bases as $base) {
			$this->fill($base);
		}
		return $bases;
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

	/**
	 * 
	 * @param OrbitalBase $orbitalBase
	 * @throws ErrorException
	 */
	protected function fill(OrbitalBase $orbitalBase) {
		# BuildingQueueManager
		$oldBQMSess = $this->buildingQueueManager->getCurrentSession();
		$this->buildingQueueManager->newSession(ASM_UMODE);
		$this->buildingQueueManager->load(array('rOrbitalBase' => $orbitalBase->getRPlace()), array('dEnd', 'ASC'));
		$orbitalBase->buildingManager = $this->buildingQueueManager->getCurrentSession();
		$size = $this->buildingQueueManager->size();

		$realGeneratorLevel = $orbitalBase->getLevelGenerator();
		$realRefineryLevel = $orbitalBase->getLevelRefinery();
		$realDock1Level = $orbitalBase->getLevelDock1();
		$realDock2Level = $orbitalBase->getLevelDock2();
		$realDock3Level = $orbitalBase->getLevelDock3();
		$realTechnosphereLevel = $orbitalBase->getLevelTechnosphere();
		$realCommercialPlateformeLevel = $orbitalBase->getLevelCommercialPlateforme();
		$realStorageLevel = $orbitalBase->getLevelStorage();
		$realRecyclingLevel = $orbitalBase->getLevelRecycling();
		$realSpatioportLevel = $orbitalBase->getLevelSpatioport();

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
					throw new ErrorException('Erreur dans la base de données dans load() de OrbitalBaseManager');
			}
		}

		$orbitalBase->setRealGeneratorLevel($realGeneratorLevel);
		$orbitalBase->setRealRefineryLevel($realRefineryLevel);
		$orbitalBase->setRealDock1Level($realDock1Level);
		$orbitalBase->setRealDock2Level($realDock2Level);
		$orbitalBase->setRealDock3Level($realDock3Level);
		$orbitalBase->setRealTechnosphereLevel($realTechnosphereLevel);
		$orbitalBase->setRealCommercialPlateformeLevel($realCommercialPlateformeLevel);
		$orbitalBase->setRealStorageLevel($realStorageLevel);
		$orbitalBase->setRealRecyclingLevel($realRecyclingLevel);
		$orbitalBase->setRealSpatioportLevel($realSpatioportLevel);
		$this->buildingQueueManager->changeSession($oldBQMSess);

		# ShipQueueManager
		$S_SQM1 = $this->shipQueueManager->getCurrentSession();
		$this->shipQueueManager->newSession(ASM_UMODE);
		$this->shipQueueManager->load(array('rOrbitalBase' => $orbitalBase->getRPlace(), 'dockType' => 1), array('dEnd'));
		$orbitalBase->dock1Manager = $this->shipQueueManager->getCurrentSession();
		$this->shipQueueManager->newSession(ASM_UMODE);
		$this->shipQueueManager->load(array('rOrbitalBase' => $orbitalBase->getRPlace(), 'dockType' => 2), array('dEnd'));
		$orbitalBase->dock2Manager = $this->shipQueueManager->getCurrentSession();
		$this->shipQueueManager->changeSession($S_SQM1);
		# TechnologyQueueManager
		$S_TQM1 = $this->technologyQueueManager->getCurrentSession();
		$this->technologyQueueManager->newSession(ASM_UMODE);
		$this->technologyQueueManager->load(array('rPlace' => $orbitalBase->getRPlace()), array('dEnd'));
		$orbitalBase->technoQueueManager = $this->technologyQueueManager->getCurrentSession();
		$this->technologyQueueManager->changeSession($S_TQM1);

		# CommercialShippingManager
		$S_CSM1 = $this->commercialShippingManager->getCurrentSession();
		$this->commercialShippingManager->newSession(ASM_UMODE);
		$this->commercialShippingManager->load(array('rBase' => $orbitalBase->getRPlace()));
		$this->commercialShippingManager->load(array('rBaseDestination' => $orbitalBase->getRPlace()));
		$orbitalBase->shippingManager = $this->commercialShippingManager->getCurrentSession();
		$this->commercialShippingManager->changeSession($S_CSM1);

		$this->uMethod($orbitalBase);
	}

	public function add(OrbitalBase $orbitalBase) {
		# prépare le rechargement de la map
		$this->galaxyColorManager->apply();

		$this->entityManager->persist($orbitalBase);
		$this->entityManager->flush($orbitalBase);
		
		$orbitalBase->buildingManager = $this->buildingQueueManager->getFirstSession();
		$orbitalBase->dock1Manager = $this->shipQueueManager->getFirstSession();
		$orbitalBase->dock2Manager = $this->shipQueueManager->getFirstSession();
		$orbitalBase->dock3Manager = $this->shipQueueManager->getFirstSession();
		$orbitalBase->technoQueueManager = $this->technologyQueueManager->getFirstSession();
		$orbitalBase->shippingManager = $this->commercialShippingManager->getFirstSession();
	}

	public function changeOwnerById($id, $base, $newOwner, $recyclingSession, $baseCommanders) {
		if ($base->getId() == 0) {
			throw new ErrorException('Cette base orbitale n\'existe pas !');
		}
		# changement de possesseur des offres du marché
		$transactions = $this->transactionManager->getBasePropositions($base->rPlace);

		foreach ($transactions as $transaction) {
			# change owner of transaction
			$transaction->rPlayer = $newOwner;

			$S_CSM1 = $this->commercialShippingManager->getCurrentSession();
			$this->commercialShippingManager->newSession(FALSE);
			$this->commercialShippingManager->load(array('rTransaction' => $transaction->id, 'rPlayer' => $base->rPlayer));
			# change owner of commercial shipping
			$this->commercialShippingManager->get()->rPlayer = $newOwner;
			$this->commercialShippingManager->changeSession($S_CSM1);
		}

		# attribuer le rPlayer à la Base
		$oldOwner = $base->rPlayer;
		$base->setRPlayer($newOwner);

		# suppression des routes commerciales
		$this->commercialRouteManager->removeBaseRoutes($base);

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
		foreach ($baseCommanders as $commander) {
			if (in_array($commander->statement, [Commander::INSCHOOL, Commander::ONSALE, Commander::RESERVE])) {
				$commander->rPlayer = $newOwner;
			} else if ($commander->statement == Commander::MOVING) {
				$commander->statement = Commander::RETIRED;
			} else {
				$commander->statement = Commander::DEAD;		
			}
		}

		# vérifie si le joueur n'a plus de planète, si c'est le cas, il est mort, on lui redonne une planète
		$oldPlayerBases = $this->getPlayerBases($oldOwner);
		$nbOldPlayerBases = count($oldPlayerBases);
		if ($nbOldPlayerBases === 0 || ($nbOldPlayerBases === 1 && $oldPlayerBases[0]->rPlace === $id)) {
			$this->playerManager->reborn($oldOwner);
		}
		# applique en cascade le changement de couleur des sytèmes
		$this->galaxyColorManager->apply();
        $this->entityManager->flush();
	}
	

	public function updatePoints(OrbitalBase $orbitalBase) {
		$points = 0;

		for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) { 
			for ($j = 0; $j < $orbitalBase->getBuildingLevel($i); $j++) { 
				$points += $this->orbitalBaseHelper->getBuildingInfo($i, 'level', $j + 1, 'resourcePrice') / 1000;
			}
		}

		$points = round($points);
		$orbitalBase->setPoints($points);
		
		$this->entityManager->flush($orbitalBase);
	}

	// UPDATE METHODS
	public function uMethod(OrbitalBase $orbitalBase) {
		$token = $this->ctc->createContext('orbitalbase');
		$now   = Utils::now();

		if (Utils::interval($orbitalBase->uOrbitalBase, $now, 's') > 0) {
			# update time
			$hours = Utils::intervalDates($now, $orbitalBase->uOrbitalBase);
			$orbitalBase->uOrbitalBase = $now;

			# load the player
			$player = $this->playerManager->get($orbitalBase->rPlayer);

			if (count($hours)) {
				# load the bonus
				$playerBonus = $this->playerBonusManager->getBonusByPlayer($orbitalBase->rPlayer);
				$this->playerBonusManager->load($playerBonus);

				# RESOURCES
				foreach ($hours as $key => $hour) {
					$this->ctc->add($hour, $this, 'uResources', $orbitalBase, array($orbitalBase, $playerBonus));
				}

				# ANTI-SPY
				foreach ($hours as $key => $hour) {
					$this->ctc->add($hour, $this, 'uAntiSpy', $orbitalBase, array($orbitalBase));
				}
			}

			# BUILDING QUEUE
			$S_BQM2 = $this->buildingQueueManager->getCurrentSession();
			$this->buildingQueueManager->changeSession($orbitalBase->buildingManager);
			for ($i = 0; $i < $this->buildingQueueManager->size(); $i++) { 
				$queue = $this->buildingQueueManager->get($i);

				if ($queue->dEnd < $now) {
					$this->ctc->add($queue->dEnd, $this, 'uBuildingQueue', $orbitalBase, array($orbitalBase, $queue, $player));
				} else {
					break;
				}
			}
			$this->buildingQueueManager->changeSession($S_BQM2);

			# SHIP QUEUE DOCK 1
			$S_SQM1 = $this->shipQueueManager->getCurrentSession();
			$this->shipQueueManager->changeSession($orbitalBase->dock1Manager);
			for ($i = 0; $i < $this->shipQueueManager->size(); $i++) { 
				$sq = $this->shipQueueManager->get($i);

				if ($sq->dEnd < $now) {
					$this->ctc->add($sq->dEnd, $this, 'uShipQueue1', $orbitalBase, array($orbitalBase, $sq, $player));
				} else {
					break;
				}
			} 
			$this->shipQueueManager->changeSession($S_SQM1);

			# SHIP QUEUE DOCK 2
			$S_SQM1 = $this->shipQueueManager->getCurrentSession();
			$this->shipQueueManager->changeSession($orbitalBase->dock2Manager);
			for ($i = 0; $i < $this->shipQueueManager->size(); $i++) { 
				$sq = $this->shipQueueManager->get($i);

				if ($sq->dEnd < $now) {
					$this->ctc->add($sq->dEnd, $this, 'uShipQueue2', $orbitalBase, array($orbitalBase, $sq, $player));
				} else {
					break;
				}
			} 
			$this->shipQueueManager->changeSession($S_SQM1);

			# TECHNOLOGY QUEUE
			$S_TQM1 = $this->technologyQueueManager->getCurrentSession();
			$this->technologyQueueManager->changeSession($orbitalBase->technoQueueManager);
			for ($i = 0; $i < $this->technologyQueueManager->size(); $i++) { 
				$tq = $this->technologyQueueManager->get($i);

				if ($tq->dEnd < $now) {
					$this->ctc->add($tq->dEnd, $this, 'uTechnologyQueue', $orbitalBase, array($orbitalBase, $tq, $player));
				} else {
					break;
				}
			}
			$this->technologyQueueManager->changeSession($S_TQM1);

			# COMMERCIAL SHIPPING
			$S_CSM1 = $this->commercialShippingManager->getCurrentSession();
			$this->commercialShippingManager->changeSession($orbitalBase->shippingManager);
			for ($i = 0; $i < $this->commercialShippingManager->size(); $i++) { 
				$cs = $this->commercialShippingManager->get($i);

				if ($cs->dArrival < $now AND $cs->dArrival !== '0000-00-00 00:00:00') {
					$commander = NULL;

					# load transaction (if it's not a resource shipping)
					if (($transaction = $this->transactionManager->get($cs->rTransaction)) !== null) {
						# load commander if it's a commander shipping
						if ($transaction->type == Transaction::TYP_COMMANDER) {
							$commander = $this->commanderManager->get($transaction->identifier);
						}
					}
					$destOB = $this->get($cs->rBaseDestination);

					$this->ctc->add($cs->dArrival, $this, 'uCommercialShipping', $orbitalBase, array($orbitalBase, $cs, $transaction, $destOB, $commander));
				}
			}
			$this->commercialShippingManager->changeSession($S_CSM1);

			# RECYCLING MISSION
			$S_REM1 = $this->recyclingMissionManager->getCurrentSession();
			$this->recyclingMissionManager->newSession(ASM_UMODE);
			$this->recyclingMissionManager->load(array('rBase' => $orbitalBase->rPlace, 'statement' => array(RecyclingMission::ST_ACTIVE, RecyclingMission::ST_BEING_DELETED)));
			for ($i = 0; $i < $this->recyclingMissionManager->size(); $i++) { 
				$mission = $this->recyclingMissionManager->get($i);

				$interval = Utils::interval($mission->uRecycling, $now, 's');
				if ($interval > $mission->cycleTime) {
					# update time
					$recyclingQuantity = floor($interval / $mission->cycleTime);

					# Place
					$place = $this->placeManager->get($mission->rTarget);

					for ($j = 0; $j < $recyclingQuantity; $j++) { 
						$dateOfUpdate = Utils::addSecondsToDate($mission->uRecycling, ($j + 1) * $mission->cycleTime);
						$this->ctc->add($dateOfUpdate, $this, 'uRecycling', $mission, array($orbitalBase, $mission, $place, $player, $dateOfUpdate));
					}
				}
			}
			$this->recyclingMissionManager->changeSession($S_REM1);
		}

		$this->ctc->applyContext($token);
		$this->entityManager->flush($orbitalBase);
	}

	public function uResources(OrbitalBase $orbitalBase, PlayerBonus $playerBonus) {
		$addResources = Game::resourceProduction($this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $orbitalBase->levelRefinery, 'refiningCoefficient'), $orbitalBase->planetResources);
		$addResources += $addResources * $playerBonus->bonus->get(PlayerBonus::REFINERY_REFINING) / 100;
		$newResources = $orbitalBase->resourcesStorage + (int) $addResources;
		$maxStorage = $this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $orbitalBase->levelStorage, 'storageSpace');
		$maxStorage += $maxStorage * $playerBonus->bonus->get(PlayerBonus::REFINERY_STORAGE) / 100;

		if ($orbitalBase->resourcesStorage < $maxStorage) {
			if ($newResources > $maxStorage) {
				$orbitalBase->resourcesStorage = $maxStorage;
			} else {
				$orbitalBase->resourcesStorage = $newResources;
			}
		}
	}

	public function uAntiSpy(OrbitalBase $orbitalBase) {
		$orbitalBase->antiSpyAverage = round((($orbitalBase->antiSpyAverage * (24 - 1)) + ($orbitalBase->iAntiSpy)) / 24);
	}

	public function uBuildingQueue(OrbitalBase $orbitalBase, $queue, $player) {
		# update builded building
		$orbitalBase->setBuildingLevel($queue->buildingNumber, ($orbitalBase->getBuildingLevel($queue->buildingNumber) + 1));
		# update the points of the orbitalBase
		$this->updatePoints($orbitalBase);
		# increase player experience
		$experience = $this->orbitalBaseHelper->getBuildingInfo($queue->buildingNumber, 'level', $queue->targetLevel, 'points');
		$this->playerManager->increaseExperience($player, $experience);
		
		# alert
		if ($this->session->get('playerId') == $orbitalBase->rPlayer) {
			$this->session->addFlashbag('Construction de votre <strong>' . $this->orbitalBaseHelper->getBuildingInfo($queue->buildingNumber, 'frenchName') . ' niveau ' . $queue->targetLevel . '</strong> sur <strong>' . $orbitalBase->name . '</strong> terminée. Vous gagnez ' . $experience . ' point' . Format::addPlural($experience) . ' d\'expérience.', Flashbag::TYPE_GENERATOR_SUCCESS);
		}
		# delete queue in database
		$this->buildingQueueManager->deleteById($queue->id);
	}

	public function uShipQueue1(OrbitalBase $orbitalBase, $sq, $player) {
		# vaisseau construit
		$orbitalBase->setShipStorage($sq->shipNumber, $orbitalBase->getShipStorage($sq->shipNumber) + $sq->quantity);
		# increase player experience
		$experience = $sq->quantity * ShipResource::getInfo($sq->shipNumber, 'points');
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if ($this->session->get('playerId') == $orbitalBase->rPlayer) {
			$alt = 'Construction de ';
			if ($sq->quantity > 1) {
				$alt .= 'vos <strong>' . $sq->quantity . ' ' . ShipResource::getInfo($sq->shipNumber, 'codeName') . 's</strong>';
			} else {
				$alt .= 'votre <strong>' . ShipResource::getInfo($sq->shipNumber, 'codeName') . '</strong>';
			}
			$alt .= ' sur <strong>' . $orbitalBase->name . '</strong> terminée. Vous gagnez ' . $experience . ' point' . Format::addPlural($experience) . ' d\'expérience.';
			$this->session->addFlashbag($alt, Flashbag::TYPE_DOCK1_SUCCESS);
		}

		# delete queue in database
		$this->shipQueueManager->deleteById($sq->id);
	}

	public function uShipQueue2(OrbitalBase $orbitalBase, $sq, $player) {
		# vaisseau construit
		$orbitalBase->setShipStorage($sq->shipNumber, $orbitalBase->getShipStorage($sq->shipNumber) + 1);
		# increase player experience
		$experience = ShipResource::getInfo($sq->shipNumber, 'points');
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if ($this->session->get('playerId') == $orbitalBase->rPlayer) {
			$this->session->addFlashbag('Construction de votre ' . ShipResource::getInfo($sq->shipNumber, 'codeName') . ' sur ' . $orbitalBase->name . ' terminée. Vous gagnez ' . $experience . ' d\'expérience.', Flashbag::TYPE_DOCK2_SUCCESS);
		}

		# delete queue in database
		$this->shipQueueManager->deleteById($sq->id);
	}

	public function uTechnologyQueue(OrbitalBase $orbitalBase, $tq, $player) {
		# technologie construite
		$technology = $this->technologyManager->getPlayerTechnology($player->getId());
		$this->technologyManager->affectTechnology($technology, $tq->technology, $tq->targetLevel);
		# increase player experience
		$experience = $this->technologyHelper->getInfo($tq->technology, 'points', $tq->targetLevel);
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if ($this->session->get('playerId') == $orbitalBase->rPlayer) {
			$alt = 'Développement de votre technologie ' . $this->technologyHelper->getInfo($tq->technology, 'name');
			if ($tq->targetLevel > 1) {
				$alt .= ' niveau ' . $tq->targetLevel;
			} 
			$alt .= ' terminée. Vous gagnez ' . $experience . ' d\'expérience.';
			$this->session->addFlashbag($alt, Flashbag::TYPE_TECHNOLOGY_SUCCESS);
		}

		# delete queue in database
		$this->technologyQueueManager->deleteById($tq->id);
	}

	public function uCommercialShipping(OrbitalBase $orbitalBase, $cs, $transaction, $destOB, $commander) {
		switch ($cs->statement) {
			case CommercialShipping::ST_GOING :
				# shipping arrived, delivery of items to rBaseDestination
				$this->commercialShippingManager->deliver($cs, $transaction, $destOB, $commander);
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
				$this->notificationManager->add($n);
				# delete commercialShipping
				$this->commercialShippingManager->deleteById($cs->id);
				break;
			default :break;
		}
	}

	public function uRecycling(OrbitalBase $orbitalBase, RecyclingMission $mission, Place $targetPlace, Player $player, $dateOfUpdate) {
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
				$n->addLnk('map/base-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
				$n->addEnd();
				$this->notificationManager->add($n);
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
				$n->addLnk('map/base-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
				$n->addEnd();
				$this->notificationManager->add($n);
			}

			$creditRecycled = round($targetPlace->population * $totalRecycled * 10 / 100);
			$resourceRecycled = round($targetPlace->coefResources * $totalRecycled / 100);
			$shipRecycled = round($targetPlace->coefHistory * $totalRecycled / 100);

			# diversify a little (resource and credit)
			$percent = rand(-5, 5);
			$diffAmountCredit = round($creditRecycled * $percent / 100);
			$diffAmountResource = round($resourceRecycled * $percent / 100);
			$creditRecycled += $diffAmountCredit;
			$resourceRecycled -= $diffAmountResource;

			if ($creditRecycled < 0) { $creditRecycled = 0; }
			if ($resourceRecycled < 0) { $resourceRecycled = 0; }

			# convert shipRecycled to real ships
			$pointsToRecycle = round($shipRecycled * RecyclingMission::COEF_SHIP);
			$shipsArray1 = array();
			$buyShip = array();
			for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) { 
				if (floor($pointsToRecycle / ShipResource::getInfo($i, 'resourcePrice')) > 0) {
					$shipsArray1[] = array(
						'ship' => $i,
						'price' => ShipResource::getInfo($i, 'resourcePrice'),
						'canBuild' => TRUE);
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
			$continue = TRUE;
			if (count($shipsArray) > 0) {
				while($continue) {
					foreach ($shipsArray as $key => $line) {
						if ($line['canBuild']) {
							$nbmax = floor($pointsToRecycle / $line['price']);
							if ($nbmax < 1) {
								$shipsArray[$key]['canBuild'] = FALSE;
							} else {
								$qty = rand(1, $nbmax);
								$pointsToRecycle -= $qty * $line['price'];
								$buyShip[$line['ship']] += $qty;
							}
						}
					}

					$canBuild = FALSE;
					# verify if we can build one more ship
					foreach ($shipsArray as $key => $line) {
						if ($line['canBuild']) {
							$canBuild = TRUE;
							break;
						}
					}
					if (!$canBuild) {
						# if the 3 types of ships can't be build anymore --> stop
						$continue = FALSE;
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
			$this->recyclingLogManager->add($rl);

			# give to the orbitalBase ($orbitalBase) and player what was recycled
			$this->increaseResources($orbitalBase, $resourceRecycled);
			for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) { 
				$this->addShipToDock($orbitalBase, $i, $buyShip[$i]);
			}
			$this->playerManager->increaseCredit($player, $creditRecycled);

			# add recyclers waiting to the mission
			$mission->recyclerQuantity += $mission->addToNextMission;
			$mission->addToNextMission = 0;

			# if a mission is stopped by the user, delete it
			if ($mission->statement == RecyclingMission::ST_BEING_DELETED) {
				$mission->statement = RecyclingMission::ST_DELETED;
			}

			# update u
			$mission->uRecycling = $dateOfUpdate;
		} else {
			# the place become an empty place
			$targetPlace->resources = 0;

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
			$n->addLnk('map/base-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
			$n->addEnd();
			$this->notificationManager->add($n);
		}
	}

	// OBJECT METHODS
	public function increaseResources(OrbitalBase $orbitalBase, $resources, $canGoHigher = FALSE) {
		if (intval($resources) >= 0) {
			# load the bonus
			$playerBonus = $this->playerBonusManager->getBonusByPlayer($orbitalBase->rPlayer);
			$this->playerBonusManager->load($playerBonus);
			$bonus = $playerBonus->bonus->get(PlayerBonus::REFINERY_STORAGE);

			$newResources = $orbitalBase->resourcesStorage + (int) $resources;
			
			$maxStorage = $this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $orbitalBase->levelStorage, 'storageSpace');
			$maxStorage += $maxStorage * $bonus / 100;

			if ($canGoHigher) {
				$maxStorage += OrbitalBase::EXTRA_STOCK;
			}

			if ($orbitalBase->resourcesStorage < $maxStorage || $canGoHigher) {
				if ($newResources > $maxStorage) {
					$orbitalBase->resourcesStorage = $maxStorage;
				} else {
					$orbitalBase->resourcesStorage = $newResources;
				}
			}
			$this->entityManager->flush($orbitalBase);
		} else {
			throw new ErrorException('Problème dans increaseResources de OrbitalBase');
		}
	}

	public function decreaseResources(OrbitalBase $orbitalBase, $resources) {
		if (intval($resources)) {
			$orbitalBase->resourcesStorage -= abs($resources);
			$this->entityManager->flush($orbitalBase);
		} else {
			throw new ErrorException('Problème dans decreaseResources de OrbitalBase');
		}
	}

	public function addShipToDock(OrbitalBase $orbitalBase, $shipId, $quantity) {
		if ($this->orbitalBaseHelper->isAShipFromDock1($shipId) OR $this->orbitalBaseHelper->isAShipFromDock2($shipId)) {
			$orbitalBase->setShipStorage($shipId, $orbitalBase->getShipStorage($shipId) + $quantity);
			$this->entityManager->flush($orbitalBase);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function removeShipFromDock(OrbitalBase $orbitalBase, $shipId, $quantity) {
		if ($this->orbitalBaseHelper->isAShipFromDock1($shipId) OR $this->orbitalBaseHelper->isAShipFromDock2($shipId)) {
			if ($orbitalBase->getShipStorage($shipId) >= $quantity) {
				$orbitalBase->setShipStorage($shipId, $orbitalBase->getShipStorage($shipId) - $quantity);
				$this->entityManager->flush($orbitalBase);
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}
}