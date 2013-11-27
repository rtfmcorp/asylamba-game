<?php

/**
 * Building Queue Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 20.05.13
*/

class BuildingQueueManager extends Manager {
	protected $managerType = '_BuildingQueue';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
			FROM orbitalBaseBuildingQueue
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
			$n = new BuildingQueue();

			$n->setId($aw['id']);
			$n->setROrbitalBase($aw['rOrbitalBase']);
			$n->setBuildingNumber($aw['buildingNumber']);
			$n->setTargetLevel($aw['targetLevel']);
			$n->setRemainingTime($aw['remainingTime']);
			$n->setPosition($aw['position']);

			$this->_Add($n);
	}
	}

	public function add(BuildingQueue $n) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			orbitalBaseBuildingQueue(rOrbitalBase, buildingNumber, targetLevel, remainingTime, position)
			VALUES(?, ?, ?, ?, ?)');
		$qr->execute(array(
			$n->getROrbitalBase(),
			$n->getBuildingNumber(),
			$n->getTargetLevel(),
			$n->getRemainingTime(),
			$n->getPosition()
		));

		$n->setId($db->lastInsertId());
		$this->_Add($n);
	}

	public function save() {
		$buildingQueues = $this->_Save();
		foreach ($buildingQueues AS $n) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE orbitalBaseBuildingQueue
				SET	id = ?,
					rOrbitalBase = ?,
					buildingNumber = ?,
					targetlevel = ?,
					remainingTime = ?,
					position = ?
				WHERE id = ?');
			$qr->execute(array(
				$n->getId(),
				$n->getROrbitalBase(),
				$n->getBuildingNumber(),
				$n->getTargetLevel(),
				$n->getRemainingTime(),
				$n->getPosition(),
				$n->getId()
			));
		}
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM orbitalBaseBuildingQueue WHERE id = ?');
		$qr->execute(array($id));

		// suppression de l'objet en manager
		$this->_Remove($id);

		return TRUE;
	}

	/**
	 *	ToDo
	 *
	 *	public function invertPosition($id1, $id2) {}
	 */
}
?>