<?php

/**
 * PlayerManager
 *
 * @author Gil Clavien
 * @copyright Expansion - le jeu
 *
 * @package Zeus
 * @version 20.05.13
 **/
namespace Asylamba\Modules\Zeus\Manager;

use Asylamba\Classes\Worker\Manager;

use Asylamba\Classes\Worker\CTC;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Worker\API;
use Asylamba\Classes\Container\Session;

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Athena\Model\Transaction;

use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Ares\Manager\CommanderManager;
use Asylamba\Modules\Gaia\Manager\GalaxyColorManager;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use Asylamba\Modules\Gaia\Manager\PlaceManager;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Promethee\Manager\ResearchManager;
use Asylamba\Modules\Athena\Manager\TransactionManager;
use Asylamba\Modules\Athena\Manager\CommercialRouteManager;
use Asylamba\Modules\Promethee\Manager\TechnologyManager;

use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Container\ArrayList;

use Asylamba\Classes\Exception\ErrorException;

class PlayerManager extends Manager {
	/** @var string */
	protected $managerType = '_Player';
	/** @var GalaxyColorManager **/
	protected $galaxyColorManager;
	/** @var SectorManager */
	protected $sectorManager;
	/** @var NotificationManager **/
	protected $notificationManager;
	/** @var OrbitalBaseManager **/
	protected $orbitalBaseManager;
	/** @var PlaceManager **/
	protected $placeManager;
	/** @var CommanderManager **/
	protected $commanderManager;
	/** @var ColorManager **/
	protected $colorManager;
	/** @var ResearchManager **/
	protected $researchManager;
	/** @var TransactionManager **/
	protected $transactionManager;
	/** @var CommercialRouteManager **/
	protected $commercialRouteManager;
	/** @var TechnologyManager **/
	protected $technologyManager;
	/** @var PlayerBonusManager **/
	protected $playerBonusManager;
	/** @var CTC **/
	protected $ctc;
	/** @var Session **/
	protected $session;
	/** @var int **/
	protected $playerBaseLevel;
	
	/**
	 * @param Database $database
	 * @param GalaxyColorManager $galaxyColorManager
	 * @param SectorManager $sectorManager
	 * @param NotificationManager $notificationManager
	 * @param OrbitalBaseManager $orbitalBaseManager
	 * @param PlaceManager $placeManager
	 * @param CommanderManager $commanderManager
	 * @param ColorManager $colorManager
	 * @param ResearchManager $researchManager
	 * @param TransactionManager $transactionManager
	 * @param CommercialRouteManager $commercialRouteManager
	 * @param TechnologyManager $technologyManager
	 * @param PlayerBonusManager $playerBonusManager
	 * @param CTC $ctc
	 * @param Session $session
	 * @param int $playerBaseLevel
	 */
	public function __construct(
		Database $database,
		GalaxyColorManager $galaxyColorManager,
		SectorManager $sectorManager,
		NotificationManager $notificationManager,
		OrbitalBaseManager $orbitalBaseManager,
		PlaceManager $placeManager,
		CommanderManager $commanderManager,
		ColorManager $colorManager,
		ResearchManager $researchManager,
		TransactionManager $transactionManager,
		CommercialRouteManager $commercialRouteManager,
		TechnologyManager $technologyManager,
		PlayerBonusManager $playerBonusManager,
		CTC $ctc,
		Session $session,
		$playerBaseLevel
	)
	{
		parent::__construct($database);
		$this->galaxyColorManager = $galaxyColorManager;
		$this->sectorManager = $sectorManager;
		$this->notificationManager = $notificationManager;
		$this->orbitalBaseManager = $orbitalBaseManager;
		$this->placeManager = $placeManager;
		$this->commanderManager = $commanderManager;
		$this->colorManager = $colorManager;
		$this->researchManager = $researchManager;
		$this->transactionManager = $transactionManager;
		$this->commercialRouteManager = $commercialRouteManager;
		$this->technologyManager = $technologyManager;
		$this->playerBonusManager = $playerBonusManager;
		$this->ctc = $ctc;
		$this->session = $session;
		$this->playerBaseLevel = $playerBaseLevel;
	}
			
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'p.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT p.*
			FROM player AS p
			' . $formatWhere . '
			' . $formatOrder . '
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

		if(empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		$this->fill($qr);
	}

	// public function loadFromFactionByRank($factionId) {
	// 	$order = ['generalPosition', 'ASC'];

	// 	$formatOrder = Utils::arrayToOrder($order);
	// 	$formatLimit = Utils::arrayToLimit([]);

	// 	$db = Database::getInstance();
	// 	$qr = $db->prepare('SELECT p.*
	// 		FROM playerRanking AS pl
	// 		LEFT JOIN player AS p 
	// 			ON pl.rPlayer = p.id
	// 		WHERE p.rColor = ' . $factionId . '
	// 		' . $formatOrder . '
	// 		' . $formatLimit
	// 	);

	// 	if(empty($valuesArray)) {
	// 		$qr->execute();
	// 	} else {
	// 		$qr->execute($valuesArray);
	// 	}

	// 	$this->fill($qr);
	// }

	public function search($search, $order = array(), $limit = array()) {
		$search = '%' . $search . '%';
		
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT p.*
			FROM player AS p
			WHERE LOWER(name) LIKE LOWER(?)
			' . $formatOrder . ' 
			' . $formatLimit
		);

		$qr->execute(array($search));

		$this->fill($qr);
	}

	protected function fill($qr) {
		$playerId = $this->session->get('playerId');
		while ($aw = $qr->fetch()) {
			$p = new Player();

			
			$p->setId($aw['id'], $playerId);
			$p->setBind($aw['bind']);
			$p->setRColor($aw['rColor']);
			$p->setName($aw['name']);
			$p->sex = $aw['sex'];
			$p->description = $aw['description'];
			$p->setAvatar($aw['avatar']);
			$p->setStatus($aw['status']);
			$p->rGodfather = $aw['rGodfather'];
			$p->setCredit($aw['credit']);
			$p->uPlayer = $aw['uPlayer'];
			$p->setExperience($aw['experience']);
			$p->factionPoint = $aw['factionPoint'];
			$p->setLevel($aw['level']);
			$p->setVictory($aw['victory']);
			$p->setDefeat($aw['defeat']);
			$p->setStepTutorial($aw['stepTutorial']);
			$p->stepDone = $aw['stepDone'];
			$p->iUniversity = $aw['iUniversity'];
			$p->partNaturalSciences = $aw['partNaturalSciences'];
			$p->partLifeSciences = $aw['partLifeSciences'];
			$p->partSocialPoliticalSciences = $aw['partSocialPoliticalSciences'];
			$p->partInformaticEngineering = $aw['partInformaticEngineering'];
			$p->setDInscription($aw['dInscription']);
			$p->setDLastConnection($aw['dLastConnection']);
			$p->setDLastActivity($aw['dLastActivity']);
			$p->setPremium($aw['premium']);
			$p->setStatement($aw['statement']);

			$currentP = $this->_Add($p);

			if ($currentP->isSynchronized()) {
				$this->saveSessionData($currentP);
			}
			
			if ($this->currentSession->getUMode()) {
				$this->uMethod($currentP);
			}
		}
	}
	
	/**
	 * @param Player $player
	 */
	public function saveSessionData(Player $player)
	{
		if(!$this->session->exist('playerInfo')) {
			$this->session->add('playerInfo', new ArrayList());
		}
		$this->session->get('playerInfo')->add('color', $player->getRColor());
		$this->session->get('playerInfo')->add('name', $player->getName());
		$this->session->get('playerInfo')->add('avatar', $player->getAvatar());
		$this->session->get('playerInfo')->add('credit', $player->getCredit());
		$this->session->get('playerInfo')->add('experience', $player->getExperience());
		$this->session->get('playerInfo')->add('level', $player->getLevel());
	}

	public function add(Player $p) {
		$qr = $this->database->prepare('INSERT INTO
			player(bind, rColor, name, sex, description, avatar, status, rGodfather, credit, uPlayer, experience, factionPoint, level, victory, defeat, stepTutorial, stepDone, iUniversity, partNaturalSciences, partLifeSciences, partSocialPoliticalSciences, partInformaticEngineering, dInscription, dLastConnection, dLastActivity, premium, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$p->getBind(),
			$p->getRColor(),
			$p->getName(),
			$p->sex,
			$p->description,
			$p->getAvatar(),
			$p->getStatus(),
			$p->rGodfather,
			$p->getCredit(),
			$p->uPlayer,
			$p->getExperience(),
			$p->factionPoint,
			$p->getLevel(),
			$p->getVictory(),
			$p->getDefeat(),
			$p->getStepTutorial(),
			$p->stepDone,
			$p->iUniversity,
			$p->partNaturalSciences,
			$p->partLifeSciences,
			$p->partSocialPoliticalSciences,
			$p->partInformaticEngineering,
			$p->getDInscription(),
			$p->getDLastConnection(),
			$p->getDLastActivity(),
			$p->getPremium(),
			$p->getStatement()
		));

		$p->setId($this->database->lastInsertId(), $this->session->get('playerId'));

		$this->_Add($p);
	}

	public function save() {
		$players = $this->_Save();

		foreach ($players AS $p) {
			$qr = $this->database->prepare('UPDATE player
				SET	id = ?,
					bind = ?,
					rColor = ?,
					name = ?,
					sex = ?,
					description = ?,
					avatar = ?,
					status = ?,
					rGodfather = ?,
					credit = ?,
					uPlayer = ?,
					experience = ?,
					factionPoint = ?,
					level = ?,
					victory = ?,
					defeat = ?,
					stepTutorial = ?,
					stepDone = ?,
					iUniversity = ?,
					partNaturalSciences = ?,
					partLifeSciences = ?,
					partSocialPoliticalSciences = ?,
					partInformaticEngineering = ?,
					dInscription = ?,
					dLastConnection = ?,
					dLastActivity = ?,
					premium = ?,
					statement = ?
				WHERE id = ?');
			$qr->execute(array(
				$p->getId(),
				$p->getBind(),
				$p->getRColor(),
				$p->getName(),
				$p->sex,
				$p->description,
				$p->getAvatar(),
				$p->getStatus(),
				$p->rGodfather,
				$p->getCredit(),
				$p->uPlayer,
				$p->getExperience(),
				$p->factionPoint,
				$p->getLevel(),
				$p->getVictory(),
				$p->getDefeat(),
				$p->getStepTutorial(),
				$p->stepDone,
				$p->iUniversity,
				$p->partNaturalSciences,
				$p->partLifeSciences,
				$p->partSocialPoliticalSciences,
				$p->partInformaticEngineering,
				$p->getDInscription(),
				$p->getDLastConnection(),
				$p->getDLastActivity(),
				$p->getPremium(),
				$p->getStatement(),
				$p->getId()
			));
		}
	}

	public static function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM player WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}

	public function kill($player) {
		$S_PAM1 = $this->getCurrentSession();
		$this->newSession(FALSE);
		$this->load(array('id' => $player));
		$p = $this->get();

		# API call
		$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
		$api->playerIsDead($p->bind, APP_ID);

		# check if there is no other player with the same dead-name
		$futureName = '&#8224; ' . $p->name . ' ';
		$S_PAM_INSCR = $this->getCurrentSession();
		while(TRUE) {

			$this->newSession(FALSE);
			$this->load(array('name' => $futureName));

			if ($this->size() == 0) {
				break;
			} else {
				# on ajoute un 'I' à chaque fois
				$futureName .= 'I';
			}
		};
		$this->changeSession($S_PAM_INSCR);

		# deadify the player
		$p->name = $futureName;
		$p->statement = Player::DEAD;
		$p->bind = NULL;
		$p->rColor = 0;

		$this->changeSession($S_PAM1);
	}

	public function reborn($player) {
		$S_PAM1 = $this->getCurrentSession();
		$this->newSession(FALSE);
		$this->load(array('id' => $player));
		$player = $this->get();

		# sector choice 
		$S_SEM1 = $this->sectorManager->getCurrentSession();
		$this->sectorManager->newSession(FALSE);
		$this->sectorManager->load(array('rColor' => $player->rColor), array('id', 'DESC'));

		$placeFound = FALSE;
		$place = NULL;
		for ($i = 0; $i < $this->sectorManager->size(); $i++) { 
			$sector = $this->sectorManager->get($i);

			# place choice
			$qr = $this->database->prepare('SELECT * FROM place AS p
				INNER JOIN system AS sy ON p.rSystem = sy.id
					INNER JOIN sector AS se ON sy.rSector = se.id
				WHERE p.typeOfPlace = 1
					AND se.id = ?
					AND p.rPlayer IS NULL
				ORDER BY p.population ASC
				LIMIT 0, 30'
			);
			$qr->execute(array($sector->id));
			$aw = $qr->fetchAll();
			if ($aw !== NULL) {
				$placeFound = TRUE;
				$place = $aw[rand(0, (count($aw) - 1))][0];
				break;
			}
		}
		$this->sectorManager->changeSession($S_SEM1);

		if ($placeFound) {

			# reinitialize some values of the player
			$player->iUniversity = 1000;
			$player->partNaturalSciences = 25;
			$player->partLifeSciences = 25;
			$player->partSocialPoliticalSciences = 25;
			$player->partInformaticEngineering = 25;
			$player->statement = Player::ACTIVE;
			$player->factionPoint = 0;

			$technos = $this->technologyManager->getPlayerTechnology($player->id);
			$levelAE = $technos->getTechnology(Technology::BASE_QUANTITY);
			if ($levelAE != 0) {
				$this->technologyManager->deleteByRPlayer($player->id, Technology::BASE_QUANTITY);
			}
			
			# attribute new base and place to player
			$ob = new OrbitalBase();

			$ob->setRPlace($place);

			$ob->setRPlayer($player->id);
			$ob->setName("Colonie");

			$ob->setLevelGenerator(1);
			$ob->setLevelRefinery(1);
			$ob->setLevelDock1(0);
			$ob->setLevelDock2(0);
			$ob->setLevelDock3(0);
			$ob->setLevelTechnosphere(0);
			$ob->setLevelCommercialPlateforme(0);
			$ob->setLevelStorage(1);
			$ob->setLevelRecycling(0);
			$ob->setLevelSpatioport(0);
			$ob->setResourcesStorage(1000);

			$ob->updatePoints();

			# initialisation des investissement
			$ob->setISchool(500);
			$ob->setIAntiSpy(500);

			# ajout de la base
			$ob->uOrbitalBase = Utils::now();
			$ob->dCreation = Utils::now();
			$this->orbitalBaseManager->add($ob);

			# modification de la place
			$_PLM8761 = $this->placeManager->getCurrentSession();
			$this->placeManager->newSession();
			$this->placeManager->load(array('id' => $place));
			$this->placeManager->get()->rPlayer = $player->id;
			$this->placeManager->get()->population = 50;
			$this->placeManager->get()->coefResources = 60;
			$this->placeManager->get()->coefHistory = 20;
			$this->placeManager->changeSession($_PLM8761);

			$this->galaxyColorManager->apply();

			# envoi d'une notif

			$notif = new Notification();
			$notif->setRPlayer($player->id);
			$notif->setTitle('Nouvelle Colonie');
			$notif->addBeg()
				->addTxt('Vous vous êtes malheureusement fait prendre votre dernière planète. Une nouvelle colonie vous a été attribuée')
				->addEnd();
			$this->notificationManager->add($notif);
		} else {
			# si on ne trouve pas de lieu pour le faire poper ou si la faction n'a plus de secteur, le joueur meurt
			$this->kill($player);
		}
		$this->playerManager->changeSession($S_PAM1);
	}

	public function count($where = array()) {
		$formatWhere = Utils::arrayToWhere($where);

		$qr = $this->database->prepare('SELECT COUNT(id) AS nbr FROM player ' . $formatWhere);

		$valuesArray = array();
		foreach($where AS $v) {
			if (is_array($v)) {
				foreach ($v as $p) {
					$valuesArray[] = $p;
				}
			} else {
				$valuesArray[] = $v;
			}
		}

		$qr->execute($valuesArray);
		$aw = $qr->fetch();

		return $aw['nbr'];
	}

	public function uMethod(Player $player) {
		if ($player->statement != Player::DEAD) {
			$token = $this->ctc->createContext('player');
			$now   = Utils::now();

			if (Utils::interval($player->uPlayer, $now, 'h') > 0) {
				# update time
				$hours = Utils::intervalDates($now, $player->uPlayer);
				$player->uPlayer = $now;

				# load orbital bases
				$S_OBM1 = $this->orbitalBaseManager->getCurrentSession();
				$this->orbitalBaseManager->newSession();
				$this->orbitalBaseManager->load(array('rPlayer' => $player->id));

				# load the bonus
				$playerBonus = $this->playerBonusManager->getBonusByPlayer($player->id);
				$this->playerBonusManager->load($playerBonus);

				# load the commanders
				$S_COM1 = $this->commanderManager->getCurrentSession();
				$this->commanderManager->newSession();
				$this->commanderManager->load(
					array(
						'c.rPlayer' => $player->id,
						'c.statement' => array(Commander::AFFECTED, Commander::MOVING)), 
					array(
						'c.experience', 'DESC',
						'c.statement', 'ASC')
				);

				# load the researches
				$S_RSM1 = $this->researchManager->getCurrentSession();
				$this->researchManager->newSession();
				$this->researchManager->load(array('rPlayer' => $player->id));

				# load the colors (faction)
				$S_CLM1 = $this->colorManager->getCurrentSession();
				$this->colorManager->newSession();
				$this->colorManager->load(array());

				# load the transactions
				$S_TRM1 = $this->transactionManager->getCurrentSession();
				$this->transactionManager->newSession();
				$this->transactionManager->load(array('rPlayer' => $player->id, 'type' => Transaction::TYP_SHIP, 'statement' => Transaction::ST_PROPOSED));

				foreach ($hours as $key => $hour) {
					$this->ctc->add($hour, $this, 'uCredit', $player, array($player, $this->orbitalBaseManager->getCurrentSession(), $playerBonus, $this->commanderManager->getCurrentSession(), $this->researchManager->getCurrentSession(), $this->colorManager->getCurrentSession(), $this->transactionManager->getCurrentSession()));
				}
				$this->transactionManager->changeSession($S_TRM1);
				$this->colorManager->changeSession($S_CLM1);
				$this->researchManager->changeSession($S_RSM1);
				$this->commanderManager->changeSession($S_COM1);
				$this->orbitalBaseManager->changeSession($S_OBM1);
			}
			$this->ctc->applyContext($token);
		}
	}

	public function uCredit(Player $player, $obmSession, $playerBonus, $comSession, $rsmSession, $clmSession, $trmSession) {
		
		$S_OBM1 = $this->orbitalBaseManager->getCurrentSession();
		$this->orbitalBaseManager->changeSession($obmSession);

		$popTax = 0; $nationTax = 0;
		$credits = $player->credit;
		$schoolInvests = 0; $antiSpyInvests = 0;

		$totalGain = 0;

		# university investments
		$uniInvests = $player->iUniversity;
		$naturalTech = ($player->iUniversity * $player->partNaturalSciences / 100);
		$lifeTech = ($player->iUniversity * $player->partLifeSciences / 100);
		$socialTech = ($player->iUniversity * $player->partSocialPoliticalSciences / 100);
		$informaticTech = ($player->iUniversity * $player->partInformaticEngineering / 100);

		$S_CLM1 = $this->colorManager->getCurrentSession();
		$this->colorManager->changeSession($clmSession);
		
		for ($i = 0; $i < $this->orbitalBaseManager->size(); $i++) {
			$base = $this->orbitalBaseManager->get($i);
			$popTax = Game::getTaxFromPopulation($base->getPlanetPopulation(), $base->typeOfBase);
			$popTax += $popTax * $playerBonus->bonus->get(PlayerBonus::POPULATION_TAX) / 100;
			$nationTax = $base->tax * $popTax / 100;

			# revenu des routes commerciales
			$routesIncome = 0;
			$S_CRM1 =  $this->commercialRouteManager->getCurrentSession();
			$this->commercialRouteManager->changeSession($base->routeManager);
			for ($r = 0; $r < $this->commercialRouteManager->size(); $r++) {
				if ($this->commercialRouteManager->get($r)->getStatement() == CommercialRoute::ACTIVE) {
					$routesIncome += $this->commercialRouteManager->get($r)->getIncome();
				}
			}
			$routesIncome += $routesIncome * $playerBonus->bonus->get(PlayerBonus::COMMERCIAL_INCOME) / 100;
			$this->commercialRouteManager->changeSession($S_CRM1);

			$credits += ($popTax - $nationTax + $routesIncome);
			$totalGain += $popTax - $nationTax + $routesIncome;

			# investments
			$schoolInvests += $base->getISchool();
			$antiSpyInvests += $base->getIAntiSpy();

			# paiement à l'alliance
			if ($player->rColor != 0) {
				for ($j = 0; $j < $this->colorManager->size(); $j++) { 
					if ($this->colorManager->get($j)->id == $base->sectorColor) {
						$this->colorManager->get($j)->increaseCredit($nationTax);
						break;
					}
				}
			}
		}
		$this->colorManager->changeSession($S_CLM1);

		# si la balance de crédit est positive
		$totalInvests = $uniInvests + $schoolInvests + $antiSpyInvests;
		if ($credits >= $totalInvests) {
			$credits -= $totalInvests;
			$newCredit = $credits;
		} else { # si elle est négative
			$n = new Notification();
			$n->setRPlayer($player->id);
			$n->setTitle('Caisses vides');
			$n->addBeg()->addTxt('Domaine')->addSep();
			$n->addTxt('Vous ne disposez pas d\'assez de crédits.')->addBrk()->addTxt('Les impôts que vous percevez ne suffisent plus à payer vos investissements.');

			if ($totalInvests - $uniInvests <= $totalGain) {
				# we can decrease only the uni investments
				$newIUniversity = $totalGain - $schoolInvests - $antiSpyInvests;

				$player->iUniversity = $newIUniversity;
				$credits -= ($newIUniversity + $schoolInvests + $antiSpyInvests);

				# recompute the real amount for each research
				$naturalTech = ($player->iUniversity * $player->partNaturalSciences / 100);
				$lifeTech = ($player->iUniversity * $player->partLifeSciences / 100);
				$socialTech = ($player->iUniversity * $player->partSocialPoliticalSciences / 100);
				$informaticTech = ($player->iUniversity * $player->partInformaticEngineering / 100);

				$n->addBrk()->addTxt(' Vos investissements dans l\'université ont été modifiés afin qu\'aux prochaines relèves vous puissiez payer. Attention, cette situation ne vous apporte pas de crédits.');
			} else {
				# we have to decrease the other investments too
				# investments in university to 0
				$player->iUniversity = 0;
				# then we decrease the other investments with a ratio
				$ratioDifference = floor($totalGain / ($schoolInvests + $antiSpyInvests) * 100);

				$naturalTech = 0; $lifeTech = 0; $socialTech = 0; $informaticTech = 0;

				for ($i = 0; $i < $this->orbitalBaseManager->size(); $i++) {
					$orbitalBase = $this->orbitalBaseManager->get($i);

					$newISchool = ceil($orbitalBase->getISchool() * $ratioDifference / 100);
					$newIAntiSpy = ceil($orbitalBase->getIAntiSpy() * $ratioDifference / 100);

					$orbitalBase->setISchool($newISchool);
					$orbitalBase->setIAntiSpy($newIAntiSpy);

					$credits -= ($newISchool + $newIAntiSpy);

					$naturalTech += ($newISchool * $player->partNaturalSciences / 100);
					$lifeTech += ($newISchool * $player->partLifeSciences / 100);
					$socialTech += ($newISchool * $player->partSocialPoliticalSciences / 100);
					$informaticTech += ($newISchool * $player->partInformaticEngineering / 100);
					
				}
				$n->addTxt(' Seuls ')->addStg($ratioDifference . '%')->addTxt(' des crédits d\'investissements peuvent être honorés.')->addBrk();
				$n->addTxt(' Vos investissements dans l\'université ont été mis à zéro et les autres diminués de façon pondérée afin qu\'aux prochaines relèves vous puissiez payer. Attention, cette situation ne vous apporte pas de crédits.');
			}

			$n->addSep()->addLnk('financial', 'vers les finances →');
			$n->addEnd();
			
			$S_NTM1 = $this->notificationManager->getCurrentSession();
			$this->notificationManager->newSession();
			$this->notificationManager->add($n);
			$this->notificationManager->changeSession($S_NTM1);

			$newCredit = $credits;
		}

		# payer les commandants
		$nbOfComNotPaid = 0;
		$comList = new ArrayList();
		$S_COM1 = $this->commanderManager->getCurrentSession();
		$this->commanderManager->changeSession($comSession);
		for ($i = ($this->commanderManager->size() - 1); $i >= 0; $i--) {
			$commander = $this->commanderManager->get($i);
			if ($commander->getStatement() == 1 OR $commander->getStatement() == 2) {
				if ($newCredit >= (Commander::LVLINCOMECOMMANDER * $commander->getLevel())) {
					$newCredit -= (Commander::LVLINCOMECOMMANDER * $commander->getLevel());
				} else {
					# on remet les vaisseaux dans les hangars
					$commander->emptySquadrons();
					
					# on vend le commandant
					$commander->setStatement(Commander::ONSALE);
					$commander->setRPlayer(ID_GAIA);

					# TODO : vendre le commandant au marché 
					#			(ou alors le mettre en statement COM_DESERT et supprimer ses escadrilles)

					$comList->add($nbOfComNotPaid, $commander->getName());
					$nbOfComNotPaid++;
				}
			}
		}
		$this->commanderManager->changeSession($S_COM1);
		# si au moins un commandant n'a pas pu être payé --> envoyer une notif
		if ($nbOfComNotPaid) {	
			$n = new Notification();
			$n->setRPlayer($this->id);
			$n->setTitle('Commandant impayé');

			$n->addBeg()->addTxt('Domaine')->addSep();
			if ($nbOfComNotPaid == 1) {
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer votre commandant ' . $comList->get(0) . '. Celui-ci a donc déserté ! ');
				$n->addBrk()->addTxt('Il est allé proposer ses services sur le marché. Si vous voulez le récupérer, vous pouvez vous y rendre et le racheter.');
			} else {
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer certains de vos commandants. Ils ont donc déserté ! ')->addBrk();
				$n->addTxt('Voici la liste de ces commandants : ');
				for ($i = 0; $i < $comList->size() - 2; $i++) { 
					$n->addTxt($comList->get($i) . ', ');
				}
				$n->addTxt($comList->get($comList->size() - 2) . ' et ' . $comList->get($comList->size() - 1) . '.');
				$n->addBrk()->addTxt('Ils sont tous allés proposer leurs services sur le marché. Si vous voulez les récupérer, vous pouvez vous y rendre et les racheter.');
			}
			$n->addEnd();
			$S_NTM1 = $this->notificationManager->getCurrentSession();
			$this->notificationManager->newSession();
			$this->notificationManager->add($n);
			$this->notificationManager->changeSession($S_NTM1);
		}

		# payer l'entretien des vaisseaux
		# vaisseaux en vente
		$S_TRM1 = $this->transactionManager->getCurrentSession();
		$this->transactionManager->changeSession($trmSession);
		$transactionTotalCost = 0;
		for ($i = ($this->transactionManager->size() - 1); $i >= 0; $i--) {
			$transaction = $this->transactionManager->get($i);
			$transactionTotalCost += ShipResource::getInfo($transaction->identifier, 'cost') * ShipResource::COST_REDUCTION * $transaction->quantity;
		}
		if ($newCredit >= $transactionTotalCost) {
			$newCredit -= $transactionTotalCost;
		} else {
			$newCredit = 0;
		}
		$this->commanderManager->changeSession($S_TRM1);
		# vaisseaux affectés
		$S_COM1 = $this->commanderManager->getCurrentSession();
		$this->commanderManager->changeSession($comSession);
		for ($i = ($this->commanderManager->size() - 1); $i >= 0; $i--) {
			$commander = $this->commanderManager->get($i);
			$ships = $commander->getNbrShipByType();
			$cost = Game::getFleetCost($ships, TRUE);

			if ($newCredit >= $cost) {
				$newCredit -= $cost;
			} else {
				# on vend le commandant car on n'arrive pas à payer la flotte (trash hein)
				$commander->setStatement(Commander::ONSALE);
				$commander->setRPlayer(ID_GAIA);

				$n = new Notification();
				$n->setRPlayer($this->id);
				$n->setTitle('Flotte impayée');
				$n->addBeg()->addTxt('Domaine')->addSep();
				$n->addTxt('Vous n\'avez pas assez de crédits pour payer l\'entretien de la flotte de votre officier ' . $commander->name . '. Celui-ci a donc déserté ! ... avec la flotte, désolé.');
				$n->addEnd();
				$S_NTM1 = $this->notificationManager->getCurrentSession();
				$this->notificationManager->newSession();
				$this->notificationManager->add($n);
				$this->notificationManager->changeSession($S_NTM1);
			}
		}
		$this->commanderManager->changeSession($S_COM1);
		# vaisseaux sur la planète
		for ($i = 0; $i < $this->orbitalBaseManager->size(); $i++) {
			$base = $this->orbitalBaseManager->get($i);
			$cost = Game::getFleetCost($base->shipStorage, FALSE);

			if ($newCredit >= $cost) {
				$newCredit -= $cost;
			} else {
				# n'arrive pas à tous les payer !
				for ($j = ShipResource::SHIP_QUANTITY-1; $j >= 0; $j--) { 
					if ($base->shipStorage[$j] > 0) {
						$unitCost = ShipResource::getInfo($j, 'cost');

						$possibleMaintenable = floor($newCredit / $unitCost);
						if ($possibleMaintenable > $base->shipStorage[$j]) {
							$possibleMaintenable = $base->shipStorage[$j];
						}
						$newCredit -= $possibleMaintenable * $unitCost;

						$toKill = $base->shipStorage[$j] - $possibleMaintenable;
						if ($toKill > 0) {
							$base->removeShipFromDock($j, $toKill);

							$n = new Notification();
							$n->setRPlayer($player->id);
							$n->setTitle('Entretien vaisseau impayé');

							$n->addBeg()->addTxt('Domaine')->addSep();
							if ($toKill == 1) {
								$n->addTxt('Vous n\'avez pas assez de crédits pour payer l\'entretien d\'un(e) ' . ShipResource::getInfo($j, 'codeName') . ' sur ' . $base->name . '. Ce vaisseau part donc à la casse ! ');
							} else {
								$n->addTxt('Vous n\'avez pas assez de crédits pour payer l\'entretien de ' . $toKill . ' ' . ShipResource::getInfo($j, 'codeName') . 's sur ' . $base->name . '. Ces vaisseaux partent donc à la casse ! ');
							}
							$n->addEnd();
							$S_NTM1 = $this->notificationManager->getCurrentSession();
							$this->notificationManager->newSession();
							$this->notificationManager->add($n);
							$this->notificationManager->changeSession($S_NTM1);
						}
					}
				}
			}
		}
		
		# faire les recherches
		$S_RSM1 = $this->researchManager->getCurrentSession();
		$this->researchManager->changeSession($rsmSession);
		if ($this->researchManager->size() == 1) {
			# add the bonus
			$naturalTech += $naturalTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$lifeTech += $lifeTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$socialTech += $socialTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;
			$informaticTech += $informaticTech * $playerBonus->bonus->get(PlayerBonus::UNI_INVEST) / 100;

			$tech = $this->researchManager->get();
			$this->researchManager->update($tech, $player->id, $naturalTech, $lifeTech, $socialTech, $informaticTech);
		} else {
			throw new ErrorException('une erreur est survenue lors de la mise à jour des investissements de recherche pour le joueur ' . $player->id . '.');
		}
		$this->researchManager->changeSession($S_RSM1);

		$player->credit = $newCredit;
		if ($player->isSynchronized()) {
			$this->session->get('playerInfo')->add('credit', $newCredit);
		}

		$this->orbitalBaseManager->changeSession($S_OBM1);
	}

	// OBJECT METHOD
	public function increaseCredit(Player $player, $credit) {
		$player->credit += abs($credit);

		if ($player->isSynchronized()) {
			$this->session->get('playerInfo')->add('credit', $player->credit);
		}
	}

	public function decreaseCredit(Player $player, $credit) {
		if (abs($credit) > $player->credit) {
			$player->credit = 0;
		} else {
			$player->credit -= abs($credit);
		}
		if ($player->isSynchronized()) {
			$this->session->get('playerInfo')->add('credit', $player->credit);
		}
	}

	public function increaseExperience(Player $player, $exp) {
		$exp = round($exp);
		$player->experience += $exp;
		if ($player->isSynchronized()) {
			$this->session->get('playerInfo')->add('experience', $player->experience);
		}
		$nextLevel =  $this->playerBaseLevel * pow(2, ($player->level - 1));
		if ($player->experience >= $nextLevel) {
			$player->level++;
			if ($player->isSynchronized()) {
				$this->session->get('playerInfo')->add('level', $player->level);
			}
			$n = new Notification();
			$n->setTitle('Niveau supérieur');
			$n->setRPlayer($player->id);
			$n->addBeg()->addTxt('Félicitations, vous gagnez un niveau, vous êtes ')->addStg('niveau ' . $player->level)->addTxt('.');
			if ($player->level == 2) {
				$n->addSep()->addTxt('Attention, à partir de maintenant vous ne bénéficiez plus de la protection des nouveaux arrivants, n\'importe quel joueur peut désormais piller votre planète. ');
				$n->addTxt('Pensez donc à développer vos flottes pour vous défendre.');
			}
			if ($player->level == 4) {
				$n->addSep()->addTxt('Attention, à partir de maintenant un joueur adverse peut conquérir votre planète ! Si vous n\'en avez plus, le jeu est terminé pour vous. ');
				$n->addTxt('Pensez donc à étendre votre royaume en colonisant d\'autres planètes.');
			}
			$n->addEnd();

			$S_NTM1 = $this->notificationManager->getCurrentSession();
			$this->notificationManager->newSession();
			$this->notificationManager->add($n);
			$this->notificationManager->changeSession($S_NTM1);

			# parrainage : au niveau 3, le parrain gagne 1M crédits
			if ($player->level == 3 AND $player->rGodfather != NULL) {
				# add 1'000'000 credits to the godfather
				$S_PAM1 = $this->getCurrentSession();
				$this->newSession();
				$this->load(array('id' => $player->rGodfather));
				if ($this->size() == 1) {
					$this->increaseCredit($this->get(), 1000000);

					# send a message to the godfather
					$n = new Notification();
					$n->setRPlayer($this->rGodfather);
					$n->setTitle('Récompense de parrainage');
					$n->addBeg()->addTxt('Un de vos filleuls a atteint le niveau 3. ');
					$n->addTxt('Il s\'agit de ');
					$n->addLnk('embassy/player-' . $player->getId(), '"' . $player->name . '"')->addTxt('.');
					$n->addBrk()->addTxt('Vous venez de gagner 1\'000\'000 crédits. N\'hésitez pas à parrainer d\'autres personnes pour gagner encore plus.');
					$n->addEnd();

					$S_NTM2 = $this->notificationManager->getCurrentSession();
					$this->notificationManager->newSession();
					$this->notificationManager->add($n);
					$this->notificationManager->changeSession($S_NTM2);
				} 
				$this->changeSession($S_PAM1);
			}
		}
	}
}