<?php

/**
 * Notification Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Hermes
 * @update 20.05.13
*/

class NotificationManager extends Manager {
	protected $managerType = '_Notification';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
			FROM notification
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
			$n = new Notification();

			$n->setId($aw['id']);
			$n->setRPlayer($aw['rPlayer']);
			$n->setTitle($aw['title']);
			$n->setContent($aw['content']);
			$n->setDSending($aw['dSending']);
			$n->setReaded($aw['readed']);
			$n->setArchived($aw['archived']);

			$this->_Add($n);
		}
	}

	public function add(Notification $n) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			notification(rPlayer, title, content, dSending, readed, archived)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$n->getRPlayer(),
			$n->getTitle(),
			$n->getContent(),
			$n->getDSending(),
			$n->getReaded(),
			$n->getArchived()
		));

		$n->setId($db->lastInsertId());

		$this->_Add($n);
	}

	public function save() {
		$notifications = $this->_Save();

		foreach ($notifications AS $n) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE notification
				SET	id = ?,
					rPlayer = ?,
					title = ?,
					content = ?,
					dSending = ?,
					readed = ?,
					archived = ?
				WHERE id = ?');
			$qr->execute(array(
				$n->getId(),
				$n->getRPlayer(),
				$n->getTitle(),
				$n->getContent(),
				$n->getDSending(),
				$n->getReaded(),
				$n->getArchived(),
				$n->getId()
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

	public function deleteByRPlayer($rPlayer) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM notification WHERE rPlayer = ? AND archived = 0');
		$qr->execute(array($rPlayer));

		$nbrDeleted = 0;
		for ($i = 0; $i < $this->size(); $i++) { 
			if ($this->get($i)->getRPlayer() == $rPlayer) {
				$nbrDeleted++;
			}

			$this->_Remove($this->get($i)->getId());
		}

		return $nbrDeleted;
	}

	public static function countAll($where = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT 
				COUNT(id) AS nbr
			FROM notification
			' . $formatWhere
		);
		foreach($where AS $v) {
			$valuesArray[] = $v;
		}
		$qr->execute($valuesArray);
		$aw = $qr->fetch();
		return $aw['nbr'];
	}
}