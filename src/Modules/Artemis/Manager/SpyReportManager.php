<?php

/**
 * SpyReportManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Artemis
 * @version 26.03.14
 **/

namespace App\Modules\Artemis\Manager;

use App\Classes\Library\Utils;
use App\Classes\Worker\Manager;
use App\Classes\Database\Database;

use App\Modules\Artemis\Model\SpyReport;

class SpyReportManager extends Manager
{
	protected $managerType = '_SpyReport';

	public function __construct(Database $database)
	{
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'sr.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT sr.*,
			p.typeOfPlace AS typeOfPlace,
			p.position AS position,
			p.population AS population,
			p.coefResources AS coefResources,
			p.coefHistory AS coefHistory,
			s.rSector AS rSector,
			s.xPosition AS xPosition,
			s.yPosition AS yPosition,
			s.typeOfSystem AS typeOfSystem
			FROM spyReport AS sr
			LEFT JOIN place AS p 
				ON sr.rPlace = p.id
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

		while($aw = $qr->fetch()) {
			$sr = new SpyReport();

			$sr->id = $aw['id'];
			$sr->rPlayer = $aw['rPlayer'];
			$sr->price = $aw['price'];
			$sr->rPlace = $aw['rPlace'];
			$sr->placeColor = $aw['placeColor'];
			$sr->typeOfBase = $aw['typeOfBase'];
			$sr->typeOfOrbitalBase = $aw['typeOfOrbitalBase'];
			$sr->placeName = $aw['placeName'];
			$sr->points = $aw['points'];
			$sr->rEnemy = $aw['rEnemy'];
			$sr->enemyName = $aw['enemyName'];
			$sr->enemyAvatar = $aw['enemyAvatar'];
			$sr->enemyLevel = $aw['enemyLevel'];
			$sr->resources = $aw['resources'];
			$sr->shipsInStorage = $aw['shipsInStorage'];
			$sr->antiSpyInvest = $aw['antiSpyInvest'];
			$sr->commercialRouteIncome = $aw['commercialRouteIncome'];
			$sr->commanders = $aw['commanders'];
			$sr->success = $aw['success'];
			$sr->type = $aw['type'];
			$sr->dSpying = $aw['dSpying'];

			$sr->typeOfPlace = $aw['typeOfPlace'];
			$sr->position = $aw['position'];
			$sr->population = $aw['population'];
			$sr->coefResources = $aw['coefResources'];
			$sr->coefHistory = $aw['coefHistory'];
			$sr->rSector = $aw['rSector'];
			$sr->xPosition = $aw['xPosition'];
			$sr->yPosition = $aw['yPosition'];
			$sr->typeOfSystem = $aw['typeOfSystem'];

			$currentT = $this->_Add($sr);
		}
	}

	public function add(SpyReport $sr) {
		$qr = $this->database->prepare('INSERT INTO
			spyReport(rPlayer, price, rPlace, placeColor, typeOfBase, typeOfOrbitalBase, placeName, points, rEnemy, enemyName, enemyAvatar, enemyLevel, resources, shipsInStorage, antiSpyInvest, commercialRouteIncome, commanders, success, type, dSpying)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$sr->rPlayer,
			$sr->price,
			$sr->rPlace,
			$sr->placeColor,
			$sr->typeOfBase,
			$sr->typeOfOrbitalBase,
			$sr->placeName,
			$sr->points,
			$sr->rEnemy,
			$sr->enemyName,
			$sr->enemyAvatar,
			$sr->enemyLevel,
			$sr->resources,
			$sr->shipsInStorage,
			$sr->antiSpyInvest,
			$sr->commercialRouteIncome,
			$sr->commanders,
			$sr->success,
			$sr->type,
			$sr->dSpying
		));

		$sr->id = $this->database->lastInsertId();

		$this->_Add($sr);
	}

	public function save() {
		$reports = $this->_Save();

		foreach ($reports AS $sr) {
			$qr = $this->database->prepare('UPDATE spyReport
				SET	id = ?,
					rPlayer = ?,
					price = ?,
					rPlace = ?,
					placeColor = ?,
					typeOfBase = ?,
					typeOfOrbitalBase = ?,
					placeName = ?,
					points = ?,
					rEnemy = ?,
					enemyName = ?,
					enemyAvatar = ?,
					enemyLevel = ?,
					resources = ?,
					shipsInStorage = ?,
					antiSpyInvest = ?,
					commercialRouteIncome = ?,
					commanders = ?,
					success = ?,
					type = ?,
					dSpying = ?
				WHERE id = ?');
			$qr->execute(array(
				$sr->id,
				$sr->rPlayer,
				$sr->price,
				$sr->rPlace,
				$sr->placeColor,
				$sr->typeOfBase,
				$sr->typeOfOrbitalBase,
				$sr->placeName,
				$sr->points,
				$sr->rEnemy,
				$sr->enemyName,
				$sr->enemyAvatar,
				$sr->enemyLevel,
				$sr->resources,
				$sr->shipsInStorage,
				$sr->antiSpyInvest,
				$sr->commercialRouteIncome,
				$sr->commanders,
				$sr->success,
				$sr->type,
				$sr->dSpying,
				$sr->id
			));
		}
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM spyReport WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	public function deleteByRPlayer($rPlayer) {
		$qr = $this->database->prepare('DELETE FROM spyReport WHERE rPlayer = ?');
		$qr->execute(array($rPlayer));

		$nbrDeleted = 0;
		for ($i = 0; $i < $this->size(); $i++) { 
			if ($this->get($i)->rPlayer == $rPlayer) {
				$nbrDeleted++;
			}

			$this->_Remove($this->get($i)->getId());
		}

		return $nbrDeleted;
	}
}
