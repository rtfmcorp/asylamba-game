<?php

/**
 * Commander Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 20.05.13
*/
// !! lors d'un load, mettre c. avant les attribut where

namespace App\Modules\Ares\Manager;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\DateTimeConverter;
use App\Classes\Library\Utils;
use App\Classes\Library\Game;
use App\Modules\Ares\Message\CommanderTravelMessage;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Zeus\Manager\PlayerManager;
use App\Modules\Zeus\Manager\PlayerBonusManager;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Classes\Container\ArrayList;

use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Gaia\Model\Place;
use App\Modules\Ares\Model\Report;
use App\Modules\Ares\Model\LiveReport;
use App\Modules\Zeus\Model\PlayerBonus;
use App\Modules\Ares\Model\Commander;
use App\Modules\Gaia\Resource\SquadronResource;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

class CommanderManager
{
	protected FightManager $fightManager;
	protected PlayerManager $playerManager;

	public function __construct(
		protected EntityManager $entityManager,
		protected ReportManager $reportManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected PlayerBonusManager $playerBonusManager,
		protected PlaceManager $placeManager,
		protected MessageBusInterface $messageBus,
		protected int $commanderBaseLevel,
		protected int $gaiaId,
	) {
	}

	#[Required]
	public function setFightManager(FightManager $fightManager): void
	{
		$this->fightManager = $fightManager;
	}

	#[Required]
	public function setPlayerManager(PlayerManager $playerManager): void
	{
		$this->playerManager = $playerManager;
	}

	public function get(int $id): Commander
	{
		return $this->entityManager->getRepository(Commander::class)->get($id);
	}

	public function getBaseCommanders(int $orbitalBaseId, array $statements = [], array $orderBy = []): array
	{
		return $this->entityManager->getRepository(Commander::class)->getBaseCommanders($orbitalBaseId, $statements, $orderBy);
	}
	
	public function getPlayerCommanders(int $playerId, array $statements = [], array $orderBy = []): array
	{
		return $this->entityManager->getRepository(Commander::class)->getPlayerCommanders($playerId, $statements, $orderBy);
	}

	/**
	 * @return list<Commander>
	 */
	public function getMovingCommanders(): array
	{
		return $this->entityManager->getRepository(Commander::class)->getMovingCommanders();
	}
	
	public function getCommandersByIds(array $ids): array
	{
		return $this->entityManager->getRepository(Commander::class)->getCommandersByIds($ids);
	}
	
	public function getCommandersByLine(int $orbitalBaseId, int $line): array
	{
		return $this->entityManager->getRepository(Commander::class)->getCommandersByLine($orbitalBaseId, $line);
	}
	
	public function getIncomingAttacks(int $playerId): array
	{
		return $this->entityManager->getRepository(Commander::class)->getIncomingAttacks($playerId);
	}
	
	public function getVisibleIncomingAttacks(int $playerId): array
	{
		$attackingCommanders = $this->getIncomingAttacks($playerId) ;
		$incomingCommanders = [] ;
		
		foreach ($attackingCommanders as $commander) { 
			# va chercher les heures auxquelles il rentre dans les cercles d'espionnage
			$startPlace = $this->placeManager->get($commander->getRBase());
			$destinationPlace = $this->placeManager->get($commander->getRPlaceDestination());
			$times = Game::getAntiSpyEntryTime($startPlace, $destinationPlace, $commander->getArrivalDate());

			if (strtotime(Utils::now()) >= strtotime($times[0])) {
				# ajout de l'événement
				$incomingCommanders[] = $commander;
			}
		}		
		return $incomingCommanders;
	}
	
	public function getOutcomingAttacks(int $playerId): array
	{
		return $this->entityManager->getRepository(Commander::class)->getOutcomingAttacks($playerId);
	}
	
	public function getIncomingCommanders(int $place): array
	{
		return $this->entityManager->getRepository(Commander::class)->getIncomingCommanders($place);
	}
	
	public function scheduleMovements(): void
	{
		$commanders = $this->getMovingCommanders();

		foreach ($commanders as $commander) {
			$this->messageBus->dispatch(
				new CommanderTravelMessage($commander->getId()),
				[DateTimeConverter::to_delay_stamp($commander->getArrivalDate())],
			);
		}
	}
	
	public function countCommandersByLine(int $orbitalBaseId, int $line): int
	{
		return $this->entityManager->getRepository(Commander::class)->countCommandersByLine($orbitalBaseId, $line);
	}

	public function setEarnedExperience(Commander $commander, Commander $enemyCommander): void
	{
		$commander->setArmy();
		$finalOwnPev = 0;

		foreach ($commander->army as $squadron) {
			foreach ($squadron->getSquadron() as $ship) {
				$finalOwnPev += $ship->getPev();
			}
		}
		$importance = (($finalOwnPev + 1) * ($enemyCommander->getPevInBegin())) / 
			((($commander->pevInBegin + 1) * (($enemyCommander->getLevel() + 1) / 
				($commander->level + 1))));

		$commander->earnedExperience = $importance * Commander::COEFFEARNEDEXP;
		if($commander->winner) {
			LiveReport::$importance = $importance;
		}
		
		if ($commander->rPlayer > 0) {
			$exp = round($commander->earnedExperience / Commander::COEFFEXPPLAYER);
			$this->playerManager->increaseExperience($this->playerManager->get($commander->rPlayer), $exp);

			if ($enemyCommander->isAttacker == TRUE) {
				LiveReport::$expPlayerD = $exp;
			} else {
				LiveReport::$expPlayerA = $exp;
			}
		}
	}

	public function setBonus(Commander $commander): void
	{
		$commander->setArmy();
		$playerBonus = new PlayerBonus($commander->rPlayer);
		$playerBonus->load();

		foreach ($commander->army as $squadron) {
			foreach ($squadron->squadron as $ship) {
				$ship->setBonus($playerBonus->bonus);
			}
		}
	}

	public function upExperience(Commander $commander, $earnedExperience) {
		$commander->experience += $earnedExperience;
		$initialLevel = $commander->getLevel();
		
		while ($commander->experience >= $this->experienceToLevelUp($commander)) {
			$commander->setLevel($commander->getLevel() + 1);
		}
		$this->entityManager->getRepository(Commander::class)->updateExperience(
			$commander,
			$earnedExperience,
			$commander->getLevel() - $initialLevel
		);
	}

	public function nbLevelUp($level, $newExperience) {
		$oLevel = $level;
		$nLevel = $level;
		while (1) {
			if ($newExperience >= (pow(2, $nLevel) * $this->commanderBaseLevel)) {
				$nLevel++;
			} else {
				break;
			}
		}
		return $nLevel - $oLevel;
	}

	public function experienceToLevelUp(Commander $commander) {
		return pow(2, $commander->level) * $this->commanderBaseLevel;
	}

	public function emptySquadrons(Commander $commander) {
		if (($orbitalBase = $this->orbitalBaseManager->get($commander->rBase)) === null) {
			return;
		}
		$nbSquadrons = count($commander->squadronsIds);
		for ($i = 0; $i < $nbSquadrons; ++$i) {
			for ($j = 0; $j < 12; $j++) {
				$orbitalBase->setShipStorage($j, $orbitalBase->getShipStorage($j) + $commander->getSquadron($i)->getNbrShipByType($j));
			}
			$commander->getSquadron($i)->emptySquadron();
		}
	}

	public function move(Commander $commander, int $rDestinationPlace, int $rStartPlace, string $travelType, int $travelLength, int $duration): void
	{
		$commander->rDestinationPlace = $rDestinationPlace;
		$commander->rStartPlace = $rStartPlace;
		$commander->travelType = $travelType;
		$commander->travelLength = $travelLength;
		$commander->statement = Commander::MOVING;

		$commander->dStart = ($travelType != 3) ? Utils::now() : $commander->dArrival;
		$commander->destinationPlaceName = ($travelType != 3) ? $commander->destinationPlaceName : $commander->startPlaceName;
		$commander->startPlaceName = ($travelType != 3) ? $commander->oBName : $commander->destinationPlaceName;
		$date = new \DateTime($commander->dStart);
		$date->modify('+' . $duration . 'second');
		$commander->dArrival = $date->format('Y-m-d H:i:s');

		$this->messageBus->dispatch(
			new CommanderTravelMessage($commander->getId()),
			[DateTimeConverter::to_delay_stamp($commander->getArrivalDate())],
		);
	}
	
	public function resultOfFight(Commander $commander, $isWinner, $enemyCommander) {
		if ($isWinner == TRUE) {
			$this->setEarnedExperience($commander, $enemyCommander);
			$commander->earnedExperience = round($commander->earnedExperience);
			LiveReport::$expCom = $commander->earnedExperience;

			$commander->winner = TRUE;
			$commander->palmares++;
			$commander->setArmyAtEnd();
			$this->upExperience($commander, $commander->earnedExperience);
			$commander->hasChanged = TRUE;
		} else {
			/** @TOVERIFY **/
			$this->setEarnedExperience($commander, $commander, $enemyCommander);
			$commander->earnedExperience = round($commander->earnedExperience);

			$commander->winner = FALSE;
			$commander->setArmyAtEnd();
			$this->upExperience($commander, $commander->earnedExperience);
			$commander->hasChanged = TRUE;
		}
	}

	# ENGAGE UN COMBAT ENTRE CHAQUE SQUADRON CONTRE UN COMMANDANT
	public function engage(Commander $commander, $enemyCommander) {
		$commander->setArmy();
		
		for ($i = 0; $i < count($commander->squadronsIds); $i++) {
			$commander->getSquadron($i)->relId = 0;
		}
		$idSquadron = 0;
		foreach ($commander->army as $squadron) {
			if ($squadron->getNbrShips() != 0 AND $squadron->getLineCoord() * 3 <= FightManager::getCurrentLine()) {
				$enemyCommander = $squadron->engage($enemyCommander, $idSquadron, $commander->id, $commander->name, $commander);
			}
			$idSquadron++;
		}
		return $enemyCommander;
	}

	public function getPosition(Commander $commander, $x1, $y1, $x2, $y2) {
		$x = $x1;
		$y = $y1;
		if ($commander->statement == Commander::MOVING) {
			$parcouredTime = Utils::interval($commander->dStart, Utils::now(), 's');
			$totalTime = Utils::interval($commander->dStart, $commander->dArrival, 's');
			$progression = $parcouredTime / $totalTime;

			$x = $x1 + $progression * ($x2-$x1);
			$y = $y1 + $progression * ($y2-$y1);
		}
		return array($x, $y);
	}

	public function getEventInfo(Commander $commander) {
		$info = new ArrayList();
		$info->add('id', $commander->id);
		$info->add('name', $commander->name);
		$info->add('avatar', $commander->avatar);
		$info->add('level', $commander->level);

		$info->add('dStart', $commander->dStart);
		$info->add('rStart', $commander->rStartPlace);
		$info->add('nStart', $commander->startPlaceName);
		$info->add('dArrival', $commander->dArrival);
		$info->add('rArrival', $commander->rDestinationPlace);
		$info->add('nArrival', $commander->destinationPlaceName);

		$info->add('travelType', $commander->travelType);
		$info->add('resources', $commander->resources);

		return $info;
	}

	public function uChangeBase(Commander $commander): void
	{
		$place = $this->placeManager->get($commander->rDestinationPlace);
		$place->commanders = $this->getBaseCommanders($place->id);
		$commanderPlace = $this->placeManager->get($commander->rBase);
		$player = $this->playerManager->get($commander->rPlayer);
		$playerBonus = $this->playerBonusManager->getBonusByPlayer($player);
		$this->playerBonusManager->load($playerBonus);
		# si la place et la flotte ont la même couleur
		# on pose la flotte si il y a assez de place
		# sinon on met la flotte dans les hangars
		if ($place->playerColor !== $commander->playerColor or $place->typeOfBase !== Place::TYP_ORBITALBASE) {
			# retour forcé
			$this->comeBack($place, $commander, $commanderPlace, $playerBonus);
			$this->placeManager->sendNotif($place, Place::CHANGELOST, $commander);
			$this->entityManager->flush();
			return;
		}
		$maxCom =
			($place->typeOfOrbitalBase == OrbitalBase::TYP_MILITARY || $place->typeOfOrbitalBase == OrbitalBase::TYP_CAPITAL)
			? OrbitalBase::MAXCOMMANDERMILITARY
			: OrbitalBase::MAXCOMMANDERSTANDARD
		;

		# si place a assez de case libre :
		if (count($place->commanders) < $maxCom) {
			$comLine2 = 0;

			foreach ($place->commanders as $com) {
				if ($com->line == 2) {
					$comLine2++;
				}
			}

			if ($maxCom == OrbitalBase::MAXCOMMANDERMILITARY) {
				if ($comLine2 < 2) {
					$commander->line = 2;
				} else {
					$commander->line = 1;
				}
			} else {
				if ($comLine2 < 1) {
					$commander->line = 2;
				} else {
					$commander->line = 1;
				}
			}

			# changer rBase commander
			$commander->rBase = $place->id;
			$this->endTravel($commander, Commander::AFFECTED);

			# ajouter à la place le commandant
			$place->commanders[] = $commander;

			# envoi de notif
			$this->placeManager->sendNotif($place, Place::CHANGESUCCESS, $commander);
		} else {
			# changer rBase commander
			$commander->rBase = $place->id;
			$this->endTravel($commander, Commander::RESERVE);

			$this->emptySquadrons($commander);

			# envoi de notif
			$this->placeManager->sendNotif($place, Place::CHANGEFAIL, $commander);
		}

		# modifier le rPlayer (ne se modifie pas si c'est le même)
		$commander->rPlayer = $place->rPlayer;

		# instance de la place d'envoie + suppr commandant de ses flottes
		# enlever à rBase le commandant
		for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
			if ($commanderPlace->commanders[$i]->id == $commander->id) {
				unset($commanderPlace->commanders[$i]);
				$commanderPlace->commanders = array_merge($commanderPlace->commanders);
			}
		}
		$this->entityManager->flush();
	}
	
	/**
	 * @param Commander $commander
	 * @param string $statement
	 */
	public function endTravel(Commander $commander, $statement)
	{
		$commander->travelType = null;
		$commander->travelLength = null;
		$commander->dStart = null;
		$commander->dArrival = null;
		$commander->rStartPlace = null;
		$commander->rDestinationPlace = null;
		$commander->statement = $statement;
	}

	# HELPER

	# comeBack
	public function comeBack(Place $place, $commander, $commanderPlace, $playerBonus) {
		$length   = Game::getDistance($place->getXSystem(), $commanderPlace->getXSystem(), $place->getYSystem(), $commanderPlace->getYSystem());
		$duration = Game::getTimeToTravel($commanderPlace, $place, $playerBonus->bonus);

		$this->move($commander, $commander->rBase, $place->id, Commander::BACK, $length, $duration);
	}

	public function lootAnEmptyPlace(Place $place, $commander, $playerBonus) {
		$bonus = $playerBonus->bonus->get(PlayerBonus::SHIP_CONTAINER);
	
		$storage = $commander->getPevToLoot() * Commander::COEFFLOOT;
		$storage += round($storage * ((2 * $bonus) / 100));

		$resourcesLooted = 0;
		$resourcesLooted = ($storage > $place->resources) ? $place->resources : $storage;

		$place->resources -= $resourcesLooted;
		$commander->resources = $resourcesLooted;

		LiveReport::$resources = $resourcesLooted;
	}

	public function lootAPlayerPlace($commander, $playerBonus, $placeBase) {
		$bonus = $playerBonus->bonus->get(PlayerBonus::SHIP_CONTAINER);

		$resourcesToLoot = $placeBase->getResourcesStorage() - Commander::LIMITTOLOOT;

		$storage = $commander->getPevToLoot() * Commander::COEFFLOOT;
		$storage += round($storage * ((2 * $bonus) / 100));

		$resourcesLooted = 0;
		$resourcesLooted = ($storage > $resourcesToLoot) ? $resourcesToLoot : $storage;

		if ($resourcesLooted > 0) {
			$this->orbitalBaseManager->decreaseResources($placeBase, $resourcesLooted);
			$commander->resources = $resourcesLooted;

			LiveReport::$resources = $resourcesLooted;
		}
	}

	public function startFight(Place $place, $commander, $player, $enemyCommander = NULL, $enemyPlayer = NULL, $pvp = FALSE) {
		if ($pvp == TRUE) {
			$commander->setArmy();
			$enemyCommander->setArmy();

			$this->fightManager->startFight($commander, $player, $enemyCommander, $enemyPlayer);
		} else {
			$commander->setArmy();
			$computerCommander = $this->createVirtualCommander($place);

			$this->fightManager->startFight($commander, $player, $computerCommander);
		}
	}

	public function createReport(Place $place) {
		$report = new Report();

		$report->rPlayerAttacker = LiveReport::$rPlayerAttacker;
		$report->rPlayerDefender =  LiveReport::$rPlayerDefender;
		$report->rPlayerWinner = LiveReport::$rPlayerWinner;
		$report->avatarA = LiveReport::$avatarA;
		$report->avatarD = LiveReport::$avatarD;
		$report->nameA = LiveReport::$nameA;
		$report->nameD = LiveReport::$nameD;
		$report->levelA = LiveReport::$levelA;
		$report->levelD = LiveReport::$levelD;
		$report->experienceA = LiveReport::$experienceA;
		$report->experienceD = LiveReport::$experienceD;
		$report->palmaresA = LiveReport::$palmaresA;
		$report->palmaresD = LiveReport::$palmaresD;
		$report->resources = LiveReport::$resources;
		$report->expCom = LiveReport::$expCom;
		$report->expPlayerA = LiveReport::$expPlayerA;
		$report->expPlayerD = LiveReport::$expPlayerD;
		$report->rPlace = $place->id;
		$report->type = LiveReport::$type;
		$report->round = LiveReport::$round;
		$report->importance = LiveReport::$importance;
		$report->squadrons = LiveReport::$squadrons;
		$report->dFight = LiveReport::$dFight;
		$report->isLegal = LiveReport::$isLegal;
		$report->placeName = ($place->baseName == '') ? 'planète rebelle' : $place->baseName;
		$report->setArmies();
		$report->setPev();
		
		$this->reportManager->add($report);
		LiveReport::clear();

		return $report;
	}

	/**
	 * @param Place $place
	 * @return Commander
	 */
	public function createVirtualCommander(Place $place) {
		$vCommander = new Commander();
		$vCommander->id = 'Null';
		$vCommander->rPlayer = $this->gaiaId;
		$vCommander->name = 'rebelle';
		$vCommander->avatar = 't3-c4';
		$vCommander->sexe = 1;
		$vCommander->age = 42;
		$vCommander->statement = 1;
		$vCommander->level = ceil((((($place->maxDanger / (Place::DANGERMAX / Place::LEVELMAXVCOMMANDER))) * 9) + ($place->population / (Place::POPMAX / Place::LEVELMAXVCOMMANDER))) / 10);

		$nbrsquadron = ceil($vCommander->level * (($place->danger + 1) / ($place->maxDanger + 1)));

		$army = array();
		$squadronsIds = array();

		for ($i = 0; $i < $nbrsquadron; $i++) {
			$aleaNbr = ($place->coefHistory * $place->coefResources * $place->position * $i) % SquadronResource::size();
			$army[] = SquadronResource::get($vCommander->level, $aleaNbr);
			$squadronsIds[] = 0;
		}

		for ($i = $vCommander->level - 1; $i >= $nbrsquadron; $i--) {
			$army[$i] = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, Utils::now());
			$squadronsIds[] = 0;
		}

		$vCommander->setSquadronsIds($squadronsIds);
		$vCommander->setArmyInBegin($army);
		$vCommander->setArmy();
		$vCommander->setPevInBegin();

		return $vCommander;
	}
}
