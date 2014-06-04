<?php

/**
 * UserRankingManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Atlas
 * @version 04.06.14
 **/

class UserRankingManager extends Manager {
	protected $managerType = '_UserRanking';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'ur.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT ur.*,
			p.rColor AS color,
			p.name AS name
			FROM userRanking AS ur
			LEFT JOIN player AS p 
				ON ur.rPlayer = p.id
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
			$ur = new UserRanking();

			$ur->id = $aw['id']; 
			$ur->rRanking = $aw['rRanking'];
			$ur->rPlayer = $aw['rPlayer']; 
			$ur->general = $aw['general'];
			$ur->generalPosition = $aw['generalPosition'];
			$ur->generalVariation = $aw['generalVariation'];
			$ur->experience = $aw['experience'];
			$ur->experiencePosition = $aw['experiencePosition'];
			$ur->experienceVariation = $aw['experienceVariation'];
			$ur->victory = $aw['victory'];
			$ur->victoryPosition = $aw['victoryPosition'];
			$ur->victoryVariation = $aw['victoryVariation'];
			$ur->defeat = $aw['defeat'];
			$ur->defeatPosition = $aw['defeatPosition'];
			$ur->defeatVariation = $aw['defeatVariation'];
			$ur->ratio = $aw['ratio'];
			$ur->ratioPosition = $aw['ratioPosition'];
			$ur->ratioVariation = $aw['ratioVariation'];

			$ur->color = $aw['color'];
			$ur->name = $aw['name'];

			$currentT = $this->_Add($ur);
		}
	}

	public function add(UserRanking $ur) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			userRanking(rRanking, rPlayer, general, generalPosition, generalVariation, 
				experience, experiencePosition, experienceVariation, 
				victory, victoryPosition, victoryVariation, 
				defeat, defeatPosition, defeatVariation, ratio, ratioPosition, ratioVariation)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$ur->rRanking,
			$ur->rPlayer, 
			$ur->general,
			$ur->generalPosition,
			$ur->generalVariation,
			$ur->experience,
			$ur->experiencePosition,
			$ur->experienceVariation,
			$ur->victory,
			$ur->victoryPosition,
			$ur->victoryVariation,
			$ur->defeat,
			$ur->defeatPosition,
			$ur->defeatVariation,
			$ur->ratio,
			$ur->ratioPosition,
			$ur->ratioVariation
		));

		$ur->id = $db->lastInsertId();

		$this->_Add($ur);
	}

	public function save() {
		$rankings = $this->_Save();

		foreach ($rankings AS $ur) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE userRanking
				SET	id = ?,
					rRanking = ?,
					rPlayer = ?, 
					general = ?,
					generalPosition = ?,
					generalVariation = ?,
					experience = ?,
					experiencePosition = ?,
					experienceVariation = ?,
					victory = ?,
					victoryPosition = ?,
					victoryVariation = ?,
					defeat = ?,
					defeatPosition = ?,
					defeatVariation = ?,
					ratio = ?,
					ratioPosition = ?,
					ratioVariation = ?
				WHERE id = ?');
			$qr->execute(array(
				$ur->id,
				$ur->rRanking,
				$ur->rPlayer, 
				$ur->general,
				$ur->generalPosition,
				$ur->generalVariation,
				$ur->experience,
				$ur->experiencePosition,
				$ur->experienceVariation,
				$ur->victory,
				$ur->victoryPosition,
				$ur->victoryVariation,
				$ur->defeat,
				$ur->defeatPosition,
				$ur->defeatVariation,
				$ur->ratio,
				$ur->ratioPosition,
				$ur->ratioVariation,
				$ur->id
			));
		}
	}
}
?>