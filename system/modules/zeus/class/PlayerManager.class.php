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

class PlayerManager extends Manager {
	protected $managerType = '_Player';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'p.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
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

		while($aw = $qr->fetch()) {
			$p = new Player();

			$p->setId($aw['id']);
			$p->setBind($aw['bind']);
			$p->setRColor($aw['rColor']);
			$p->setName($aw['name']);
			$p->setAvatar($aw['avatar']);
			$p->setStatus($aw['status']);
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

	public function kill($player) {

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => $player));
		$p = ASM::$pam->get();

		# API call
		$api = new API(GETOUT_ROOT);
		$api->playerIsDead($p->bind, APP_ID);

		# deadify the player
		$p->name = '&#8224; ' . $p->name;
		$p->statement = PAM_DEAD;
		$p->bind = NULL;
		$p->rColor = 0;

		ASM::$pam->changeSession($S_PAM1);
	}

	public function search($string, $quantity = 20, $offset = 0) {
		$db = Database::getInstance();
		$qr = $db->query('SELECT * FROM player WHERE LOWER(name) LIKE LOWER(\'%' . $string . '%\') LIMIT ' . intval($offset) . ', ' . intval($quantity));
		
		while($aw = $qr->fetch()) {
			$p = new Player();

			$p->setId($aw['id']);
			$p->setBind($aw['bind']);
			$p->setRColor($aw['rColor']);
			$p->setName($aw['name']);
			$p->setAvatar($aw['avatar']);
			$p->setStatus($aw['status']);
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

			$this->_Add($p);
		}
	}

	public function add(Player $p) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			player(bind, rColor, name, avatar, status, credit, uPlayer, experience, factionPoint, level, victory, defeat, stepTutorial, stepDone, iUniversity, partNaturalSciences, partLifeSciences, partSocialPoliticalSciences, partInformaticEngineering, dInscription, dLastConnection, dLastActivity, premium, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$p->getBind(),
			$p->getRColor(),
			$p->getName(),
			$p->getAvatar(),
			$p->getStatus(),
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
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE player
				SET	id = ?,
					bind = ?,
					rColor = ?,
					name = ?,
					avatar = ?,
					status = ?,
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
				$p->getAvatar(),
				$p->getStatus(),
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
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM player WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}
}
?>