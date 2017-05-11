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

namespace Asylamba\Modules\Ares\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Manager\PlayerBonusManager;
use Asylamba\Modules\Gaia\Manager\PlaceManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Classes\Library\Session\SessionWrapper;
use Asylamba\Classes\Container\ArrayList;

use Asylamba\Classes\Worker\EventDispatcher;

use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Modules\Ares\Model\LiveReport;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Resource\SquadronResource;
use Asylamba\Modules\Demeter\Model\Color;

use Asylamba\Classes\Scheduler\RealTimeActionScheduler;

use Asylamba\Modules\Gaia\Event\PlaceOwnerChangeEvent;

class CommanderManager
{
	/** @var EntityManager **/
	protected $entityManager;
	/** @var FightManager **/
	protected $fightManager;
	/** @var ReportManager **/
	protected $reportManager;
	/** @var OrbitalBaseManager **/
	protected $orbitalBaseManager;
	/** @var PlayerManager **/
	protected $playerManager;
	/** @var PlayerBonusManager **/
	protected $playerBonusManager;
	/** @var PlaceManager **/
	protected $placeManager;
	/** @var ColorManager **/
	protected $colorManager;
	/** @var NotificationManager **/
	protected $notificationManager;
	/** @var Session **/
	protected $session;
	/** @var RealTimeActionScheduler **/
	protected $scheduler;
	/** @var EventDispatcher **/
	protected $eventDispatcher;
	/** @var int **/
	protected $commanderBaseLevel;
	
	protected $actions = [
		Commander::MOVE => 'uChangeBase',
		Commander::LOOT => 'uLoot',
		Commander::COLO => 'uConquer',
		Commander::BACK => 'uReturnBase'
	];

	/**
	 * @param EntityManager $entityManager
	 * @param FightManager $fightManager
	 * @param ReportManager $reportManager
	 * @param OrbitalBaseManager $orbitalBaseManager
	 * @param PlayerManager $playerManager
	 * @param PlayerBonusManager $playerBonusManager
	 * @param PlaceManager $placeManager
	 * @param ColorManager $colorManager
	 * @param NotificationManager $notificationManager
	 * @param SessionWrapper $session
	 * @param RealTimeActionScheduler $scheduler
	 * @param EventDispatcher $eventDispatcher
	 * @param int $commanderBaseLevel
	 */
	public function __construct(
		EntityManager $entityManager,
		FightManager $fightManager,
		ReportManager $reportManager,
		OrbitalBaseManager $orbitalBaseManager,
		PlayerManager $playerManager,
		PlayerBonusManager $playerBonusManager,
		PlaceManager $placeManager,
		ColorManager $colorManager,
		NotificationManager $notificationManager,
		SessionWrapper $session,
		RealTimeActionScheduler $scheduler,
		EventDispatcher $eventDispatcher,
		$commanderBaseLevel
	) {
		$this->entityManager = $entityManager;
		$this->fightManager = $fightManager;
		$this->reportManager = $reportManager;
		$this->orbitalBaseManager = $orbitalBaseManager;
		$this->playerManager = $playerManager;
		$this->playerBonusManager = $playerBonusManager;
		$this->placeManager = $placeManager;
		$this->colorManager = $colorManager;
		$this->notificationManager = $notificationManager;
		$this->session = $session;
		$this->scheduler = $scheduler;
		$this->eventDispatcher = $eventDispatcher;
		$this->commanderBaseLevel = $commanderBaseLevel;
	}
	
	/**
	 * @param integer $id
	 * @return Commander
	 */
	public function get($id)
	{
		return $this->entityManager->getRepository(Commander::class)->get($id);
	}
	
	/**
	 * @param integer $orbitalBaseId
	 * @param array $statements
	 * @return array
	 */
	public function getBaseCommanders($orbitalBaseId, $statements = [], $orderBy = [])
	{
		return $this->entityManager->getRepository(Commander::class)->getBaseCommanders($orbitalBaseId, $statements, $orderBy);
	}
	
	/**
	 * @param int $playerId
	 * @param array $statements
	 * @return array
	 */
	public function getPlayerCommanders($playerId, $statements = [], $orderBy = [])
	{
		return $this->entityManager->getRepository(Commander::class)->getPlayerCommanders($playerId, $statements, $orderBy);
	}
	
	/**
	 * @return array
	 */
	public function getMovingCommanders()
	{
		return $this->entityManager->getRepository(Commander::class)->getMovingCommanders();
	}
	
	/**
	 * @param array $ids
	 * @return array
	 */
	public function getCommandersByIds($ids)
	{
		return $this->entityManager->getRepository(Commander::class)->getCommandersByIds($ids);
	}
	
	/**
	 * @param int $orbitalBaseId
	 * @param int $line
	 * @return array
	 */
	public function getCommandersByLine($orbitalBaseId, $line)
	{
		return $this->entityManager->getRepository(Commander::class)->getCommandersByLine($orbitalBaseId, $line);
	}
	
	/**
	 * @param array $places
	 * @return array
	 */
	public function getIncomingAttacks($places)
	{
		return $this->entityManager->getRepository(Commander::class)->getIncomingAttacks($places);
	}
	
	/**
	 * @param array $place
	 * @return array
	 */
	public function getIncomingCommanders($place)
	{
		return $this->entityManager->getRepository(Commander::class)->getIncomingCommanders($place);
	}
	
	public function scheduleMovements()
	{
		$commanders = $this->getMovingCommanders();
		foreach ($commanders as $commander) {
			$this->scheduler->schedule(
				'ares.commander_manager',
				$this->actions[$commander->getTravelType()],
				$commander,
				$commander->dArrival
			);
		}
	}
	
	/**
	 * @param int $orbitalBaseId
	 * @param int $line
	 * @return int
	 */
	public function countCommandersByLine($orbitalBaseId, $line)
	{
		return $this->entityManager->getRepository(Commander::class)->countCommandersByLine($orbitalBaseId, $line);
	}

    public function setCommander($commander) {
        $this->objects['' . $commander->getId() .''] = $commander;
    }

	public function setEarnedExperience(Commander $commander, $enemyCommander) {
		$commander->setArmy();
		$finalOwnPev = 0;

		foreach ($commander->army AS $squadron) {
			foreach ($squadron->getSquadron() AS $ship) {
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

	public function setBonus(Commander $commander) {
		$commander->setArmy();
		if ($commander->rPlayer != $this->session->get('playerId')) {
			$playerBonus = new PlayerBonus($commander->rPlayer);
			$playerBonus->load();
			
			foreach ($commander->army AS $squadron) {
				foreach ($squadron->squadron AS $ship) {
					$ship->setBonus($playerBonus->bonus);
				}
			}
		} else {
			foreach ($commander->army AS $squadron) {
				foreach ($squadron->squadron AS $ship) {
					$ship->setBonus($this->session->get('playerBonus'));
				}
			}
		}
	}

	public function upExperience(Commander $commander, $earnedExperience) {
		$commander->experience += $earnedExperience;

		while (1) {
			if ($commander->experience >= $this->experienceToLevelUp($commander)) {
				$commander->level++;
			} else {
				break;
			}
		}
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

	public function uExperienceInSchool()
	{
		$now = Utils::now();
		$commanders = $this->entityManager->getRepository(Commander::class)->getAllByStatements([Commander::INSCHOOL]);

		foreach ($commanders as $commander) {
			// If the commander was updated recently, we skip him
			if (Utils::interval($commander->uCommander, $now, 'h') === 0) {
				continue;
			}
			
			$nbrHours = Utils::intervalDates($now, $commander->uCommander);
			$commander->uCommander = $now;
			$orbitalBase = $this->orbitalBaseManager->get($commander->rBase);
			
			if ($commander->rPlayer != $this->session->get('playerId')) {
				$playerBonus = $this->playerBonusManager->getBonusByPlayer($this->playerManager->get($commander->rPlayer));
				$this->playerBonusManager->load($playerBonus);
				$playerBonus = $playerBonus->bonus;
			} else {
				$playerBonus = $this->session->get('playerBonus');
			}
			foreach ($nbrHours as $hour) {
				$invest  = $orbitalBase->iSchool;
				$invest += $invest * $playerBonus->get(PlayerBonus::COMMANDER_INVEST) / 100;

				# xp gagnée
				$earnedExperience  = $invest / Commander::COEFFSCHOOL;
				$earnedExperience += (rand(0, 1) == 1) 
					? rand(0, $earnedExperience / 20)
					: -(rand(0, $earnedExperience / 20));
				$earnedExperience  = round($earnedExperience);
				$earnedExperience  = ($earnedExperience < 0)
					? 0 : $earnedExperience;

				$this->upExperience($commander, $earnedExperience);
			}
		}
		$this->entityManager->flush(Commander::class);
	}

	public function move(Commander $commander, $rDestinationPlace, $rStartPlace, $travelType, $travelLength, $duration) {
		$commander->rDestinationPlace = $rDestinationPlace;
		$commander->rStartPlace = $rStartPlace;
		$commander->travelType = $travelType;
		$commander->travelLength = $travelLength;
		$commander->statement = Commander::MOVING;

		$commander->dStart = ($travelType != 3) ? Utils::now() : $commander->dArrival;
		$commander->startPlaceName = ($travelType != 3) ? $commander->oBName : $commander->destinationPlaceName;
		$commander->destinationPlaceName = ($travelType != 3) ? $commander->destinationPlaceName : $commander->startPlaceName;
		$date = new \DateTime($commander->dStart);
		$date->modify('+' . $duration . 'second');
		$commander->dArrival = $date->format('Y-m-d H:i:s');

		// ajout de l'event dans le contrôleur
		if ($this->session->exist('playerEvent') && $commander->rPlayer == $this->session->get('playerId')) {
			$this->session->get('playerEvent')->add(
				$commander->dArrival,
				EVENT_OUTGOING_ATTACK,
				$commander->id,
				$this->getEventInfo($commander)
			);
		}
		$this->scheduler->schedule(
			'ares.commander_manager',
			$this->actions[$travelType],
			$commander,
			$commander->dArrival
		);
		return TRUE;
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

	/**
	 * Fleet moving
	 * 
	 * @param int $commanderId
	 */
	public function uChangeBase($commanderId) {
		$commander = $this->get($commanderId);
		$place = $this->placeManager->get($commander->rDestinationPlace);
		$commanderPlace = $this->placeManager->get($commander->rBase);
		$player = $this->playerManager->get($commander->rPlayer);
		$playerBonus = $this->playerBonusManager->getBonusByPlayer($player);
		$this->playerBonusManager->load($playerBonus);
		# si la place et la flotte ont la même couleur
		# on pose la flotte si il y a assez de place
		# sinon on met la flotte dans les hangars
		if ($place->playerColor !== $commander->playerColor OR $place->typeOfBase !== Place::TYP_ORBITALBASE) {
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

	# pillage
	public function uLoot($commanderId) {
		$commander = $this->get($commanderId);
		$place = $this->placeManager->get($commander->rDestinationPlace);
		$placePlayer = $this->playerManager->get($place->rPlayer);
		$placeBase = $this->orbitalBaseManager->get($place->id);
		$commanderPlace = $this->placeManager->get($commander->rBase);
		$commanderPlayer = $this->playerManager->get($commander->rPlayer);
		$commanderColor = $this->colorManager->get($commanderPlayer->rColor);
		$playerBonus = $this->playerBonusManager->getBonusByPlayer($commanderPlayer);
		$this->playerBonusManager->load($playerBonus);
		LiveReport::$type   = Commander::LOOT;
		LiveReport::$dFight = $commander->dArrival;

		# si la planète est vide
		if ($place->rPlayer == NULL) {
			LiveReport::$isLegal = Report::LEGAL;

			# planète vide : faire un combat
			$this->startFight($place, $commander, $commanderPlayer);

			# victoire
			if ($commander->getStatement() != Commander::DEAD) {
				# piller la planète
				$this->lootAnEmptyPlace($place, $commander, $playerBonus);
				# création du rapport de combat
				$report = $this->createReport($place);

				# réduction de la force de la planète
				$percentage = (($report->pevAtEndD + 1) / ($report->pevInBeginD + 1)) * 100;
				$place->danger = round(($percentage * $place->danger) / 100);

				$this->comeBack($place, $commander, $commanderPlace, $playerBonus);
				$this->placeManager->sendNotif($place, Place::LOOTEMPTYSSUCCESS, $commander, $report->id);
			} else {
				# si il est mort
				# enlever le commandant de la session
				for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
					if ($commanderPlace->commanders[$i]->getId() == $commander->getId()) {
						unset($commanderPlace->commanders[$i]);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}

				# création du rapport de combat
				$report = $this->createReport($place);
				$this->placeManager->sendNotif($place, Place::LOOTEMPTYFAIL, $commander, $report->id);

				# réduction de la force de la planète
				$percentage = (($report->pevAtEndD + 1) / ($report->pevInBeginD + 1)) * 100;
				$place->danger = round(($percentage * $place->danger) / 100);
			}
		# si il y a une base d'un joueur
		} else {
			if ($commanderColor->colorLink[$place->playerColor] == Color::ALLY || $commanderColor->colorLink[$place->playerColor] == Color::PEACE) {
				LiveReport::$isLegal = Report::ILLEGAL;
			} else {
				LiveReport::$isLegal = Report::LEGAL;
			}

			# planète à joueur : si $this->rColor != commandant->rColor
			# si il peut l'attaquer
			if (($place->playerColor != $commander->getPlayerColor() && $place->playerLevel > 1 && $commanderColor->colorLink[$place->playerColor] != Color::ALLY) || ($place->playerColor == 0)) {
				$dCommanders = array();
				foreach ($place->commanders AS $dCommander) {
					if ($dCommander->statement == Commander::AFFECTED && $dCommander->line == 1) {
						$dCommanders[] = $dCommander;
					}
				}

				# il y a des commandants en défense : faire un combat avec un des commandants
				if (count($dCommanders) != 0) {
					$aleaNbr = rand(0, count($dCommanders) - 1);
					$this->startFight($place, $commander, $commanderPlayer, $dCommanders[$aleaNbr], $placePlayer, TRUE);

					# victoire
					if ($commander->getStatement() != Commander::DEAD) {
						# piller la planète
						$this->lootAPlayerPlace($commander, $playerBonus, $placeBase);
						$this->comeBack($place, $commander, $commanderPlace, $playerBonus);
	
						# suppression des commandants						
						unset($place->commanders[$aleaNbr]);
						$place->commanders = array_merge($place->commanders);

						# création du rapport
						$report = $this->createReport($place);

						$this->placeManager->sendNotif($place, Place::LOOTPLAYERWHITBATTLESUCCESS, $commander, $report->id);
				
					# défaite
					} else {
						# enlever le commandant de la session
						for ($i = 0; $i < count($commanderPlace->commanders); $i++) {
							if ($commanderPlace->commanders[$i]->getId() == $commander->getId()) {
								unset($commanderPlace->commanders[$i]);
								$commanderPlace->commanders = array_merge($commanderPlace->commanders);
							}
						}

						# création du rapport
						$report = $this->createReport($place);

						# mise à jour des flottes du commandant défenseur
						for ($j = 0; $j < count($dCommanders[$aleaNbr]->armyAtEnd); $j++) {
							for ($i = 0; $i < 12; $i++) { 
								$dCommanders[$aleaNbr]->armyInBegin[$j][$i] = $dCommanders[$aleaNbr]->armyAtEnd[$j][$i];
							}
						}

						$this->placeManager->sendNotif($place, Place::LOOTPLAYERWHITBATTLEFAIL, $commander, $report->id);
					}
				} else {
					$this->lootAPlayerPlace($commander, $playerBonus, $placeBase);
					$this->comeBack($place, $commander, $commanderPlace, $playerBonus);
					$this->placeManager->sendNotif($place, Place::LOOTPLAYERWHITOUTBATTLESUCCESS, $commander);
				}

			} else {
				# si c'est la même couleur
				if ($place->rPlayer == $commander->rPlayer) {
					# si c'est une de nos planètes
					# on tente de se poser
					$this->uChangeBase($commander->id);
				} else {
					# si c'est une base alliée
					# on repart
					$this->comeBack($place, $commander, $commanderPlace, $playerBonus);
					$this->placeManager->sendNotif($place, Place::CHANGELOST, $commander);
				}
			}
		}
		$this->entityManager->flush();
	}

	# conquest
	public function uConquer($commanderId) {
		$commander = $this->get($commanderId);
		$place = $this->placeManager->get($commander->rDestinationPlace);
		$placePlayer = $this->playerManager->get($place->rPlayer);
		$placeBase = $this->orbitalBaseManager->get($place->id);
		$commanderPlace = $this->placeManager->get($commander->rBase);
		$commanderPlayer = $this->playerManager->get($commander->rPlayer);
		$commanderColor = $this->colorManager->get($commanderPlayer->rColor);
		$baseCommanders = $this->getBaseCommanders($place->getId());
		$playerBonus = $this->playerBonusManager->getBonusByPlayer($commanderPlayer);
		$this->playerBonusManager->load($playerBonus);
		# conquete
		if ($place->rPlayer != NULL) {
			if (($place->playerColor != $commander->getPlayerColor() && $place->playerLevel > 3 && $commanderColor->colorLink[$place->playerColor] != Color::ALLY) || ($place->playerColor == 0)) {
				$tempCom = array();

				for ($i = 0; $i < count($place->commanders); $i++) {
					if ($place->commanders[$i]->line <= 1) {
						$tempCom[] = $place->commanders[$i];
					}
				}
				for ($i = 0; $i < count($place->commanders); $i++) {
					if ($place->commanders[$i]->line >= 2) {
						$tempCom[] = $place->commanders[$i];
					}
				}

				$place->commanders = $tempCom;

				$nbrBattle = 0;
				$reportIds   = array();
				$reportArray = array();

				while ($nbrBattle < count($place->commanders)) {
					if ($place->commanders[$nbrBattle]->statement == Commander::AFFECTED) {
						LiveReport::$type = Commander::COLO;
						LiveReport::$dFight = $commander->dArrival;

						if ($commanderColor->colorLink[$place->playerColor] == Color::ALLY || $commanderColor->colorLink[$place->playerColor] == Color::PEACE) {
							LiveReport::$isLegal = Report::ILLEGAL;
						} else {
							LiveReport::$isLegal = Report::LEGAL;
						}

						$this->startFight($place, $commander, $commanderPlayer, $place->commanders[$nbrBattle], $placePlayer, TRUE);

						$report = $this->createReport($place);
						$reportArray[] = $report;
						$reportIds[] = $report->id;
						
						# PATCH DEGUEU POUR LES MUTLIS-COMBATS
						$reports = $this->reportManager->getByAttackerAndPlace($commander->rPlayer, $place->id, $commander->dArrival);
						foreach($reports as $r) {
							if ($r->id == $report->id) {
								continue;
							}
							$r->statementAttacker = Report::DELETED;
							$r->statementDefender = Report::DELETED;
						}
						$this->entityManager->flush(Report::class);
						########################################

						# mettre à jour armyInBegin si prochain combat pour prochain rapport
						for ($j = 0; $j < count($commander->armyAtEnd); $j++) {
							for ($i = 0; $i < 12; $i++) { 
								$commander->armyInBegin[$j][$i] = $commander->armyAtEnd[$j][$i];
							}
						}
						for ($j = 0; $j < count($place->commanders[$nbrBattle]->armyAtEnd); $j++) {
							for ($i = 0; $i < 12; $i++) {
								$place->commanders[$nbrBattle]->armyInBegin[$j][$i] = $place->commanders[$nbrBattle]->armyAtEnd[$j][$i];
							}
						}
						
						$nbrBattle++;
						# mort du commandant
						# arrêt des combats
						if ($commander->getStatement() == Commander::DEAD) {
							break;
						}
					} else {
						$nbrBattle++;
					}
				}

				# victoire
				if ($commander->getStatement() != Commander::DEAD) {
					if ($nbrBattle == 0) {
						$this->placeManager->sendNotif($place, Place::CONQUERPLAYERWHITOUTBATTLESUCCESS, $commander, NULL);
					} else {
						$this->placeManager->sendNotifForConquest($place, Place::CONQUERPLAYERWHITBATTLESUCCESS, $commander, $reportIds);
					}


					#attribuer le joueur à la place
					$place->commanders = array();
					$place->playerColor = $commander->playerColor;
					$place->rPlayer = $commander->rPlayer;

					# changer l'appartenance de la base (et de la place)
					$this->orbitalBaseManager->changeOwnerById($place->id, $placeBase, $commander->getRPlayer(), $baseCommanders);
					$place->commanders[] = $commander;

					$commander->rBase = $place->id;
					$this->endTravel($commander, Commander::AFFECTED);
					$commander->line = 2;
					
					$this->eventDispatcher->dispatch(new PlaceOwnerChangeEvent($place));

					# PATCH DEGUEU POUR LES MUTLIS-COMBATS
					$this->notificationManager->patchForMultiCombats($commander->rPlayer, $place->rPlayer, $commander->dArrival);
				# défaite
				} else {
					for ($i = 0; $i < count($place->commanders); $i++) {
						if ($place->commanders[$i]->statement == Commander::DEAD) {
							unset($place->commanders[$i]);
							$place->commanders = array_merge($place->commanders);
						}
					}

					$this->placeManager->sendNotifForConquest($place, Place::CONQUERPLAYERWHITBATTLEFAIL, $commander, $reportIds);
				}

			} else {
				# si c'est la même couleur
				if ($place->rPlayer == $commander->rPlayer) {
					# si c'est une de nos planètes
					# on tente de se poser
					$this->uChangeBase($commander->id);
				} else {
					# si c'est une base alliée
					# on repart
					$this->comeBack($place, $commander, $commanderPlace, $playerBonus);
					$this->placeManager->sendNotif($place, Place::CHANGELOST, $commander);
				}
			}

		# colonisation
		} else {
			# faire un combat
			LiveReport::$type = Commander::COLO;
			LiveReport::$dFight = $commander->dArrival;
			LiveReport::$isLegal = Report::LEGAL;

			$this->startFight($place, $commander, $commanderPlayer);

			# victoire
			if ($commander->getStatement() !== Commander::DEAD) {
				# attribuer le rPlayer à la Place !
				$place->rPlayer = $commander->rPlayer;
				$place->commanders[] = $commander;
				$place->playerColor = $commander->playerColor;
				$place->typeOfBase = 4; 

				# créer une base
				$ob = new OrbitalBase();
				$ob->rPlace = $place->id;
				$ob->setRPlayer($commander->getRPlayer());
				$ob->setName('colonie');
				$ob->iSchool = 500;
				$ob->iAntiSpy = 500;
				$ob->resourcesStorage = 2000;
				$ob->uOrbitalBase = Utils::now();
				$ob->dCreation = Utils::now();
				$this->orbitalBaseManager->updatePoints($ob);

				$this->orbitalBaseManager->add($ob);

				# attibuer le commander à la place
				$commander->rBase = $place->id;
				$this->endTravel($commander, Commander::AFFECTED);
				$commander->line = 2;

				# ajout de la place en session
				if ($this->session->get('playerId') == $commander->getRPlayer()) {
					$this->session->addBase('ob', 
						$ob->getId(), 
						$ob->getName(), 
						$place->rSector, 
						$place->rSystem,
						'1-' . Game::getSizeOfPlanet($place->population),
						OrbitalBase::TYP_NEUTRAL);
				}
				
				# création du rapport
				$report = $this->createReport($place);

				$place->danger = 0;

				$this->placeManager->sendNotif($place, Place::CONQUEREMPTYSSUCCESS, $commander, $report->id);
				
				$this->eventDispatcher->dispatch(new PlaceOwnerChangeEvent($place));
			
			# défaite
			} else {
				# création du rapport
				$report = $this->createReport($place);

				# mise à jour du danger
				$percentage = (($report->pevAtEndD + 1) / ($report->pevInBeginD + 1)) * 100;
				$place->danger = round(($percentage * $place->danger) / 100);

				$this->placeManager->sendNotif($place, Place::CONQUEREMPTYFAIL, $commander);

				# enlever le commandant de la place
				foreach ($commanderPlace->commanders as $placeCommander) {
					if ($placeCommander->getId() == $commander->getId()) {
						unset($placeCommander);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}
			}
			$this->entityManager->flush();
		}
	}

	/**
	 * @param int $commanderId
	 */
	public function uReturnBase($commanderId) {
		$commander = $this->get($commanderId);
		$place = $this->placeManager->get($commander->rDestinationPlace);
		$commanderBase = $this->orbitalBaseManager->get($commander->rBase);
		
		$this->endTravel($commander, Commander::AFFECTED);

		$this->placeManager->sendNotif($place, Place::COMEBACK, $commander);

		if ($commander->resources > 0) {
			$this->orbitalBaseManager->increaseResources($commanderBase, $commander->resources, TRUE);
			$commander->resources = 0;
		}
		$this->entityManager->flush($commander);
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

		$commander->startPlaceName = $place->baseName;
		$commander->destinationPlaceName = $commander->oBName;
		$this->move($commander, $commander->rBase, $place->id, Commander::BACK, $length, $duration);
	}

	private function lootAnEmptyPlace(Place $place, $commander, $playerBonus) {
		$bonus = ($commander->rPlayer != $this->session->get('playerId'))
			? $playerBonus->bonus->get(PlayerBonus::SHIP_CONTAINER)
			: $this->session->get('playerBonus')->get(PlayerBonus::SHIP_CONTAINER);
	
		$storage = $commander->getPevToLoot() * Commander::COEFFLOOT;
		$storage += round($storage * ((2 * $bonus) / 100));

		$resourcesLooted = 0;
		$resourcesLooted = ($storage > $place->resources) ? $place->resources : $storage;

		$place->resources -= $resourcesLooted;
		$commander->resources = $resourcesLooted;

		LiveReport::$resources = $resourcesLooted;
	}

	private function lootAPlayerPlace($commander, $playerBonus, $placeBase) {
		$bonus = ($commander->rPlayer != $this->session->get('playerId'))
			? $playerBonus->bonus->get(PlayerBonus::SHIP_CONTAINER)
			: $this->session->get('playerBonus')->get(PlayerBonus::SHIP_CONTAINER);

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

	private function startFight(Place $place, $commander, $player, $enemyCommander = NULL, $enemyPlayer = NULL, $pvp = FALSE) {
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

	private function createReport(Place $place) {
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
		$population = $place->population;
		$vCommander = new Commander();
		$vCommander->id = 'Null';
		$vCommander->rPlayer = ID_GAIA;
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
