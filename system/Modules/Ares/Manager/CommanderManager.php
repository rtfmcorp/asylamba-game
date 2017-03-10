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
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Zeus\Manager\PlayerBonusManager;
use Asylamba\Modules\Gaia\Manager\PlaceManager;
use Asylamba\Classes\Container\Session;
use Asylamba\Classes\Container\ArrayList;
use Asylamba\Classes\Worker\CTC;

use Asylamba\Modules\Ares\Model\LiveReport;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Ares\Model\Commander;

class CommanderManager
{
	/** @var EntityManager **/
	protected $entityManager;
	/** @var FightManager **/
	protected $fightManager;
	/** @var OrbitalBaseManager **/
	protected $orbitalBaseManager;
	/** @var PlayerManager **/
	protected $playerManager;
	/** @var PlayerBonusManager **/
	protected $playerBonusManager;
	/** @var PlaceManager **/
	protected $placeManager;
	/** @var Session **/
	protected $session;
	/** @var CTC **/
	protected $ctc;
	/** @var int **/
	protected $commanderBaseLevel;

	/**
	 * @param EntityManager $entityManager
	 * @param FightManager $fightManager
	 * @param OrbitalBaseManager $orbitalBaseManager
	 * @param PlayerManager $playerManager
	 * @param PlayerBonusManager $playerBonusManager
	 * @param PlaceManager $placeManager
	 * @param Session $session
	 * @param CTC $ctc
	 * @param int $commanderBaseLevel
	 */
	public function __construct(
		EntityManager $entityManager,
		FightManager $fightManager,
		OrbitalBaseManager $orbitalBaseManager,
		PlayerManager $playerManager,
		PlayerBonusManager $playerBonusManager,
		PlaceManager $placeManager,
		Session $session,
		CTC $ctc,
		$commanderBaseLevel
	) {
		$this->entityManager = $entityManager;
		$this->fightManager = $fightManager;
		$this->orbitalBaseManager = $orbitalBaseManager;
		$this->playerManager = $playerManager;
		$this->playerBonusManager = $playerBonusManager;
		$this->placeManager = $placeManager;
		$this->session = $session;
		$this->ctc = $ctc;
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

	public function uExperienceInSchool(Commander $commander, $ob, $playerBonus) {
		if ($commander->statement == Commander::INSCHOOL) {
			# investissement
			$invest  = $ob->iSchool;
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

	public function move(Commander $commander, $rDestinationPlace, $rStartPlace, $travelType, $travelLength, $duration) {
		$commander->rDestinationPlace = $rDestinationPlace;
		$commander->rStartPlace = $rStartPlace;
		$commander->travelType = $travelType;
		$commander->travelLength = $travelLength;
		$commander->statement = 2;

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

	public function uCommander(Commander $commander) {
		$token = $this->ctc->createContext();
		$now = Utils::now();

		# check s'il gagne de l'exp à l'école
		if (Utils::interval($commander->uCommander, $now, 'h') > 0 AND $commander->statement == Commander::INSCHOOL) {
			$nbrHours = Utils::intervalDates($now, $commander->uCommander);
			$commander->uCommander = $now;

			$orbitalBase = $this->orbitalBaseManager->get($commander->rBase);
                        
			$playerBonus = 0;
			if ($commander->rPlayer != $this->session->get('playerId')) {
				$playerBonus = $this->playerBonusManager->getBonusByPlayer($commander->rPlayer);
				/** @TOVERIFY **/
				$this->playerBonusManager->load($playerBonus);
				$playerBonus = $playerBonus->bonus;
			} else {
				$playerBonus = $this->session->get('playerBonus');
			}

			foreach ($nbrHours as $hour) {
				$this->ctc->add($hour, $this, 'uExperienceInSchool', $commander, array($commander, $orbitalBase, $playerBonus));
			}
		}

		# test si il y a des combats
		if ($commander->dArrival <= Utils::now() AND $commander->statement == Commander::MOVING AND $commander->hasToU) {
			$commander->hasToU = FALSE;

			$S_PLM = $this->placeManager->getCurrentSession();
			$this->placeManager->newSession();
			$this->placeManager->load(array('id' => $commander->rDestinationPlace));
			$pl = $this->placeManager->get();
			$this->placeManager->changeSession($S_PLM);
		}

		$this->ctc->applyContext($token);
	}
}
