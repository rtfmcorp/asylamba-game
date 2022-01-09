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
namespace App\Modules\Athena\Manager;

use App\Classes\Library\Session\SessionWrapper;
use App\Classes\Library\Utils;
use App\Classes\Library\Game;
use App\Classes\Library\Format;
use App\Classes\Worker\CTC;
use App\Classes\Exception\ErrorException;
use App\Classes\Entity\EntityManager;

use App\Modules\Ares\Model\Commander;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Gaia\Model\System;
use App\Modules\Promethee\Manager\TechnologyQueueManager;
use App\Modules\Promethee\Manager\TechnologyManager;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Manager\PlayerBonusManager;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Athena\Helper\OrbitalBaseHelper;
use App\Modules\Zeus\Model\PlayerBonus;

use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Promethee\Helper\TechnologyHelper;
use App\Modules\Athena\Resource\ShipResource;
use App\Classes\Library\Flashbag;

use App\Classes\Daemon\ClientManager;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

class OrbitalBaseManager
{
	protected CommanderManager $commanderManager;
	protected CommercialShippingManager $commercialShippingManager;
	protected PlayerManager $playerManager;

	public function __construct(
		protected EntityManager $entityManager,
		protected ClientManager $clientManager,
		protected MessageBusInterface $messageBus,
		protected BuildingQueueManager $buildingQueueManager,
		protected TechnologyQueueManager $technologyQueueManager,
		protected CommercialRouteManager $commercialRouteManager,
		protected TransactionManager $transactionManager,
		protected PlayerBonusManager $playerBonusManager,
		protected RecyclingMissionManager $recyclingMissionManager,
		protected NotificationManager $notificationManager,
		protected OrbitalBaseHelper $orbitalBaseHelper,
		protected SessionWrapper $sessionWrapper
	) {
	}

	#[Required]
	public function setCommanderManager(CommanderManager $commanderManager): void
	{
		$this->commanderManager = $commanderManager;
	}

	#[Required]
	public function setCommercialShippingManager(\App\Modules\Athena\Manager\CommercialShippingManager $commercialShippingManager): void
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
				// @TODO handle cancellation
				// $this->realtimeActionScheduler->cancel($commander, $commander->getArrivalDate());
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
