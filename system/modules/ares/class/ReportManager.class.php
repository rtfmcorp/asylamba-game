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

class ReportManager extends Manager {
	protected $managerType ='_Report';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'r.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT r.* FROM report r
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

		$aw = $qr->fetchAll();
		$qr->closeCursor();

		foreach($aw AS $awReport) {
			$report = new Report();

			$report->id = $awReport['id'];
			$report->resources = $awReport['resources'];
			$report->expCom = $awReport['expCom'];
			$report->expPlayerA = $awReport['expPlayerA'];
			$report->expPlayerD = $awReport['expPlayerD'];
			$report->rPlayerWinner = $awReport['rPlayerWinner'];
			$report->round = $awReport['round'];
			$report->rPlayerAttacker = $awReport['rPlayerAttacker'];
			$report->rPlayerDefender = $awReport['rPlayerDefender'];
			$report->rBigReport = $awReport['rBigReport'];
			$report->rPlace = $awReport['rPlace']; 			
			$report->type = $awReport['type'];
			$report->importance = $awReport['importance'];
			$report->dFight = $awReport['dFight'];
			$report->statementAttacker = $awReport['statementAttacker'];
			$report->statementDefender = $awReport['statementDefender'];
			$report->placeName = $awReport['placeName'];

			$this->_Add($report);
		}		
	}

	public function loadWithBig($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'r.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT r.*,
				br.id AS brId,
				br.commanders AS brCommanders,
				br.fight AS brFight,
				br.deletedOnce AS brDeletedOnce
			FROM report AS r
			LEFT JOIN bigReport AS br
				ON br.id = r.rBigReport
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

		$aw = $qr->fetchAll();
		$qr->closeCursor();


		foreach($aw AS $awReport) {
			$report = new Report();

			$report->id = $awReport['id'];
			$report->resources = $awReport['resources'];
			$report->expCom = $awReport['expCom'];
			$report->expPlayerA = $awReport['expPlayerA'];
			$report->expPlayerD = $awReport['expPlayerD'];
			$report->rPlayerWinner = $awReport['rPlayerWinner'];
			$report->round = $awReport['round'];
			$report->rPlayerAttacker = $awReport['rPlayerAttacker'];
			$report->rPlayerDefender = $awReport['rPlayerDefender'];
			$report->rBigReport = $awReport['rBigReport'];
			$report->rPlace = $awReport['rPlace']; 			
			$report->type = $awReport['type'];
			$report->importance = $awReport['importance'];
			$report->dFight = $awReport['dFight'];
			$report->statementAttacker = $awReport['statementAttacker'];
			$report->statementDefender = $awReport['statementDefender'];
			$report->placeName = $awReport['placeName'];

			if ($awReport['brId'] !== NULL) {
				$report->rBigReport = $awReport['brId'];
				$report->fight = unserialize($awReport['brFight']);
				$report->commanders = unserialize($awReport['brCommanders']);
				$report->deletedOnce = $awReport['brDeletedOnce'];
			}
			
			$this->_Add($report);
		}
	}

	public function save() {
		/*$db = DataBase::getInstance();
		$reports = $this->_Save();

		foreach ($reports AS $report) {
			if ($report->commanders != NULL) {
				$qr = $db->prepare('UPDATE bigReport 
					SET 
						commanders = ?,
						fight = ?,
						dletedOnce = ?,
						WHERE id = ?');
				$qr->execute(array(
					$report->commanders,
					$report->fight,
					$report->deletedOnce,
					$report->rBigReport));
			}

			$qr2 = $db2->prepare('UPDATE report
				SET
					rPlayerAttacker = ?,
					rPlayerDefender = ?,
					rPlayerWinner = ?,
					rBigReport = ?,
					resources = ?,
					expCom = ?,
					expPlayerA = ?,
					expPlayerD = ?,
					rPlace = ?,
					type = ?,
					round = ?,
					importance = ?,
					statementAttacker = ?,
					statementDefender = ?,
					dFight = ?');
			$aw = $qr2->execute(array(
				$report->rPlayerAttacker,
				$report->rPlayerDefender,
				$report->rPlayerWinner,
				$report->rBigReport,
				$report->resources,
				$report->expCom,
				$report->expPlayerA,
				$report->expPlayerD,
				$report->rPlace,
				$report->type,
				$report->round,
				$report->importance,
				$report->statementAttacker,
				$report->statementDefender,
				$report->dFight
				)
			);
		}*/
	}

	public function add($newReport) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO bigReport(fight, commanders) VALUES (?, ?)');
		$aw = $qr->execute(array(
			serialize($newReport->fight),
			serialize($newReport->commanders),
			)
		);

		$bigReportId = $db->lastInsertId();
		$db2 = DataBase::getInstance();

		$qr2 = $db2->prepare('INSERT INTO report SET
			rPlayerAttacker = ?,
			rPlayerDefender = ?,
			rPlayerWinner = ?,
			rBigReport = ?,
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
			dFight = ?');
		$aw = $qr2->execute(array(
			$newReport->rPlayerAttacker,
			$newReport->rPlayerDefender,
			$newReport->rPlayerWinner,
			$bigReportId,
			$newReport->resources,
			$newReport->expCom,
			$newReport->expPlayerA,
			$newReport->expPlayerD,
			$newReport->rPlace,
			$newReport->placeName,
			$newReport->type,
			$newReport->round,
			$newReport->importance,
			$newReport->statementAttacker,
			$newReport->statementDefender,
			$newReport->dFight
			));

		$newReport->id = $db->lastInsertId();
		$this->_Add($newReport);

		return $newReport->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM bigReport WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	# assez chiant mais à faire
	public function deleteByRPlayer($playerId) {
		$this->load(array('rPlayerAttacker' => $playerId));
		$this->load(array('rPlayerDefender' => $playerId));

		$nbrDeleted = 0;

		if ($this->size() > 0) {
			foreach ($this->objects[$this->currentSession->getId()] AS $report) {
				if ($report->rPlayerAttacker == $playerId AND $report->archivedAttacker == 0 AND $report->rBigReportAttacker != 0) {
					$this->deleteById($report->rBigReportAttacker);
					$report->rBigReportAttacker = 0;
					$nbrDeleted++;
				} else if ($report->rPlayerDefender == $playerId AND $report->archivedDefender == 0 AND $report->rBigReportDefender != 0) {
					$this->deleteById($report->rBigReportDefender);
					$report->rBigReportDefender = 0;
					$nbrDeleted++;
				}
			}
		}

		return $nbrDeleted;
	}
}
?>