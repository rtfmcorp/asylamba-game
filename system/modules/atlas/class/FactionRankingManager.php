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

	public function loadLastContext($where = array(), $order = array(), $limit = array()) {	
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT * FROM ranking WHERE faction = 1 ORDER BY dRanking DESC LIMIT 1');
		$qr->execute();
		$aw = $qr->fetch();
		$rRanking = $aw['id'];

		# add the rRanking to the WHERE clause
		$where['rRanking'] = $rRanking;

		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

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

	public function loadByRequest($request, $args = array()) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
			FROM factionRanking AS fr
			' . $request
		);

		$qr->execute($args);

		while ($aw = $qr->fetch()) {
			$fr = new FactionRanking();

			$fr->id = isset($aw['id']) ? $aw['id'] : NULL;
			$fr->rRanking = isset($aw['rRanking']) ? $aw['rRanking'] : NULL;
			$fr->rFaction = isset($aw['rFaction']) ? $aw['rFaction'] : NULL;
			$fr->general = isset($aw['general']) ? $aw['general'] : NULL;
			$fr->generalPosition = isset($aw['generalPosition']) ? $aw['generalPosition'] : NULL;
			$fr->generalVariation = isset($aw['generalVariation']) ? $aw['generalVariation'] : NULL;
			$fr->power = isset($aw['power']) ? $aw['power'] : NULL;
			$fr->powerPosition = isset($aw['powerPosition']) ? $aw['powerPosition'] : NULL;
			$fr->powerVariation = isset($aw['powerVariation']) ? $aw['powerVariation'] : NULL;
			$fr->domination = isset($aw['domination']) ? $aw['domination'] : NULL;
			$fr->dominationPosition = isset($aw['dominationPosition']) ? $aw['dominationPosition'] : NULL;
			$fr->dominationVariation = isset($aw['dominationVariation']) ? $aw['dominationVariation'] : NULL;

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