<?php

/**
 * Ship Queue Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 20.05.13
*/

class ShipQueueManager extends Manager {
	protected $managerType = '_ShipQueue';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
			FROM orbitalBaseShipQueue
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
			$n = new ShipQueue();

			$n->setId($aw['id']);
			$n->setROrbitalBase($aw['rOrbitalBase']);
			$n->setDockType($aw['dockType']);
			$n->setShipNumber($aw['shipNumber']);
			$n->setQuantity($aw['quantity']);
			$n->setRemainingTime($aw['remainingTime']);
			$n->setPosition($aw['position']);

			$this->_Add($n);
		}
	}

	public function add(ShipQueue $n) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			orbitalBaseShipQueue(rOrbitalBase, dockType, shipNumber, quantity, remainingTime, position)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$n->getROrbitalBase(),
			$n->getDockType(),
			$n->getShipNumber(),
			$n->getQuantity(),
			$n->getRemainingTime(),
			$n->getPosition()
		));

		$n->setId($db->lastInsertId());
		$this->_Add($n);
	}

	public function save() {
		$shipQueues = $this->_Save();
		foreach ($shipQueues AS $k => $n) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE orbitalBaseShipQueue
				SET	id = ?,
					rOrbitalBase = ?,
					dockType = ?,
					shipNumber = ?,
					quantity = ?,
					remainingTime = ?,
					position = ?
				WHERE id = ?');
			$qr->execute(array(
				$n->getId(),
				$n->getROrbitalBase(),
				$n->getDockType(),
				$n->getShipNumber(),
				$n->getQuantity(),
				$n->getRemainingTime(),
				$n->getPosition(),
				$n->getId()
			));
		}
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM orbitalBaseShipQueue WHERE id = ?');
		$qr->execute(array($id));

		// suppression de l'objet en manager
		$this->_Remove($id);

		return TRUE;
	}
}
?>