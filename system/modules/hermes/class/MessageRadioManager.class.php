<?php

/**
 * MessageRadioManager
 *
 * @author Gil Clavien
 * @copyright Expansion - le jeu
 *
 * @package Hermes
 * @update 20.05.13
*/

class MessageRadioManager extends Manager {
	protected $managerType = '_MessageRadio';

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
			FROM radio AS r
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
			$mr = new MessageRadio();

			$mr->id = $aw['id'];
			$mr->rPlayer = $aw['rPlayer'];
			$mr->rSystem = $aw['rSystem'];
			$mr->oContent = $aw['oContent'];
			$mr->pContent = $aw['pContent'];
			$mr->dCreation = $aw['dCreation'];
			$mr->statement = $aw['statement'];

			$mr->playerName = $aw['name'];
			$mr->playerColor = $aw['color'];
			$mr->playerAvatar = $aw['avatar'];

			$this->_Add($mr);
		}
	}

	public function add(MessageRadio $mr) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			radio(rPlayer, rSystem, oContent, pContent, dCreation, statement)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$mr->rPlayer,
			$mr->rSystem,
			$mr->oContent,
			$mr->pContent,
			$mr->dCreation,
			$mr->statement
		));

		$mr->id = $db->lastInsertId();

		$this->_Add($mr);
	}

	public function save() {
		$messages = $this->_Save();

		foreach ($messages AS $mr) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE radio
				SET	rPlayer = ?,
					rSystem = ?,
					oContent = ?,
					pContent = ?,
					dCreation = ?,
					statement = ?
				WHERE id = ?');
			$qr->execute(array(
				$mr->rPlayer,
				$mr->rSystem,
				$mr->oContent,
				$mr->pContent,
				$mr->dCreation,
				$mr->statement,
				$mr->id
			));
		}
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM notification WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}