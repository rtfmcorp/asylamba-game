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

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
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

class CommanderManager extends Manager {
    protected $managerType = '_Commander';
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
	 * @param Database $database
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
		Database $database,
		FightManager $fightManager,
		OrbitalBaseManager $orbitalBaseManager,
		PlayerManager $playerManager,
		PlayerBonusManager $playerBonusManager,
		PlaceManager $placeManager,
		Session $session,
		CTC $ctc,
		$commanderBaseLevel
	) {
		parent::__construct($database);
		$this->fightManager = $fightManager;
		$this->orbitalBaseManager = $orbitalBaseManager;
		$this->playerManager = $playerManager;
		$this->playerBonusManager = $playerBonusManager;
		$this->placeManager = $placeManager;
		$this->session = $session;
		$this->ctc = $ctc;
		$this->commanderBaseLevel = $commanderBaseLevel;
	}
	
    //charge depuis la base de donnée avec ce qu'on veut
    public function load($where = array(), $order = array(), $limit = array()) {
            $formatWhere = Utils::arrayToWhere($where);
            $formatOrder = Utils::arrayToOrder($order);
            $formatLimit = Utils::arrayToLimit($limit);

            $qr = $this->database->prepare('SELECT c.*,
                            o.iSchool, o.name AS oName,
                            p.name AS pName,
                            p.rColor AS pColor,
                            pd.population AS destinationPlacePop,
                            ps.population AS startPlacePop,
                            dp.name AS dpName,
                            sp.name AS spName,
                            sq.id AS sqId,
                            sq.ship0 AS sqShip0,
                            sq.ship1 AS sqShip1,
                            sq.ship2 AS sqShip2,
                            sq.ship3 AS sqShip3,
                            sq.ship4 AS sqShip4,
                            sq.ship5 AS sqShip5,
                            sq.ship6 AS sqShip6,
                            sq.ship7 AS sqShip7,
                            sq.ship8 AS sqShip8,
                            sq.ship9 AS sqShip9,
                            sq.ship10 AS sqShip10,
                            sq.ship11 AS sqShip11,
                            sq.dCreation AS sqDCreation,
                            sq.DLastModification AS sqDLastModification
                    FROM commander AS c
                    LEFT JOIN orbitalBase AS o
                            ON o.rPlace = c.rBase
                    LEFT JOIN player AS p
                            ON p.id = c.rPlayer
                    LEFT JOIN orbitalBase AS dp
                            ON dp.rPlace = c.rDestinationPlace
                            LEFT JOIN place AS pd
                                    ON pd.id = c.rDestinationPlace
                    LEFT JOIN orbitalBase AS sp
                            ON sp.rPlace = c.rStartPlace
                            LEFT JOIN place AS ps
                                    ON ps.id = c.rStartPlace
                    LEFT JOIN squadron AS sq
                            ON sq.rCommander = c.id
                    ' . $formatWhere .'
                    ' . $formatOrder .'
                    ' . $formatLimit
            );

            foreach($where AS $v) {
                    if (is_array($v)) {
                            foreach ($v as $p) {
                                    $valuesArray[] = $p;
                            }
                    } else {
                            $valuesArray[] = $v;
                    }
            }

            if (empty($valuesArray)) {
                    $qr->execute();
            } else {
                    $qr->execute($valuesArray);
            }

            $awCommanders = $qr->fetchAll();
            $qr->closeCursor();

            if (count($awCommanders) > 0) {
                    for ($i = 0; $i < count($awCommanders); $i++) {
                            if ($i == 0 || $awCommanders[$i]['id'] != $awCommanders[$i - 1]['id']) {
                                    $commander = new Commander();

                                    $commander->id = $awCommanders[$i]['id'];
                                    $commander->name = $awCommanders[$i]['name'];
                                    $commander->avatar = $awCommanders[$i]['avatar'];
                                    $commander->rPlayer = $awCommanders[$i]['rPlayer'];
                                    $commander->playerName = $awCommanders[$i]['pName'];
                                    $commander->playerColor = $awCommanders[$i]['pColor'];
                                    $commander->rBase = $awCommanders[$i]['rBase'];
                                    $commander->comment = $awCommanders[$i]['comment'];
                                    $commander->sexe = $awCommanders[$i]['sexe'];
                                    $commander->age = $awCommanders[$i]['age'];
                                    $commander->level = $awCommanders[$i]['level'];
                                    $commander->experience = $awCommanders[$i]['experience'];
                                    $commander->uCommander = $awCommanders[$i]['uCommander'];
                                    $commander->palmares = $awCommanders[$i]['palmares'];
                                    $commander->statement = $awCommanders[$i]['statement'];
                                    $commander->line = $awCommanders[$i]['line'];
                                    $commander->dCreation = $awCommanders[$i]['dCreation'];
                                    $commander->dAffectation = $awCommanders[$i]['dAffectation'];
                                    $commander->dDeath = $awCommanders[$i]['dDeath'];
                                    $commander->oBName = $awCommanders[$i]['oName'];

                                    $commander->dStart = $awCommanders[$i]['dStart'];
                                    $commander->dArrival = $awCommanders[$i]['dArrival'];
                                    $commander->resources = $awCommanders[$i]['resources'];
                                    $commander->travelType = $awCommanders[$i]['travelType'];
                                    $commander->travelLength = $awCommanders[$i]['travelLength'];
                                    $commander->rStartPlace = $awCommanders[$i]['rStartPlace'];
                                    $commander->rDestinationPlace = $awCommanders[$i]['rDestinationPlace'];

                                    $commander->startPlaceName = ($awCommanders[$i]['spName'] == '') ? 'planète rebelle' : $awCommanders[$i]['spName'];
                                    $commander->destinationPlaceName = ($awCommanders[$i]['dpName'] == '') ? 'planète rebelle' : $awCommanders[$i]['dpName'];
                                    $commander->destinationPlacePop = $awCommanders[$i]['destinationPlacePop'];
                                    $commander->startPlacePop = $awCommanders[$i]['startPlacePop'];
                            }

                            $commander->squadronsIds[] = $awCommanders[$i]['sqId'];

                            $commander->armyInBegin[] = array(
                                    $awCommanders[$i]['sqShip0'], 
                                    $awCommanders[$i]['sqShip1'], 
                                    $awCommanders[$i]['sqShip2'], 
                                    $awCommanders[$i]['sqShip3'], 
                                    $awCommanders[$i]['sqShip4'], 
                                    $awCommanders[$i]['sqShip5'], 
                                    $awCommanders[$i]['sqShip6'], 
                                    $awCommanders[$i]['sqShip7'],
                                    $awCommanders[$i]['sqShip8'], 
                                    $awCommanders[$i]['sqShip9'], 
                                    $awCommanders[$i]['sqShip10'], 
                                    $awCommanders[$i]['sqShip11'], 
                                    $awCommanders[$i]['sqDCreation'], 
                                    $awCommanders[$i]['sqDLastModification']);

                            if ($i == count($awCommanders) - 1 || $awCommanders[$i]['id'] != $awCommanders[$i + 1]['id']) {
                                    $currentCommander = $this->_Add($commander);

                                    if ($this->currentSession->getUMode()) {
                                            $this->uCommander($currentCommander);
                                    }
                            }
                    }
            }
    }

    public function emptySession() {
            # empty the session, for player rankings
            $this->_EmptyCurrentSession();
            $this->newSession(FALSE);
    }

    //inscrit un nouveau commandant en bdd
    public function add($newCommander) {
            $qr = 'INSERT INTO commander
            SET 
                    name = ?,
                    avatar = ?,
                    rPlayer = ?,
                    rBase = ?,
                    sexe = ?,
                    age = ?,
                    level = ?,
                    experience = ?,
                    uCommander = ?,
                    statement = ?,
                    dCreation = ?';
            $qr = $this->database->prepare($qr);
            $aw = $qr->execute(array(
                    $newCommander->name,
                    $newCommander->avatar,
                    $newCommander->rPlayer,
                    $newCommander->rBase,
                    $newCommander->sexe,
                    $newCommander->age,
                    $newCommander->level,
                    $newCommander->experience,
                    Utils::now(),
                    $newCommander->statement,
                    $newCommander->dCreation,
                    ));
            $newCommander->setId($this->database->lastInsertId());

            $nbrSquadrons = $newCommander->getLevel();
            $maxId = $this->database->lastInsertId();
            $qr2 = 'INSERT INTO 
                    squadron(rCommander, dCreation)
                    VALUES(?, NOW())';
            $qr2 = $this->database->prepare($qr2);

            for ($i = 0; $i < $nbrSquadrons; $i++) {
                    $aw2 = $qr2->execute(array($maxId));
            }

            $lastSquadronId = $this->database->lastInsertId();
            for ($i = 0; $i < count($newCommander->getArmy()); $i++) {
                    $newCommander->getSquadron[$i]->setId($lastSquadronId);
                    $lastSquadronId--;
            }

            $this->_Add($newCommander);
    }

    //réécrit la base de donnée (à l'issue d'un combat par exemple)
    public function save() {
            $commanders = $this->_Save();
            foreach ($commanders AS $k => $commander) {
                    $qr = 'UPDATE commander
                            SET				
                                    name = ?,
                                    avatar = ?,
                                    rPlayer = ?,
                                    rBase = ?,
                                    comment = ?,
                                    sexe = ?,
                                    age = ?,
                                    level = ?,
                                    experience = ?,
                                    uCommander = ?,
                                    palmares = ?,
                                    statement = ?,
                                    `line` = ?,
                                    dStart = ?,
                                    dArrival = ?,
                                    resources = ?,
                                    travelType = ?,
                                    travelLength = ?,
                                    rStartPlace	= ?,
                                    rDestinationPlace = ?,
                                    dCreation = ?,
                                    dAffectation = ?,
                                    dDeath = ?

                            WHERE id = ?';

                    $qr = $this->database->prepare($qr);
                    //uper les commandants
                    $qr->execute(array( 				
                            $commander->name,
                            $commander->avatar,
                            $commander->rPlayer,
                            $commander->rBase,
                            $commander->comment,
                            $commander->sexe,
                            $commander->age,
                            $commander->level,
                            $commander->experience,
                            $commander->uCommander,
                            $commander->palmares,
                            $commander->statement,
                            $commander->line,
                            $commander->dStart,
                            $commander->dArrival,
                            $commander->resources,
                            $commander->travelType,
                            $commander->travelLength,
                            $commander->rStartPlace,
                            $commander->rDestinationPlace,
                            $commander->dCreation,
                            $commander->dAffectation,
                            $commander->dDeath,
                            $commander->id));

                    $qr = 'UPDATE squadron SET
                            rCommander = ?,
                            ship0 = ?,
                            ship1 = ?,
                            ship2 = ?,
                            ship3 = ?,
                            ship4 = ?,
                            ship5 = ?,
                            ship6 = ?,
                            ship7 = ?,
                            ship8 = ?,
                            ship9 = ?,
                            ship10 = ?,
                            ship11 = ?,
                            DLAstModification = NOW()
                    WHERE id = ?';

                    $qr = $this->database->prepare($qr);
                    $army = $commander->getArmy();

                    foreach ($army AS $squadron) {
                            //uper les escadrilles
                            $qr->execute(array(
                                    $squadron->getRCommander(),
                                    $squadron->getNbrShipByType(0),
                                    $squadron->getNbrShipByType(1),
                                    $squadron->getNbrShipByType(2),
                                    $squadron->getNbrShipByType(3),
                                    $squadron->getNbrShipByType(4),
                                    $squadron->getNbrShipByType(5),
                                    $squadron->getNbrShipByType(6),
                                    $squadron->getNbrShipByType(7),
                                    $squadron->getNbrShipByType(8),
                                    $squadron->getNbrShipByType(9),
                                    $squadron->getNbrShipByType(10),
                                    $squadron->getNbrShipByType(11),
                                    $squadron->getId()
                            ));
                    }
                    if ($commander->getLevel() > $commander->getSizeArmy()) {
                            //on créé une nouvelle squadron avec rCommander correspondant
                            $nbrSquadronToCreate = $commander->getLevel() - $commander->getSizeArmy();
                            $qr = 'INSERT INTO 
                            squadron (rCommander, dCreation)	
                            VALUES (' . $commander->getId() . ', NOW())';
                            $i = 1;
                            while ($i < $nbrSquadronToCreate) {
                                    $qr .= ',(' . $commander->getId() . ', NOW())';
                                    $i++;
                            }
                            $qr = $this->database->prepare($qr);
                            $qr->execute();
                    }
            }
            $this->isUpdate = TRUE;
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
			$S_PLM1 = $this->playerManager->getCurrentSession();
			$this->playerManager->newSession();
			$this->playerManager->load(array('id' => $commander->rPlayer));
			
			$exp = round($commander->earnedExperience / Commander::COEFFEXPPLAYER);
			$this->playerManager->increaseExperience($this->playerManager->get(0), $exp);

			if ($enemyCommander->isAttacker == TRUE) {
				LiveReport::$expPlayerD = $exp;
			} else {
				LiveReport::$expPlayerA = $exp;
			}
			
			$this->playerManager->changeSession($S_PLM1);
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
		$S_OBM = $this->orbitalBaseManager->getCurrentSession();
		$this->orbitalBaseManager->newSession();
		$this->orbitalBaseManager->load(array('rPlace' => $commander->rBase));

		if ($this->orbitalBaseManager->size() > 0) {
			for ($i = 0; $i < count($commander->squadronsIds); $i++) {
				for ($j = 0; $j < 12; $j++) {
					$this->orbitalBaseManager->get()->setShipStorage($j, $this->orbitalBaseManager->get()->getShipStorage($j) + $commander->getSquadron($i)->getNbrShipByType($j));
				}
				$commander->getSquadron($i)->emptySquadron();
			}
		}

		$this->orbitalBaseManager->changeSession($S_OBM);
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
	public function engage(Commander $commander, $enemyCommander, $commanderCommander) {
		$commander->setArmy();

		for ($i = 0; $i < count($commander->squadronsIds); $i++) {
			$commander->getSquadron($i)->relId = 0;
		}
		$idSquadron = 0;
		foreach ($commander->army as $squadron) {
			if ($squadron->getNbrShips() != 0 AND $squadron->getLineCoord() * 3 <= FightManager::getCurrentLine()) {
				$enemyCommander = $squadron->engage($enemyCommander, $idSquadron, $commander->id, $commander->name, $commanderCommander);
			}
			$idSquadron++;
		}
		return $enemyCommander;
	}

	public function getPosition (Commander $commander, $x1, $y1, $x2, $y2) {
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

			$S_OBM = $this->orbitalBaseManager->getCurrentSession();
			$this->orbitalBaseManager->newSession();
			$this->orbitalBaseManager->load(array('rPlace' => $commander->rBase));
			$ob = $this->orbitalBaseManager->get();
			$this->orbitalBaseManager->changeSession($S_OBM);
                        
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
				$this->ctc->add($hour, $this, 'uExperienceInSchool', $commander, array($commander, $ob, $playerBonus));
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
