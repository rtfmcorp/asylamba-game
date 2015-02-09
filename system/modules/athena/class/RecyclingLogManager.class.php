<?php

/**
 * RecyclingLogManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @version 09.02.15
 **/

class RecyclingLogManager extends Manager {
	protected $managerType = '_RecyclingLog';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'rl.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT rl.*
			FROM recyclingLog AS rl
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
			$rl = new RecyclingLog();

			$rl->id = $aw['id'];
			$rl->rRecycling = $aw['rRecycling'];
			$rl->resources = $aw['resources'];
			$rl->ship0 = $aw['ship0'];
			$rl->ship1 = $aw['ship1'];
			$rl->ship2 = $aw['ship2'];
			$rl->ship3 = $aw['ship3'];
			$rl->ship4 = $aw['ship4'];
			$rl->ship5 = $aw['ship5'];
			$rl->ship6 = $aw['ship6'];
			$rl->ship7 = $aw['ship7'];
			$rl->ship8 = $aw['ship8'];
			$rl->ship9 = $aw['ship9'];
			$rl->ship10 = $aw['ship10'];
			$rl->ship11 = $aw['ship11'];
			$rl->dLog = $aw['dLog'];

			$this->_Add($rl);
		}
	}

	public function add(RecyclingLog $rl) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			recyclingLog(rRecycling, resources, ship0, ship1, ship2, ship3, ship4, ship5, ship6, ship7,
				ship8, ship9, ship10, ship11, dLog)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$rl->rRecycling,
			$rl->resources,
			$rl->ship0,
			$rl->ship1,
			$rl->ship2,
			$rl->ship3,
			$rl->ship4,
			$rl->ship5,
			$rl->ship6,
			$rl->ship7,
			$rl->ship8,
			$rl->ship9,
			$rl->ship10,
			$rl->ship11,
			$rl->dLog
		));

		$rl->id = $db->lastInsertId();

		$this->_Add($rl);
	}

	public function save() {
		$recyclingLogs = $this->_Save();

		foreach ($recyclingLogs AS $rl) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE recyclingLog
				SET	id = ?,
					rRecycling = ?,
					resources = ?,
					ship0 = ?,
					ship1 = ?,
					ship2 = ?,
					ship3 = ?,
					ship4 = ?,
					ship5 = ?,
					ship6 = ?,
					ship7 = ?,
					ship8 = ?,
					ship9 = ?,
					ship10 = ?,
					ship11 = ?,
					dLog = ?
				WHERE id = ?');
			$qr->execute(array(
				$rl->id,
				$rl->rRecycling,
				$rl->resources,
				$rl->ship0,
				$rl->ship1,
				$rl->ship2,
				$rl->ship3,
				$rl->ship4,
				$rl->ship5,
				$rl->ship6,
				$rl->ship7,
				$rl->ship8,
				$rl->ship9,
				$rl->ship10,
				$rl->ship11,
				$rl->dLog,
				$rl->id
			));
		}
	}

	public static function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM recyclingLog WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}
}
?>