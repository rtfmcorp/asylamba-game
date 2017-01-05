<?php

/**
 * Report Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 12.07.13
*/

namespace Asylamba\Modules\Ares\Manager;

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Ares\Model\Report;

class LittleReportManager extends Manager {
	protected $managerType ='_LittleReport';

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

		$qr = $this->database->prepare('SELECT r.*,
				p1.rColor AS colorA,
				p2.rColor AS colorD,
				p1.name AS playerNameA,
				p2.name AS playerNameD
			FROM report AS r
			LEFT JOIN player AS p1
				ON p1.id = r.rPlayerAttacker
			LEFT JOIN player AS p2
				ON p2.id = r.rPlayerDefender
			' . $formatWhere .'
			' . $formatOrder .'
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
		
		if (empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		$this->fill($qr);
		$qr->closeCursor();
	}

	public function loadByRequest($request, $params) {
		$qr = $this->database->prepare('SELECT r.*,
				p1.rColor AS colorA,
				p2.rColor AS colorD,
				p1.name AS playerNameA,
				p2.name AS playerNameD
			FROM report AS r
			LEFT JOIN player AS p1
				ON p1.id = r.rPlayerAttacker
			LEFT JOIN player AS p2
				ON p2.id = r.rPlayerDefender
			' . $request
		);

		if (empty($params)) {
			$qr->execute();
		} else {
			$qr->execute($params);
		}

		$this->fill($qr);
		$qr->closeCursor();
	}

	protected function fill($qr) {
		while ($aw = $qr->fetch()) {
			$report = new Report();

			$report->id = $aw['id'];
			$report->rPlayerAttacker = $aw['rPlayerAttacker'];
			$report->rPlayerDefender = $aw['rPlayerDefender'];
			$report->rPlayerWinner = $aw['rPlayerWinner'];
			$report->avatarA = $aw['avatarA'];
			$report->avatarD = $aw['avatarD'];
			$report->nameA = $aw['nameA'];
			$report->nameD = $aw['nameD'];
			$report->levelA = $aw['levelA'];
			$report->levelD = $aw['levelD'];
			$report->experienceA = $aw['experienceA'];
			$report->experienceD = $aw['experienceD'];
			$report->palmaresA = $aw['palmaresA'];
			$report->palmaresD = $aw['palmaresD'];
			$report->resources = $aw['resources'];
			$report->expCom = $aw['expCom'];
			$report->expPlayerA = $aw['expPlayerA'];
			$report->expPlayerD = $aw['expPlayerD'];
			$report->rPlace = $aw['rPlace'];
			$report->isLegal = $aw['isLegal'];
			$report->placeName = $aw['placeName'];
			$report->type = $aw['type'];
			$report->round = $aw['round'];
			$report->importance = $aw['importance'];
			$report->statementAttacker = $aw['statementAttacker'];
			$report->statementDefender = $aw['statementDefender'];
			$report->dFight = $aw['dFight'];

			$report->colorA = $aw['colorA'];
			$report->colorD = $aw['colorD'];
			$report->playerNameA = $aw['playerNameA'];
			$report->playerNameD = $aw['playerNameD'];

			$this->_Add($report);
		}
	}

	public function save() {
		$reports = $this->_Save();

		foreach ($reports as $report) {
			$qr = $this->database->prepare('UPDATE report SET
				rPlayerAttacker = ?,
				rPlayerDefender = ?,
				rPlayerWinner = ?,
				avatarA = ?,
				avatarD = ?,
				nameA = ?,
				nameD = ?,
				levelA = ?,
				levelD = ?,
				experienceA = ?,
				experienceD = ?,
				palmaresA = ?,
				palmaresD = ?,
				resources = ?,
				expCom = ?,
				expPlayerA = ?,
				expPlayerD = ?,
				rPlace = ?,
				isLegal = ?,
				placeName = ?,
				type = ?,
				round = ?,
				importance = ?,
				statementAttacker = ?,
				statementDefender = ?,
				dFight = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
				$report->rPlayerAttacker,
				$report->rPlayerDefender,
				$report->rPlayerWinner,
				$report->avatarA,
				$report->avatarD,
				$report->nameA,
				$report->nameD,
				$report->levelA,
				$report->levelD,
				$report->experienceA,
				$report->experienceD,
				$report->palmaresA,
				$report->palmaresD,
				$report->resources,
				$report->expCom,
				$report->expPlayerA,
				$report->expPlayerD,
				$report->rPlace,
				$report->isLegal,
				$report->placeName,
				$report->type,
				$report->round,
				$report->importance,
				$report->statementAttacker,
				$report->statementDefender,
				$report->dFight,
				$report->id
				)
			);
		}
	}
}