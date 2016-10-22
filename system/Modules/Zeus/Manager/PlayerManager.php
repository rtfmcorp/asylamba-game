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

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Worker\API;
use Asylamba\Classes\Worker\ASM;

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Hermes\Model\Notification;

use Asylamba\Modules\Gaia\Manager\GalaxyColorManager;

class PlayerManager extends Manager {
	protected $managerType = '_Player';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'p.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT p.*
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

	// 	$db = DataBase::getInstance();
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

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT p.*
			FROM player AS p
			WHERE LOWER(name) LIKE LOWER(?)
			' . $formatOrder . ' 
			' . $formatLimit
		);

		$qr->execute(array($search));

		$this->fill($qr);
	}

	protected function fill($qr) {
		while ($aw = $qr->fetch()) {
			$p = new Player();

			$p->setId($aw['id']);
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

			if ($this->currentSession->getUMode()) {
				$currentP->uMethod();
			}
		}
	}

	public function add(Player $p) {
		$db = Database::getInstance();
		$qr = $db->prepare('INSERT INTO
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

		$p->setId($db->lastInsertId());

		$this->_Add($p);
	}

	public function save() {
		$players = $this->_Save();

		foreach ($players AS $p) {
			$db = Database::getInstance();
			$qr = $db->prepare('UPDATE player
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
		$db = Database::getInstance();
		$qr = $db->prepare('DELETE FROM player WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}

	public function kill($player) {
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('id' => $player));
		$p = ASM::$pam->get();

		# API call
		$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
		$api->playerIsDead($p->bind, APP_ID);

		# check if there is no other player with the same dead-name
		$futureName = '&#8224; ' . $p->name . ' ';
		$S_PAM_INSCR = ASM::$pam->getCurrentSession();
		while(TRUE) {

			ASM::$pam->newSession(FALSE);
			ASM::$pam->load(array('name' => $futureName));

			if (ASM::$pam->size() == 0) {
				break;
			} else {
				# on ajoute un 'I' à chaque fois
				$futureName .= 'I';
			}
		};
		ASM::$pam->changeSession($S_PAM_INSCR);

		# deadify the player
		$p->name = $futureName;
		$p->statement = PAM_DEAD;
		$p->bind = NULL;
		$p->rColor = 0;

		ASM::$pam->changeSession($S_PAM1);
	}

	public function reborn($player) {
		include_once GAIA;
		include_once ATHENA;
		include_once HERMES;

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('id' => $player));
		$player = ASM::$pam->get();

		# sector choice 
		$S_SEM1 = ASM::$sem->getCurrentSession();
		ASM::$sem->newSession(FALSE);
		ASM::$sem->load(array('rColor' => $player->rColor), array('id', 'DESC'));

		$placeFound = FALSE;
		$place = NULL;
		for ($i = 0; $i < ASM::$sem->size(); $i++) { 
			$sector = ASM::$sem->get($i);

			# place choice
			$db = Database::getInstance();
			$qr = $db->prepare('SELECT * FROM place AS p
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

		ASM::$sem->changeSession($S_SEM1);

		if ($placeFound) {

			# reinitialize some values of the player
			$player->iUniversity = 1000;
			$player->partNaturalSciences = 25;
			$player->partLifeSciences = 25;
			$player->partSocialPoliticalSciences = 25;
			$player->partInformaticEngineering = 25;
			$player->statement = PAM_ACTIVE;
			$player->factionPoint = 0;

			$technos = new Technology($player->id);
			$levelAE = $technos->getTechnology(Technology::BASE_QUANTITY);
			if ($levelAE != 0) {
				Technology::deleteByRPlayer($player->id, Technology::BASE_QUANTITY);
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
			ASM::$obm->add($ob);

			# modification de la place
			$_PLM8761 = ASM::$plm->getCurrentSession();
			ASM::$plm->newSession();
			ASM::$plm->load(array('id' => $place));
			ASM::$plm->get()->rPlayer = $player->id;
			ASM::$plm->get()->population = 50;
			ASM::$plm->get()->coefResources = 60;
			ASM::$plm->get()->coefHistory = 20;
			ASM::$plm->changeSession($_PLM8761);

			GalaxyColorManager::apply();

			# envoi d'une notif

			$notif = new Notification();
			$notif->setRPlayer($player->id);
			$notif->setTitle('Nouvelle Colonie');
			$notif->addBeg()
				->addTxt('Vous vous êtes malheureusement fait prendre votre dernière planète. Une nouvelle colonie vous a été attribuée')
				->addEnd();
			ASM::$ntm->add($notif);
		} else {
			# si on ne trouve pas de lieu pour le faire poper ou si la faction n'a plus de secteur, le joueur meurt
			$this->kill($player);
		}
		ASM::$pam->changeSession($S_PAM1);
	}

	public static function count($where = array()) {
		$formatWhere = Utils::arrayToWhere($where);

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT COUNT(id) AS nbr FROM player ' . $formatWhere);

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
}