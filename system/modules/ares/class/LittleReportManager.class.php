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

class LittleReportManager extends Manager {
	protected $managerType ='_LittleReport';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT r.*,
				p1.rColor AS colorA,
				p2.rColor AS colorD
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
			$report->round = $awReport['round'];
			$report->importance = $awReport['importance'];
			$report->statementAttacker = $awReport['statementAttacker'];
			$report->statementDefender = $awReport['statementDefender'];
			$report->dFight = $awReport['dFight'];

			$report->colorA = $awReport['colorA'];
			$report->colorD = $awReport['colorD'];

			$this->_Add($report);
		}
	}

	public function save() {
		$db = DataBase::getInstance();
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
?>