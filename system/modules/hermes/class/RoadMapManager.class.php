<?php

/**
 * RoadMapManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Hermes
 * @update 21.12.13
*/

class RoadMapManager extends Manager {
	protected $managerType = '_RoadMap';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'r.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT
				r.*,
				p.name AS name,
				p.rColor AS color,
				p.avatar AS avatar
			FROM roadMap AS r
			LEFT JOIN player AS p
				ON p.id = r.rPlayer
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
			$rm = new RoadMap();

			$rm->id = $aw['id'];
			$rm->rPlayer = $aw['rPlayer'];
			$rm->oContent = $aw['oContent'];
			$rm->pContent = $aw['pContent'];
			$rm->statement = $aw['statement'];
			$rm->dCreation = $aw['dCreation'];

			$rm->playerName = $aw['name'];
			$rm->playerColor = $aw['color'];
			$rm->playerAvatar = $aw['avatar'];

			$this->_Add($rm);
		}
	}

	public function add(RoadMap $rm) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			roadMap(rPlayer, oContent, pContent, statement, dCreation)
			VALUES(?, ?, ?, ?, ?)');
		$qr->execute(array(
			$rm->rPlayer,
			$rm->oContent,
			$rm->pContent,
			$rm->statement,
			$rm->dCreation
		));

		$rm->id = $db->lastInsertId();

		$this->_Add($rm);
	}

	public function save() {
		$roadmap = $this->_Save();

		foreach ($roadmap AS $rm) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE roadMap
				SET	rPlayer = ?,
					oContent = ?,
					pContent = ?,
					statement = ?,
					dCreation = ?
				WHERE id = ?');
			$qr->execute(array(
				$rm->rPlayer,
				$rm->oContent,
				$rm->pContent,
				$rm->statement,
				$rm->dCreation,
				$rm->id
			));
		}
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM roadMap WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}