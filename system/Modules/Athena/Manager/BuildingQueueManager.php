<?php

/**
 * Building Queue Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 10.02.14
*/
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Athena\Model\BuildingQueue;

class BuildingQueueManager extends Manager {
	protected $managerType = '_BuildingQueue';
	
	/**
	 * @param Database $database
	 */
	public function __construct(Database $database) {
		parent::__construct($database);
	}

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT *
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
			$bq = new BuildingQueue();

			$bq->id = $aw['id'];
			$bq->rOrbitalBase = $aw['rOrbitalBase'];
			$bq->buildingNumber = $aw['buildingNumber'];
			$bq->targetLevel = $aw['targetLevel'];
			$bq->dStart = $aw['dStart'];
			$bq->dEnd = $aw['dEnd'];

			$this->_Add($bq);
		}
	}

	public function add(BuildingQueue $bq) {
		$qr = $this->database->prepare('INSERT INTO
			orbitalBaseBuildingQueue(rOrbitalBase, buildingNumber, targetLevel, dStart, dEnd)
			VALUES(?, ?, ?, ?, ?)');
		$qr->execute(array(
			$bq->rOrbitalBase,
			$bq->buildingNumber,
			$bq->targetLevel,
			$bq->dStart,
			$bq->dEnd
		));

		$bq->id = $this->database->lastInsertId();
		$this->_Add($bq);
	}

	public function save() {
		$buildingQueues = $this->_Save();
		foreach ($buildingQueues AS $bq) {
			$qr = $this->database->prepare('UPDATE orbitalBaseBuildingQueue
				SET	id = ?,
					rOrbitalBase = ?,
					buildingNumber = ?,
					targetlevel = ?,
					dStart = ?,
					dEnd = ?
				WHERE id = ?');
			$qr->execute(array(
				$bq->id,
				$bq->rOrbitalBase,
				$bq->buildingNumber,
				$bq->targetLevel,
				$bq->dStart,
				$bq->dEnd,
				$bq->id
			));
		}
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM orbitalBaseBuildingQueue WHERE id = ?');
		$qr->execute(array($id));

		// suppression de l'objet en manager
		$this->_Remove($id);

		return TRUE;
	}
}