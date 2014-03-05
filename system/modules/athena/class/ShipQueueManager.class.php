<?php

/**
 * Ship Queue Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 10.02.14
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
			$sq = new ShipQueue();

			$sq->id = $aw['id'];
			$sq->rOrbitalBase = $aw['rOrbitalBase'];
			$sq->dockType = $aw['dockType'];
			$sq->shipNumber = $aw['shipNumber'];
			$sq->quantity = $aw['quantity'];
			$sq->dStart = $aw['dStart'];
			$sq->dEnd = $aw['dEnd'];

			$this->_Add($sq);
		}
	}

	public function add(ShipQueue $sq) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			orbitalBaseShipQueue(rOrbitalBase, dockType, shipNumber, quantity, dStart, dEnd)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$sq->rOrbitalBase,
			$sq->dockType,
			$sq->shipNumber,
			$sq->quantity,
			$sq->dStart,
			$sq->dEnd
		));

		$sq->id = $db->lastInsertId();
		$this->_Add($sq);
	}

	public function save() {
		$shipQueues = $this->_Save();
		foreach ($shipQueues AS $k => $sq) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE orbitalBaseShipQueue
				SET	id = ?,
					rOrbitalBase = ?,
					dockType = ?,
					shipNumber = ?,
					quantity = ?,
					dStart = ?,
					dEnd = ?
				WHERE id = ?');
			$qr->execute(array(
				$sq->id,
				$sq->rOrbitalBase,
				$sq->dockType,
				$sq->shipNumber,
				$sq->quantity,
				$sq->dStart,
				$sq->dEnd,
				$sq->id
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