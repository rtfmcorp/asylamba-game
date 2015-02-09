<?php

/**
 * RecyclingMissionManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @version 09.02.15
 **/

class RecyclingMissionManager extends Manager {
	protected $managerType = '_RecyclingMission';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'rm.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT rm.*
			FROM recyclingMission AS rm
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

		$this->fill($qr);
	}

	protected function fill($qr) {
		while ($aw = $qr->fetch()) {
			$rm = new RecyclingMission();

			$rm->id = $aw['id'];
			$rm->rBase = $aw['rBase'];
			$rm->rTarget = $aw['rTarget'];
			$rm->cycleTime = $aw['cycleTime'];
			$rm->recyclerQuantity = $aw['recyclerQuantity'];
			$rm->uRecycling = $aw['uRecycling'];
			$rm->statement = $aw['statement'];

			$currentRM = $this->_Add($rm);

			if ($this->currentSession->getUMode()) {
				$currentRM->uMethod();
			}
		}
	}

	public function add(RecyclingMission $rm) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			recyclingMission(rBase, rTarget, cycleTime, recyclerQuantity, uRecycling, statement)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$rm->rBase,
			$rm->rTarget,
			$rm->cycleTime,
			$rm->recyclerQuantity,
			$rm->uRecycling,
			$rm->statement
		));

		$rm->id = $db->lastInsertId();

		$this->_Add($rm);
	}

	public function save() {
		$recyclingMissions = $this->_Save();

		foreach ($recyclingMissions AS $rm) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE recyclingMission
				SET	id = ?,
					rBase = ?,
					rTarget = ?,
					cycleTime = ?,
					recyclerQuantity = ?,
					uRecycling = ?,
					statement = ?
				WHERE id = ?');
			$qr->execute(array(
				$rm->id,
				$rm->rBase,
				$rm->rTarget,
				$rm->cycleTime,
				$rm->recyclerQuantity,
				$rm->uRecycling,
				$rm->statement,
				$rm->id
			));
		}
	}

	public static function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM recyclingMission WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}
}
?>