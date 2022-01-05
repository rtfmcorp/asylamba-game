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

use Asylamba\Classes\Library\Session\SessionWrapper;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Worker\CTC;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Entity\EntityManager;

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

use Asylamba\Classes\Daemon\ClientManager;
use Asylamba\Classes\Scheduler\RealTimeActionScheduler;
use Symfony\Contracts\Service\Attribute\Required;

class OrbitalBaseManager
{
	protected CommanderManager $commanderManager;
	protected CommercialShippingManager $commercialShippingManager;
	protected PlayerManager $playerManager;

	public function __construct(
		protected EntityManager $entityManager,
		protected ClientManager $clientManager,
		protected RealTimeActionScheduler $realtimeActionScheduler,
		protected BuildingQueueManager $buildingQueueManager,
		protected ShipQueueManager $shipQueueManager,
		protected TechnologyQueueManager $technologyQueueManager,
		protected TechnologyManager $technologyManager,
		protected TechnologyHelper $technologyHelper,
		protected CommercialRouteManager $commercialRouteManager,
		protected TransactionManager $transactionManager,
		protected PlayerBonusManager $playerBonusManager,
		protected RecyclingMissionManager $recyclingMissionManager,
		protected RecyclingLogManager $recyclingLogManager,
		protected PlaceManager $placeManager,
		protected NotificationManager $notificationManager,
		protected OrbitalBaseHelper $orbitalBaseHelper,
		protected CTC $ctc,
		protected SessionWrapper $sessionWrapper
	) {
	}

	#[Required]
	public function setCommanderManager(CommanderManager $commanderManager): void
	{
		$this->commanderManager = $commanderManager;
	}

	#[Required]
	public function setCommercialShippingManager(\Asylamba\Modules\Athena\Manager\CommercialShippingManager $commercialShippingManager): void
	{
		$this->commercialShippingManager = $commercialShippingManager;
	}

	#[Required]
	public function setPlayerManager(PlayerManager $playerManager): void
	{
		$this->playerManager = $playerManager;
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
		$buildingQueues = $this->buildingQueueManager->getBaseQueues($orbitalBase->getRPlace());

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

		foreach ($buildingQueues as $buildingQueue) {
			switch ($buildingQueue->buildingNumber) {
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

		$orbitalBase->buildingQueues = $buildingQueues;
		$orbitalBase->technoQueues = $this->technologyQueueManager->getPlaceQueues($orbitalBase->getRPlace());
		$orbitalBase->commercialShippings = $this->commercialShippingManager->getByBase($orbitalBase->getRPlace());
	}

	public function add(OrbitalBase $orbitalBase) {
		$this->entityManager->persist($orbitalBase);
		$this->entityManager->flush($orbitalBase);
	}

	public function changeOwnerById($id, $base, $newOwner, $baseCommanders) {
		if ($base->getId() == 0) {
			throw new ErrorException('Cette base orbitale n\'existe pas !');
		}
		# changement de possesseur des offres du marché
		$transactions = $this->transactionManager->getBasePropositions($base->rPlace);

		foreach ($transactions as $transaction) {
			# change owner of transaction
			$transaction->rPlayer = $newOwner;

			$commercialShipping = $this->commercialShippingManager->getByTransactionId($transaction->id);
			# change owner of commercial shipping
			$commercialShipping->rPlayer = $newOwner;
		}

		# attribuer le rPlayer à la Base
		$oldOwner = $base->rPlayer;
		$base->setRPlayer($newOwner);

		# suppression des routes commerciales
		$this->commercialRouteManager->removeBaseRoutes($base);

		# suppression des technologies en cours de développement
		foreach ($base->technoQueues as $queue) {
			$this->technologyQueueManager->remove($queue);
		}

		# suppression des missions de recyclages ainsi que des logs de recyclages
		$this->recyclingMissionManager->removeBaseMissions($base->getId());

		# mise des investissements à 0
		$base->iSchool = 0;
		$base->iAntiSpy = 0;

		# mise à jour de la date de création pour qu'elle soit dans l'ordre
		$base->dCreation = Utils::now();

		// If the new owner is connected, we add the base to his session
		if (($session = $this->clientManager->getSessionByPlayerId($newOwner)) !== null) {
			$session->addBase('ob', $base->getId(), $base->getName(), $base->getSector(), $base->getSystem(), '1-' . Game::getSizeOfPlanet($base->getPlanetPopulation()), $base->typeOfBase);
			$this->sessionWrapper->save($session);
		}
		// If the  previous owner is connected, we remove the base from his session
		if (($session = $this->clientManager->getSessionByPlayerId($oldOwner)) !== null) {
			$session->removeBase('ob', $base->getId());
			$this->sessionWrapper->save($session);
		}

		# rendre déserteuses les flottes en voyage
		foreach ($baseCommanders as $commander) {
			if (in_array($commander->statement, [Commander::INSCHOOL, Commander::ONSALE, Commander::RESERVE])) {
				$commander->rPlayer = $newOwner;
			} else if ($commander->statement == Commander::MOVING) {
                $this->commanderManager->endTravel($commander, Commander::RETIRED);
				$this->realtimeActionScheduler->cancel($commander, $commander->getArrivalDate());
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
        $this->entityManager->flush();
	}
	
	/**
	 * @param OrbitalBase $orbitalBase
	 * @return int
	 */
	public function updatePoints(OrbitalBase $orbitalBase) {
		$initialPoints = $orbitalBase->getPoints();
		$points = 0;

		for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) { 
			for ($j = 0; $j < $orbitalBase->getBuildingLevel($i); $j++) { 
				$points += $this->orbitalBaseHelper->getBuildingInfo($i, 'level', $j + 1, 'resourcePrice') / 1000;
			}
		}

		$points = round($points);
		$orbitalBase->setPoints($points);
		return $points - $initialPoints;
	}
	
	public function updateBases()
	{
		$repository = $this->entityManager->getRepository(OrbitalBase::class);
		$bases = $repository->getAll();
		$this->entityManager->beginTransaction();
		$now = Utils::now();
		
		foreach ($bases as $base) {
			# update time
			$hours = Utils::intervalDates($now, $base->uOrbitalBase);

			if (count($hours) === 0) {
				continue;
			}
			$player = $this->playerManager->get($base->rPlayer);
			$playerBonus = $this->playerBonusManager->getBonusByPlayer($player);
			$this->playerBonusManager->load($playerBonus);
			$base->setUpdatedAt($now);
			$initialResources = $base->resourcesStorage;
			$initialAntiSpyAverage = $base->antiSpyAverage;
			
			foreach ($hours as $hour) {
				$this->updateResources($base, $playerBonus);
				$this->updateAntiSpy($base);
			}
			
			$repository->updateBase(
				$base,
				$base->resourcesStorage - $initialResources,
				$base->antiSpyAverage - $initialAntiSpyAverage
			);
		}
		$this->entityManager->commit();
	}
	
	/**
	 * @param OrbitalBase $orbitalBase
	 * @param PlayerBonus $playerBonus
	 */
	protected function updateResources(OrbitalBase $orbitalBase, PlayerBonus $playerBonus)
	{
		$addResources = Game::resourceProduction($this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $orbitalBase->levelRefinery, 'refiningCoefficient'), $orbitalBase->planetResources);
		$addResources += $addResources * $playerBonus->bonus->get(PlayerBonus::REFINERY_REFINING) / 100;

		$this->increaseResources($orbitalBase, (int) $addResources, false, false);
	}
	
	protected function updateAntiSpy(OrbitalBase $orbitalBase)
	{
		$orbitalBase->antiSpyAverage = round((($orbitalBase->antiSpyAverage * (24 - 1)) + ($orbitalBase->iAntiSpy)) / 24);
	}
	
	/**
	 * @param OrbitalBase $orbitalBase
	 * @param int $resources
	 * @param bool $offLimits
	 * @param bool $persist
	 */
	public function increaseResources(OrbitalBase $orbitalBase, $resources, $offLimits = false, $persist = true)
	{
		$playerBonus = $this->playerBonusManager->getBonusByPlayer($this->playerManager->get($orbitalBase->rPlayer));
		$this->playerBonusManager->load($playerBonus);
		$maxStorage = $this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $orbitalBase->levelStorage, 'storageSpace');
		$maxStorage += $maxStorage * $playerBonus->bonus->get(PlayerBonus::REFINERY_STORAGE) / 100;

		if ($offLimits === true) {
			$maxStorage += OrbitalBase::EXTRA_STOCK;
		}
		$addedResources = 
			(($orbitalBase->resourcesStorage + $resources) > $maxStorage)
			? $maxStorage - $orbitalBase->resourcesStorage
			: $resources
		;
		$orbitalBase->resourcesStorage += $addedResources;
		if ($persist === true) {
			$this->entityManager->getRepository(OrbitalBase::class)->increaseResources(
				$orbitalBase,
				$addedResources
			);
		}
	}
	
	/**
	 * @param OrbitalBase $orbitalBase
	 * @param int $resources
	 */
	public function decreaseResources(OrbitalBase $orbitalBase, $resources)
	{
		$substractedResources = 
			(($orbitalBase->resourcesStorage - $resources) < 0)
			? abs(0 - $orbitalBase->resourcesStorage)
			: $resources
		;
		$orbitalBase->resourcesStorage -= $substractedResources;
		$this->entityManager->getRepository(OrbitalBase::class)->decreaseResources(
			$orbitalBase,
			$substractedResources
		);
	}

	public function uShipQueue1($shipQueueId) {
		$queue = $this->shipQueueManager->get($shipQueueId);
		$orbitalBase = $this->get($queue->rOrbitalBase);
		$player = $this->playerManager->get($orbitalBase->rPlayer);
		# vaisseau construit
		$orbitalBase->setShipStorage($queue->shipNumber, $orbitalBase->getShipStorage($queue->shipNumber) + $queue->quantity);
		# increase player experience
		$experience = $queue->quantity * ShipResource::getInfo($queue->shipNumber, 'points');
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if (($session = $this->clientManager->getSessionByPlayerId($player->getId())) !== null) {
			$alt = 'Construction de ';
			if ($queue->quantity > 1) {
				$alt .= 'vos <strong>' . $queue->quantity . ' ' . ShipResource::getInfo($queue->shipNumber, 'codeName') . 's</strong>';
			} else {
				$alt .= 'votre <strong>' . ShipResource::getInfo($queue->shipNumber, 'codeName') . '</strong>';
			}
			$alt .= ' sur <strong>' . $orbitalBase->name . '</strong> terminée. Vous gagnez ' . $experience . ' point' . Format::addPlural($experience) . ' d\'expérience.';
			$session->addFlashbag($alt, Flashbag::TYPE_DOCK1_SUCCESS);
			$this->sessionWrapper->save($session);
		}
		$this->entityManager->remove($queue);
		$this->entityManager->flush();
	}

	public function uShipQueue2($shipQueueId) {
		$queue = $this->shipQueueManager->get($shipQueueId);
		$orbitalBase = $this->get($queue->rOrbitalBase);
		$player = $this->playerManager->get($orbitalBase->rPlayer);
		# vaisseau construit
		$orbitalBase->setShipStorage($queue->shipNumber, $orbitalBase->getShipStorage($queue->shipNumber) + 1);
		# increase player experience
		$experience = ShipResource::getInfo($queue->shipNumber, 'points');
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if (($session = $this->clientManager->getSessionByPlayerId($player->getId())) !== null) {
			$session->addFlashbag('Construction de votre ' . ShipResource::getInfo($queue->shipNumber, 'codeName') . ' sur ' . $orbitalBase->name . ' terminée. Vous gagnez ' . $experience . ' d\'expérience.', Flashbag::TYPE_DOCK2_SUCCESS);
			$this->sessionWrapper->save($session);
		}
		$this->entityManager->remove($queue);
		$this->entityManager->flush();
	}

	public function uTechnologyQueue($technologyQueueId) {
		$technologyQueue = $this->technologyQueueManager->get($technologyQueueId);
		$orbitalBase = $this->get($technologyQueue->getPlaceId());
		$player = $this->playerManager->get($technologyQueue->getPlayerId());
		# technologie construite
		$technology = $this->technologyManager->getPlayerTechnology($player->getId());
		$this->technologyManager->affectTechnology($technology, $technologyQueue->getTechnology(), $technologyQueue->getTargetLevel(), $player);
		# increase player experience
		$experience = $this->technologyHelper->getInfo($technologyQueue->getTechnology(), 'points', $technologyQueue->getTargetLevel());
		$this->playerManager->increaseExperience($player, $experience);

		# alert
		if (($session = $this->clientManager->getSessionByPlayerId($player->getId())) !== null) {
			$alt = 'Développement de votre technologie ' . $this->technologyHelper->getInfo($technologyQueue->getTechnology(), 'name');
			if ($technologyQueue->getTargetLevel() > 1) {
				$alt .= ' niveau ' . $technologyQueue->getTargetLevel();
			} 
			$alt .= ' terminée. Vous gagnez ' . $experience . ' d\'expérience.';
			$session->addFlashbag($alt, Flashbag::TYPE_TECHNOLOGY_SUCCESS);
			$this->sessionWrapper->save($session);
		}
		$this->entityManager->remove($technologyQueue);
		$this->entityManager->flush($technologyQueue);
	}

	public function uCommercialShipping($id) {

		$cs = $this->commercialShippingManager->get($id);
		$transaction = $this->transactionManager->get($cs->getTransactionId());
		$orbitalBase = $this->get($cs->getBaseId());
		$destOB = $this->get($cs->getDestinationBaseId());
		$commander =
			($transaction !== null && $transaction->type === Transaction::TYP_COMMANDER)
			? $this->commanderManager->get($transaction->identifier)
			: null
		;

		switch ($cs->statement) {
			case CommercialShipping::ST_GOING :
				# shipping arrived, delivery of items to rBaseDestination
				$this->commercialShippingManager->deliver($cs, $transaction, $destOB, $commander);
				# prepare commercialShipping for moving back
				$cs->statement = CommercialShipping::ST_MOVING_BACK;
				$timeToTravel = strtotime($cs->dArrival) - strtotime($cs->dDeparture);
				$cs->dDeparture = $cs->dArrival;
				$cs->dArrival = Utils::addSecondsToDate($cs->dArrival, $timeToTravel);
				$this->realtimeActionScheduler->schedule('athena.orbital_base_manager', 'uCommercialShipping', $cs, $cs->dArrival);
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
				$n->addLnk('map/place-' . $cs->rBase, 'base orbitale')->addTxt(' après avoir livré du matériel sur une autre ');
				$n->addLnk('map/place-' . $cs->rBaseDestination, 'base')->addTxt(' . ');
				if ($cs->shipQuantity == 1) {
					$n->addSep()->addTxt('Votre vaisseau de commerce est à nouveau disponible pour faire d\'autres transactions ou routes commerciales.');
				} else {
					$n->addSep()->addTxt('Vos ' . $cs->shipQuantity . ' vaisseaux de commerce sont à nouveau disponibles pour faire d\'autres transactions ou routes commerciales.');
				}
				$n->addEnd();
				$this->notificationManager->add($n);
				# delete commercialShipping
				$this->entityManager->remove($cs);
				break;
			default :break;
		}
		$this->entityManager->flush();
	}

	public function uRecycling($missionId) {
		$mission = $this->recyclingMissionManager->get($missionId);
		$orbitalBase = $this->get($mission->rBase);
		$targetPlace = $this->placeManager->get($mission->rTarget);
		
		$player = $this->playerManager->get($orbitalBase->getRPlayer());
		if ($targetPlace->typeOfPlace != Place::EMPTYZONE) {
			# make the recycling : decrease resources on the target place
			$totalRecycled = $mission->recyclerQuantity * RecyclingMission::RECYCLER_CAPACTIY;
			$targetPlace->resources -= $totalRecycled;
			# if there is no more resource
			if ($targetPlace->resources <= 0) {
                // Avoid out of range errors
                $targetPlace->resources = 0;
				# the place become an empty place
				$this->placeManager->turnAsEmptyPlace($targetPlace);

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
				$n->addLnk('map/place-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
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
				$n->addLnk('map/place-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
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
			$mission->uRecycling = Utils::addSecondsToDate($mission->uRecycling, $mission->cycleTime);
			// Schedule the next mission if there is still resources
            if ($mission->getStatement() !== RecyclingMission::ST_DELETED) {
                $this->realtimeActionScheduler->schedule('athena.orbital_base_manager', 'uRecycling', $mission, $mission->uRecycling);
            }
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
			$n->addLnk('map/place-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
			$n->addEnd();
			$this->notificationManager->add($n);
		}
		$this->entityManager->flush();
	}

	public function addShipToDock(OrbitalBase $orbitalBase, int $shipId, int $quantity): bool
	{
		if ($this->orbitalBaseHelper->isAShipFromDock1($shipId) || $this->orbitalBaseHelper->isAShipFromDock2($shipId)) {
			$orbitalBase->setShipStorage($shipId, $orbitalBase->getShipStorage($shipId) + $quantity);
			$this->entityManager->flush($orbitalBase);
			return true;
		}
		return false;
	}

	public function removeShipFromDock(OrbitalBase $orbitalBase, int $shipId, int $quantity)
	{
		if ($this->orbitalBaseHelper->isAShipFromDock1($shipId) || $this->orbitalBaseHelper->isAShipFromDock2($shipId)) {
			if ($orbitalBase->getShipStorage($shipId) >= $quantity) {
				$orbitalBase->setShipStorage($shipId, $orbitalBase->getShipStorage($shipId) - $quantity);
				$this->entityManager->flush($orbitalBase);

				return true;
			}
			// @TODO: Check if this return mustn't be outside the if. If so, change the return type of the method
			return false;
		}
	}
}
