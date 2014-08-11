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

	public function loadByRequest($request) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare($request);

		$qr->execute();

		while($aw = $qr->fetch()) {
			$fr = new FactionRanking();
			if (isset($aw['id'])) { $fr->id = $aw['id']; }
			if (isset($aw['rRanking'])) { $fr->rRanking = $aw['rRanking']; }
			if (isset($aw['rFaction'])) { $fr->rFaction = $aw['rFaction']; } 
			if (isset($aw['general'])) { $fr->general = $aw['general']; }
			if (isset($aw['generalPosition'])) { $fr->generalPosition = $aw['generalPosition']; }
			if (isset($aw['generalVariation'])) { $fr->generalVariation = $aw['generalVariation']; }
			if (isset($aw['power'])) { $fr->power = $aw['power']; }
			if (isset($aw['powerPosition'])) { $fr->powerPosition = $aw['powerPosition']; }
			if (isset($aw['powerVariation'])) { $fr->powerVariation = $aw['powerVariation']; }
			if (isset($aw['domination'])) { $fr->domination = $aw['domination']; }
			if (isset($aw['dominationPosition'])) { $fr->dominationPosition = $aw['dominationPosition']; }
			if (isset($aw['dominationVariation'])) { $fr->dominationVariation = $aw['dominationVariation']; }

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