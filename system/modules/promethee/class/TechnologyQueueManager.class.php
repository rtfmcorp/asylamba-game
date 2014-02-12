 <?php
/**
 * Technology Queue Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 10.02.14
*/

class TechnologyQueueManager extends Manager {
	protected $managerType = '_TechnologyQueue';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
			FROM technologyQueue
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
			$t = new TechnologyQueue();

			$t->id = $aw['id'];
			$t->rPlayer = $aw['rPlayer'];
			$t->rPlace = $aw['rPlace'];
			$t->technology = $aw['technology'];
			$t->targetLevel = $aw['targetLevel'];
			$t->dStart = $aw['dStart'];
			$t->dEnd = $aw['dEnd'];

			$this->_Add($t);
		}
	}

	public function add(TechnologyQueue $t) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			technologyQueue(rPlayer, rPlace, technology, targetLevel, dStart, dEnd)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$t->rPlayer,
			$t->rPlace,
			$t->technology,
			$t->targetLevel,
			$t->dStart,
			$t->dEnd
		));
		$t->id = $db->lastInsertId();
		$this->_Add($t);
	}

	public function save() {
		$technologyQueues = $this->_Save();
		foreach ($technologyQueues AS $k => $t) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE technologyQueue
				SET	id = ?,
					rPlayer = ?,
					rPlace = ?,
					technology = ?,
					targetlevel = ?,
					dStart = ?,
					dEnd = ?
				WHERE id = ?');
			$qr->execute(array(
				$t->id,
				$t->rPlayer,
				$t->rPlace,
				$t->technology,
				$t->targetLevel,
				$t->dStart,
				$t->dEnd,
				$t->id
			));
		}
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM technologyQueue WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);

		return TRUE;
	}
}
?>