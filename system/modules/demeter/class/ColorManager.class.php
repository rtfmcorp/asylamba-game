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
		$qr = $db->prepare('SELECT c.*,
				cl.rColorLinked AS clRColorLinked,
				cl.statement AS clStatement
			FROM color AS c
			LEFT JOIN colorLink AS cl
				ON cl.rColor = c.id
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

		$awColor = $qr->fetchAll();
		$qr->closeCursor();

		$colorsArray = array();
		for ($i = 0; $i < count($awColor); $i++) {
			if (array_key_exists($awColor[$i]['id'], $colorsArray)) {
				$colorsArray[$awColor[$i]['id']]->colorLink[$awColor[$i]['clRColorLinked']] = $awColor[$i]['clStatement'];
			} else {
				$color = new Color();
				$color->id = $awColor[$i]['id'];
				$color->alive = $awColor[$i]['alive'];
				$color->isWinner = $awColor[$i]['isWinner'];
				$color->credits = $awColor[$i]['credits'];
				$color->players = $awColor[$i]['players'];
				$color->activePlayers = $awColor[$i]['activePlayers'];
				$color->points = $awColor[$i]['points'];
				$color->sectors = $awColor[$i]['sectors'];
				$color->electionStatement = $awColor[$i]['electionStatement'];
				$color->isClosed = $awColor[$i]['isClosed'];
				$color->dLastElection = $awColor[$i]['dLastElection'];
				$color->colorLink[0] = Color::NEUTRAL;

				$colorsArray[$color->id] = $color;
			}
		}

		foreach ($colorsArray AS $color) {
			$color->colorLink = ksort($color->colorLink);
			$this->_Add($color);
			if ($this->currentSession->getUMode()) {
				$color->uMethod();
			}
		}
	}

	public function save() {
		$colors = $this->_Save();

		foreach ($colors AS $color) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE color
				SET
					alive = ?,
					isWinner = ?,
					credits = ?,
					players = ?,	
					activePlayers = ?,
					points = ?,
					sectors = ?,
					electionStatement = ?,
					isClosed = ?,
					dLastElection = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
					$color->alive,
					$color->isWinner,
					$color->credits,
					$color->players,
					$color->activePlayers,
					$color->points,
					$color->sectors,
					$color->electionStatement,
					$color->isClosed,
					$color->dLastElection,
					$color->id
				));

			$qr2 = $db->prepare('UPDATE colorLink SET
					statement = ? WHERE rColor = ? AND rColorLinked = ?
				');

			for ($i = 1; $i <= count($color->colorLink); $i++) {
				$qr2->execute(array($color->colorLink[$i], $color->id, $i));
			}
		}

	}

	public function add($newColor) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO color
		SET
			id = ?,
			alive = ?,
			isWinner = ?,
			credits = ?,
			players = ?,		
			activePlayers = ?,
			points = ?,
			sectors = ?,
			electionStatement = ?,
			isClosed = ?,
			dLastElection = ?');
		$aw = $qr->execute(array(
				$color->id,
				$color->alive,
				$color->isWinner,
				$color->credits,
				$color->players,
				$color->activePlayers,
				$color->points,
				$color->sectors,
				$color->electionStatement,
				$color->isClosed,
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
