<?php

/**
 * BugTrackerManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Apollon
 * @update 15.07.13
*/

class BugTrackerManager extends Manager {
	protected $managerType = '_BugTracker';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
			FROM bugTracker
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

		if (empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		while($aw = $qr->fetch()) {
			$bt = new BugTracker();

			$bt->id = $aw['id'];
			$bt->url = $aw['url'];
			$bt->rPlayer = $aw['rPlayer'];
			$bt->bindKey = $aw['bindKey'];
			$bt->type = $aw['type'];
			$bt->dSending = $aw['dSending'];
			$bt->message = $aw['message'];
			$bt->statement = $aw['statement'];
			
			$this->_Add($bt);
		}
	}

	public function add(BugTracker $bt) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			bugTracker(url, rPlayer, bindKey, type, dSending, message, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$bt->url,
			$bt->rPlayer,
			$bt->bindKey,
			$bt->type,
			$bt->dSending,
			$bt->message,
			$bt->statement
		));
		$bt->id = $db->lastInsertId();
		$this->_Add($bt);
	}

	public function save() {
		$bugs = $this->_Save();

		foreach ($bugs AS $k => $bt) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE bugTracker
				SET	id = ?,
					url = ?,
					rPlayer = ?,
					bindKey = ?,
					type = ?,
					dSending = ?,
					message = ?,
					statement = ?
				WHERE id = ?');
			$qr->execute(array(
				$bt->id,
				$bt->url,
				$bt->rPlayer,
				$bt->bindKey,
				$bt->type,
				$bt->dSending,
				$bt->message,
				$bt->statement,
				$bt->id
			));
		}
	}
}
?>