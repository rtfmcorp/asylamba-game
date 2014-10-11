<?php

/**
 * Color Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 26.11.13
*/

class ColorManager extends Manager {
	protected $managerType ='_Color';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'c.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT c.*
			FROM color AS c
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

		$aw = $qr->fetchAll();
		$qr->closeCursor();

		foreach($aw AS $awColor) {
			$color = new Color();

			$color->id = $awColor['id'];
			$color->alive = $awColor['alive'];
			$color->credits = $awColor['credits'];
			$color->players = $awColor['players'];
			$color->activePlayers = $awColor['activePlayers'];
			$color->points = $awColor['points'];
			$color->sectors = $awColor['sectors'];
			$color->electionStatement = $awColor['electionStatement'];
			$color->dLastElection = $awColor['dLastElection'];

			$this->_Add($color);
			$color->uMethod();
		}
	}

	public function save() {
		$colors = $this->_Save();

		foreach ($colors AS $color) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE color
				SET
					id = ?,
					alive = ?,
					credits = ?,
					players = ?,	
					activePlayers = ?,
					points = ?,
					sectors = ?,
					electionStatement = ?,
					dLastElection = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
					$color->id,
					$color->alive,
					$color->credits,
					$color->players,
					$color->activePlayers,
					$color->points,
					$color->sectors,
					$color->electionStatement,
					$color->dLastElection,
					$color->id
				));
		}
	}

	public function add($newColor) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO color
		SET
			id = ?,
			alive = ?,
			credits = ?,
			players = ?,		
			activePlayers = ?,
			points = ?,
			sectors = ?,
			electionStatement = ?,
			dLastElection = ?');
		$aw = $qr->execute(array(
				$color->id,
				$color->alive,
				$color->credits,
				$color->players,
				$color->activePlayers,
				$color->points,
				$color->sectors,
				$color->electionStatement,
				$color->dLastElection
			));

		$newColor->id = $db->lastInsertId();

		$this->_Add($newColor);

		return $newColor->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM color WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	// FONCTIONS STATICS
	public static function updateInfos($id) {
		self::updatePlayers($id);
		self::updateActivePlayers($id);
	}

	public static function updatePlayers($id) {
		include_once ZEUS;

		$_CLM1 = ASM::$clm->getCurrentSession();
		ASM::$clm->newSession();
		ASM::$clm->load(array('id' => $id));

		$_PAM = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY), 'rColor' => $id));

		ASM::$clm->getById($id)->players = ASM::$pam->size();	

		ASM::$pam->changeSession($_PAM);
		ASM::$clm->changeSession($_CLM1);
	}

	public static function updateActivePlayers($id) {
		include_once ZEUS;

		$_CLM1 = ASM::$clm->getCurrentSession();
		ASM::$clm->newSession();
		ASM::$clm->load(array('id' => $id));

		$_PAM = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('statement' => PAM_ACTIVE, 'rColor' => $id));
		
		ASM::$clm->getById($id)->activePlayers = ASM::$pam->size();

		ASM::$pam->changeSession($_PAM);
		ASM::$clm->changeSession($_CLM1);
	}
}
