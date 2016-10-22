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

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;

use Asylamba\Classes\Database\Database;

use Asylamba\Modules\Ares\Model\Report;

class ReportManager extends Manager {
	protected $managerType ='_Report';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT r.*,
				sq.id AS sqId,
				sq.position AS sqPosition,
				sq.rReport AS sqRReport,
				sq.round AS sqRound,
				sq.rCommander AS sqRCommander,
				sq.ship0 AS sqShip0,
				sq.ship1 AS sqShip1,
				sq.ship2 AS sqShip2,
				sq.ship3 AS sqShip3,
				sq.ship4 AS sqShip4,
				sq.ship5 AS sqShip5,
				sq.ship6 AS sqShip6,
				sq.ship7 AS sqShip7,
				sq.ship8 AS sqShip8,
				sq.ship9 AS sqShip9,
				sq.ship10 AS sqShip10,
				sq.ship11 AS sqShip11
			FROM report AS r
			LEFT JOIN squadronReport AS sq
				ON sq.rReport = r.id
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

		$awReports = $qr->fetchAll();
		$qr->closeCursor();

		if (count($awReports) > 0) {
			for ($i = 0; $i < count($awReports); $i++) {
				if ($i == 0 || $awReports[$i]['id'] != $awReports[$i - 1]['id']) {
					$report = new Report();

					$report->id = $awReports[$i]['id'];
					$report->rPlayerAttacker = $awReports[$i]['rPlayerAttacker'];
					$report->rPlayerDefender = $awReports[$i]['rPlayerDefender'];
					$report->rPlayerWinner = $awReports[$i]['rPlayerWinner'];
					$report->avatarA = $awReports[$i]['avatarA'];
					$report->avatarD = $awReports[$i]['avatarD'];
					$report->nameA = $awReports[$i]['nameA'];
					$report->nameD = $awReports[$i]['nameD'];
					$report->levelA = $awReports[$i]['levelA'];
					$report->levelD = $awReports[$i]['levelD'];
					$report->experienceA = $awReports[$i]['experienceA'];
					$report->experienceD = $awReports[$i]['experienceD'];
					$report->palmaresA = $awReports[$i]['palmaresA'];
					$report->palmaresD = $awReports[$i]['palmaresD'];
					$report->resources = $awReports[$i]['resources'];
					$report->expCom = $awReports[$i]['expCom'];
					$report->expPlayerA = $awReports[$i]['expPlayerA'];
					$report->expPlayerD = $awReports[$i]['expPlayerD'];
					$report->rPlace = $awReports[$i]['rPlace'];
					$report->placeName = $awReports[$i]['placeName'];
					$report->type = $awReports[$i]['type'];
					$report->isLegal = $awReports[$i]['isLegal'];
					$report->hasBeenPunished = $awReports[$i]['hasBeenPunished'];
					$report->round = $awReports[$i]['round'];
					$report->importance = $awReports[$i]['importance'];
					$report->pevInBeginA = $awReports[$i]['pevInBeginA'];
					$report->pevInBeginD = $awReports[$i]['pevInBeginD'];
					$report->pevAtEndA = $awReports[$i]['pevAtEndA'];
					$report->pevAtEndD = $awReports[$i]['pevAtEndD'];
					$report->statementAttacker = $awReports[$i]['statementAttacker'];
					$report->statementDefender = $awReports[$i]['statementDefender'];
					$report->dFight = $awReports[$i]['dFight'];

				}

				$report->squadrons[] = array(
					$awReports[$i]['sqId'], 
					$awReports[$i]['sqPosition'], 
					$awReports[$i]['sqRReport'], 
					$awReports[$i]['sqRound'],
					$awReports[$i]['sqRCommander'],
					$awReports[$i]['sqShip0'],
					$awReports[$i]['sqShip1'], 
					$awReports[$i]['sqShip2'], 
					$awReports[$i]['sqShip3'], 
					$awReports[$i]['sqShip4'], 
					$awReports[$i]['sqShip5'], 
					$awReports[$i]['sqShip6'], 
					$awReports[$i]['sqShip7'], 
					$awReports[$i]['sqShip8'], 
					$awReports[$i]['sqShip9'], 
					$awReports[$i]['sqShip10'], 
					$awReports[$i]['sqShip11']);
					
				if ($i == count($awReports) - 1 || $awReports[$i]['id'] != $awReports[$i + 1]['id']) {
					$report->setArmies();
					$this->_Add($report);
				}
			}
		}
	}

	public function loadOnlyReport($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT r.*
			FROM report AS r
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

		$awReports = $qr->fetchAll();
		$qr->closeCursor();

		foreach ($awReports AS $awReport) {
			$report = new Report();

			$report->id = $awReport['id'];
			$report->rPlayerAttacker = $awReport['rPlayerAttacker'];
			$report->rPlayerDefender = $awReport['rPlayerDefender'];
			$report->rPlayerWinner = $awReport['rPlayerWinner'];
			$report->avatarA = $awReport['avatarA'];
			$report->avatarD = $awReport['avatarD'];
			$report->nameA = $awReport['nameA'];
			$report->nameD = $awReport['nameD'];
			$report->levelA = $awReport['levelA'];
			$report->levelD = $awReport['levelD'];
			$report->experienceA = $awReport['experienceA'];
			$report->experienceD = $awReport['experienceD'];
			$report->palmaresA = $awReport['palmaresA'];
			$report->palmaresD = $awReport['palmaresD'];
			$report->resources = $awReport['resources'];
			$report->expCom = $awReport['expCom'];
			$report->expPlayerA = $awReport['expPlayerA'];
			$report->expPlayerD = $awReport['expPlayerD'];
			$report->rPlace = $awReport['rPlace'];
			$report->placeName = $awReport['placeName'];
			$report->type = $awReport['type'];
			$report->isLegal = $awReport['isLegal'];
			$report->hasBeenPunished = $awReport['hasBeenPunished'];
			$report->round = $awReport['round'];
			$report->importance = $awReport['importance'];
			$report->pevInBeginA = $awReports[$i]['pevInBeginA'];
			$report->pevInBeginD = $awReports[$i]['pevInBeginD'];
			$report->pevAtEndA = $awReports[$i]['pevAtEndA'];
			$report->pevAtEndD = $awReports[$i]['pevAtEndD'];
			$report->statementAttacker = $awReport['statementAttacker'];
			$report->statementDefender = $awReport['statementDefender'];
			$report->dFight = $awReport['dFight'];

			$this->_Add($report);
		}
	}

	public function save() {
		$db = Database::getInstance();
		$reports = $this->_Save();

		foreach ($reports as $report) {
			$qr = $db->prepare('UPDATE report SET
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
				placeName = ?,
				type = ?,
				isLegal = ?,
				hasBeenPunished = ?,
				round = ?,
				importance = ?,
				pevInBeginA = ?,
				pevInBeginD = ?,
				pevAtEndA = ?,
				pevAtEndD = ?,
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
				$report->placeName,
				$report->type,
				$report->isLegal,
				$report->hasBeenPunished,
				$report->round,
				$report->importance,
				$report->pevInBeginA,
				$report->pevInBeginD,
				$report->pevAtEndA,
				$report->pevAtEndD,
				$report->statementAttacker,
				$report->statementDefender,
				$report->dFight,
				$report->id
				)
			);
		}
	}

	public function emptySession() {
		# empty the session, for player rankings
		$this->_EmptyCurrentSession();
		$this->newSession(FALSE);
	}

	public function add($newReport) {
		$db = Database::getInstance();

		$qr = $db->prepare('INSERT INTO report SET
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
			placeName = ?,
			type = ?,
			isLegal = ?,
			hasBeenPunished = ?,
			round = ?,
			importance = ?,
			pevInBeginA = ?,
			pevInBeginD = ?,
			pevAtEndA = ?,
			pevAtEndD = ?,
			statementAttacker = ?,
			statementDefender = ?,
			dFight = ?');
		$aw = $qr->execute(array(
			$newReport->rPlayerAttacker,
			$newReport->rPlayerDefender,
			$newReport->rPlayerWinner,
			$newReport->avatarA,
			$newReport->avatarD,
			$newReport->nameA,
			$newReport->nameD,
			$newReport->levelA,
			$newReport->levelD,
			$newReport->experienceA,
			$newReport->experienceD,
			$newReport->palmaresA,
			$newReport->palmaresD,
			$newReport->resources,
			$newReport->expCom,
			$newReport->expPlayerA,
			$newReport->expPlayerD,
			$newReport->rPlace,
			$newReport->placeName,
			$newReport->type,
			$newReport->isLegal,
			$newReport->hasBeenPunished,
			$newReport->round,
			$newReport->importance,
			$newReport->pevInBeginA,
			$newReport->pevInBeginD,
			$newReport->pevAtEndA,
			$newReport->pevAtEndD,
			$newReport->statementAttacker,
			$newReport->statementDefender,
			$newReport->dFight
			));

		$newReport->id = $db->lastInsertId();

		if (count($newReport->squadrons) > 0) {

			for ($i = 0; $i < count($newReport->squadrons); $i++) {
				$newReport->squadrons[$i][2] = $newReport->id;
			}
			$qr = 'INSERT INTO squadronReport (position, rReport, round, rCommander, ship0, ship1, ship2, ship3, ship4, ship5, ship6, ship7, ship8, ship9, ship10, ship11) 
			VALUES';
			for ($j = 0; $j < count($newReport->squadrons); $j++) {
				$qr .= ' (' . $newReport->squadrons[$j][1];
					for ($i = 2; $i < 17; $i++) {
						$qr .= ', ' . $newReport->squadrons[$j][$i];
					}
				$qr .= ($j == count($newReport->squadrons) - 1) ? ');' : '),';
			}

			$qr = $db->prepare($qr);
			$aw = $qr->execute();
		}
		$this->_Add($newReport);
		return $newReport->id;
	}
}