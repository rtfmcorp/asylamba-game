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
			$p->setDescription($aw['description']);
			$p->setCredit($aw['credit']);
			$p->uPlayer = $aw['uPlayer'];
			$p->setExperience($aw['experience']);
			$p->setLevel($aw['level']);
			$p->setVictory($aw['victory']);
			$p->setDefeat($aw['defeat']);
			$p->setStepTutorial($aw['stepTutorial']);
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

	public function search($string, $quantity = 20, $offset = 0) {
		$this->writeMode = FALSE;
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
			$p->setDescription($aw['description']);
			$p->setCredit($aw['credit']);
			$p->uPlayer = $aw['uPlayer'];
			$p->setExperience($aw['experience']);
			$p->setLevel($aw['level']);
			$p->setVictory($aw['victory']);
			$p->setDefeat($aw['defeat']);
			$p->setStepTutorial($aw['stepTutorial']);
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
			player(bind, rColor, name, avatar, status, description, credit, uPlayer, experience, level, victory, defeat, stepTutorial, iUniversity, partNaturalSciences, partLifeSciences, partSocialPoliticalSciences, partInformaticEngineering, dInscription, dLastConnection, dLastActivity, premium, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$p->getBind(),
			$p->getRColor(),
			$p->getName(),
			$p->getAvatar(),
			$p->getStatus(),
			$p->getDescription(),
			$p->getCredit(),
			$p->uPlayer,
			$p->getExperience(),
			$p->getLevel(),
			$p->getVictory(),
			$p->getDefeat(),
			$p->getStepTutorial(),
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
					description = ?,
					credit = ?,
					uPlayer = ?,
					experience = ?,
					level = ?,
					victory = ?,
					defeat = ?,
					stepTutorial = ?,
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
				$p->getDescription(),
				$p->getCredit(),
				$p->uPlayer,
				$p->getExperience(),
				$p->getLevel(),
				$p->getVictory(),
				$p->getDefeat(),
				$p->getStepTutorial(),
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

	//LOAD SPECIAUX

public function loadByPopulation($where = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'p.');
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT p.*
			FROM player AS p
			' . $formatWhere . '
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
			$p->setDescription($aw['description']);
			$p->setCredit($aw['credit']);
			$p->uPlayer = $aw['uPlayer'];
			$p->setExperience($aw['experience']);
			$p->setLevel($aw['level']);
			$p->setVictory($aw['victory']);
			$p->setDefeat($aw['defeat']);
			$p->setStepTutorial($aw['stepTutorial']);
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
}
?>