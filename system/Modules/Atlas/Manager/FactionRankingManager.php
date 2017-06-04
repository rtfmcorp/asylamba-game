<?php

/**
 * FactionRankingManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Atlas
 * @version 04.06.14
 **/
namespace Asylamba\Modules\Atlas\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Atlas\Model\FactionRanking;

class FactionRankingManager extends Manager {
	protected $managerType = '_FactionRanking';

	/**
	 * @param Database $database
	 */
	public function __construct(Database $database) {
		parent::__construct($database);
	}
	
	public function loadLastContext($where = array(), $order = array(), $limit = array()) {	
		$qr = $this->database->prepare('SELECT * FROM ranking WHERE faction = 1 ORDER BY dRanking DESC LIMIT 1');
		$qr->execute();
		$aw = $qr->fetch();
		$rRanking = $aw['id'];

		# add the rRanking to the WHERE clause
		$where['rRanking'] = $rRanking;

		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT *
			FROM factionRanking AS fr
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
			$fr = new FactionRanking();

			$fr->id = $aw['id']; 
			$fr->rRanking = $aw['rRanking'];
			$fr->rFaction = $aw['rFaction']; 
			$fr->points = $aw['points'];
			$fr->pointsPosition = $aw['pointsPosition'];
			$fr->pointsVariation = $aw['pointsVariation'];
			$fr->newPoints = $aw['newPoints'];
			$fr->general = $aw['general'];
			$fr->generalPosition = $aw['generalPosition'];
			$fr->generalVariation = $aw['generalVariation'];
			$fr->wealth = $aw['wealth'];
			$fr->wealthPosition = $aw['wealthPosition'];
			$fr->wealthVariation = $aw['wealthVariation'];
			$fr->territorial = $aw['territorial'];
			$fr->territorialPosition = $aw['territorialPosition'];
			$fr->territorialVariation = $aw['territorialVariation'];

			$currentT = $this->_Add($fr);
		}
	}

	public function loadByRequest($request, $args = array()) {
		$qr = $this->database->prepare('SELECT *
			FROM factionRanking AS fr
			' . $request
		);

		$qr->execute($args);

		while ($aw = $qr->fetch()) {
			$fr = new FactionRanking();

			$fr->id = isset($aw['id']) ? $aw['id'] : NULL;
			$fr->rRanking = isset($aw['rRanking']) ? $aw['rRanking'] : NULL;
			$fr->rFaction = isset($aw['rFaction']) ? $aw['rFaction'] : NULL;
			$fr->points = isset($aw['points']) ? $aw['points'] : NULL;
			$fr->pointsPosition = isset($aw['pointsPosition']) ? $aw['pointsPosition'] : NULL;
			$fr->pointsVariation = isset($aw['pointsVariation']) ? $aw['pointsVariation'] : NULL;
			$fr->newPoints = isset($aw['newPoints']) ? $aw['newPoints'] : NULL;
			$fr->general = isset($aw['general']) ? $aw['general'] : NULL;
			$fr->generalPosition = isset($aw['generalPosition']) ? $aw['generalPosition'] : NULL;
			$fr->generalVariation = isset($aw['generalVariation']) ? $aw['generalVariation'] : NULL;
			$fr->wealth = isset($aw['wealth']) ? $aw['wealth'] : NULL;
			$fr->wealthPosition = isset($aw['wealthPosition']) ? $aw['wealthPosition'] : NULL;
			$fr->wealthVariation = isset($aw['wealthVariation']) ? $aw['wealthVariation'] : NULL;
			$fr->territorial = isset($aw['territorial']) ? $aw['territorial'] : NULL;
			$fr->territorialPosition = isset($aw['territorialPosition']) ? $aw['territorialPosition'] : NULL;
			$fr->territorialVariation = isset($aw['territorialVariation']) ? $aw['territorialVariation'] : NULL;

			$currentT = $this->_Add($fr);
		}
	}

	public function add(FactionRanking $fr) {
		$qr = $this->database->prepare('INSERT INTO
			factionRanking(rRanking, rFaction, points, pointsPosition, pointsVariation, newPoints, general, generalPosition, generalVariation, 
				wealth, wealthPosition, wealthVariation, territorial, territorialPosition, territorialVariation)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$fr->rRanking,
			$fr->rFaction, 
			$fr->points,
			$fr->pointsPosition,
			$fr->pointsVariation,
			$fr->newPoints,
			$fr->general,
			$fr->generalPosition,
			$fr->generalVariation,
			$fr->wealth,
			$fr->wealthPosition,
			$fr->wealthVariation,
			$fr->territorial,
			$fr->territorialPosition,
			$fr->territorialVariation
		));

		$fr->id = $this->database->lastInsertId();

		$this->_Add($fr);
	}

	public function save() {
		$rankings = $this->_Save();

		foreach ($rankings AS $fr) {
			$qr = $this->database->prepare('UPDATE factionRanking
				SET	id = ?,
					rRanking = ?,
					rFaction = ?,
					points = ?,
					pointsPosition = ?,
					pointsVariation = ?, 
					newPoints = ?,
					general = ?,
					generalPosition = ?,
					generalVariation = ?,
					wealth = ?,
					wealthPosition = ?,
					wealthVariation = ?,
					territorial = ?,
					territorialPosition = ?,
					territorialVariation = ?
				WHERE id = ?');
			$qr->execute(array(
				$fr->id,
				$fr->rRanking,
				$fr->rFaction, 
				$fr->points,
				$fr->pointsPosition,
				$fr->pointsVariation,
				$fr->newPoints,
				$fr->general,
				$fr->generalPosition,
				$fr->generalVariation,
				$fr->wealth,
				$fr->wealthPosition,
				$fr->wealthVariation,
				$fr->territorial,
				$fr->territorialPosition,
				$fr->territorialVariation,
				$fr->id
			));
		}
	}
}