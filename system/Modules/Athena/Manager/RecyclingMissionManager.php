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
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;

use Asylamba\Modules\Athena\Model\RecyclingMission;

class RecyclingMissionManager extends Manager {
	protected $managerType = '_RecyclingMission';

	/**
	 * @param Database $database
	 */
	public function __construct(Database $database) {
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'rm.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT rm.*,
				p.typeOfPlace AS typeOfPlace,
				p.position AS position,
				p.population AS population,
				p.coefResources AS coefResources,
				p.coefHistory AS coefHistory,
				p.resources AS resources,
				p.rSystem AS systemId,
				s.xPosition AS xPosition,
				s.yPosition AS yPosition,
				s.typeOfSystem AS typeOfSystem,
				s.rSector AS sectorId
			FROM recyclingMission AS rm
			LEFT JOIN place AS p
				ON rm.rTarget = p.id
				LEFT JOIN system AS s
					ON p.rSystem = s.id
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
			$rm->addToNextMission = $aw['addToNextMission'];
			$rm->uRecycling = $aw['uRecycling'];
			$rm->statement = $aw['statement'];

			$rm->typeOfPlace = $aw['typeOfPlace'];
			$rm->position = $aw['position'];
			$rm->population = $aw['population'];
			$rm->coefResources = $aw['coefResources'];
			$rm->coefHistory = $aw['coefHistory'];
			$rm->resources = $aw['resources'];
			$rm->systemId = $aw['systemId'];
			$rm->xSystem = $aw['xPosition'];
			$rm->ySystem = $aw['yPosition'];
			$rm->typeOfSystem = $aw['typeOfSystem'];
			$rm->sectorId = $aw['sectorId'];

			$currentRM = $this->_Add($rm);
		}
	}

	public function add(RecyclingMission $rm) {
		$qr = $this->database->prepare('INSERT INTO
			recyclingMission(rBase, rTarget, cycleTime, recyclerQuantity, addToNextMission, uRecycling, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$rm->rBase,
			$rm->rTarget,
			$rm->cycleTime,
			$rm->recyclerQuantity,
			$rm->addToNextMission,
			$rm->uRecycling,
			$rm->statement
		));

		$rm->id = $this->database->lastInsertId();

		$this->_Add($rm);
	}

	public function save() {
		$recyclingMissions = $this->_Save();

		foreach ($recyclingMissions AS $rm) {
			$qr = $this->database->prepare('UPDATE recyclingMission
				SET	id = ?,
					rBase = ?,
					rTarget = ?,
					cycleTime = ?,
					recyclerQuantity = ?,
					addToNextMission = ?,
					uRecycling = ?,
					statement = ?
				WHERE id = ?');
			$qr->execute(array(
				$rm->id,
				$rm->rBase,
				$rm->rTarget,
				$rm->cycleTime,
				$rm->recyclerQuantity,
				$rm->addToNextMission,
				$rm->uRecycling,
				$rm->statement,
				$rm->id
			));
		}
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM recyclingMission WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}
}