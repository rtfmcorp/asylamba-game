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

class FactionRankingManager extends Manager {
	protected $managerType = '_FactionRanking';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
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
			$fr->general = $aw['general'];
			$fr->generalPosition = $aw['generalPosition'];
			$fr->generalVariation = $aw['generalVariation'];
			$fr->power = $aw['power'];
			$fr->powerPosition = $aw['powerPosition'];
			$fr->powerVariation = $aw['powerVariation'];
			$fr->domination = $aw['domination'];
			$fr->dominationPosition = $aw['dominationPosition'];
			$fr->dominationVariation = $aw['dominationVariation'];

			$currentT = $this->_Add($fr);
		}
	}

	public function add(FactionRanking $fr) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			factionRanking(rRanking, rFaction, general, generalPosition, generalVariation, 
				power, powerPosition, powerVariation, domination, dominationPosition, dominationVariation)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$fr->rRanking,
			$fr->rFaction, 
			$fr->general,
			$fr->generalPosition,
			$fr->generalVariation,
			$fr->power,
			$fr->powerPosition,
			$fr->powerVariation,
			$fr->domination,
			$fr->dominationPosition,
			$fr->dominationVariation
		));

		$fr->id = $db->lastInsertId();

		$this->_Add($fr);
	}

	public function save() {
		$rankings = $this->_Save();

		foreach ($rankings AS $fr) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE factionRanking
				SET	id = ?,
					rRanking = ?,
					rFaction = ?, 
					general = ?,
					generalPosition = ?,
					generalVariation = ?,
					power = ?,
					powerPosition = ?,
					powerVariation = ?,
					domination = ?,
					dominationPosition = ?,
					dominationVariation = ?
				WHERE id = ?');
			$qr->execute(array(
				$fr->id,
				$fr->rRanking,
				$fr->rFaction, 
				$fr->general,
				$fr->generalPosition,
				$fr->generalVariation,
				$fr->power,
				$fr->powerPosition,
				$fr->powerVariation,
				$fr->domination,
				$fr->dominationPosition,
				$fr->dominationVariation,
				$fr->id
			));
		}
	}
}
?>