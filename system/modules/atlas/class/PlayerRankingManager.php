<?php

/**
 * PlayerRankingManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Atlas
 * @version 04.06.14
 **/

class PlayerRankingManager extends Manager {
	protected $managerType = '_PlayerRanking';

	public function loadLastContext($where = array(), $order = array(), $limit = array()) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT * FROM ranking WHERE player = 1 ORDER BY dRanking DESC LIMIT 1');
		$qr->execute();
		$aw = $qr->fetch();
		$rRanking = $aw['id'];

		# add the rRanking to the WHERE clause
		$where['rRanking'] = $rRanking;

		$formatWhere = Utils::arrayToWhere($where, 'pl.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT pl.*,
				p.rColor AS color,
				p.name AS name,
				p.avatar AS avatar,
				p.status AS status
			FROM playerRanking AS pl
			LEFT JOIN player AS p 
				ON pl.rPlayer = p.id
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
			$pl = new playerRanking();

			$pl->id = $aw['id']; 
			$pl->rRanking = $aw['rRanking'];
			$pl->rPlayer = $aw['rPlayer']; 
			$pl->general = $aw['general'];
			$pl->generalPosition = $aw['generalPosition'];
			$pl->generalVariation = $aw['generalVariation'];
			$pl->experience = $aw['experience'];
			$pl->experiencePosition = $aw['experiencePosition'];
			$pl->experienceVariation = $aw['experienceVariation'];
			$pl->fight = $aw['fight'];
			$pl->fightPosition = $aw['fightPosition'];
			$pl->fightVariation = $aw['fightVariation'];
			$pl->trader = $aw['trader'];
			$pl->traderPosition = $aw['traderPosition'];
			$pl->traderVariation = $aw['traderVariation'];
			$pl->armies = $aw['armies'];
			$pl->armiesPosition = $aw['armiesPosition'];
			$pl->armiesVariation = $aw['armiesVariation'];
			$pl->resources = $aw['resources'];
			$pl->resourcesPosition = $aw['resourcesPosition'];
			$pl->resourcesVariation = $aw['resourcesVariation'];
			$pl->butcher = $aw['butcher'];
			$pl->butcherPosition = $aw['butcherPosition'];
			$pl->butcherVariation = $aw['butcherVariation'];

			$pl->color = $aw['color'];
			$pl->name = $aw['name'];
			$pl->avatar = $aw['avatar'];
			$pl->status = $aw['status'];

			$currentT = $this->_Add($pl);
		}
	}

	public function loadByRequest($request) {
		$formatWhere = Utils::arrayToWhere($where, 'pl.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare($request);

		while($aw = $qr->fetch()) {
			$pl = new playerRanking();

			if (isset($aw['id'])) { $pl->id = $aw['id']; } 
			if (isset($aw['rRanking'])) { $pl->rRanking = $aw['rRanking']; }
			if (isset($aw['rPlayer'])) { $pl->rPlayer = $aw['rPlayer']; } 
			if (isset($aw['general'])) { $pl->general = $aw['general']; }
			if (isset($aw['generalPosition'])) { $pl->generalPosition = $aw['generalPosition']; }
			if (isset($aw['generalVariation'])) { $pl->generalVariation = $aw['generalVariation']; }
			if (isset($aw['experience'])) { $pl->experience = $aw['experience']; }
			if (isset($aw['experiencePosition'])) { $pl->experiencePosition = $aw['experiencePosition']; }
			if (isset($aw['experienceVariation'])) { $pl->experienceVariation = $aw['experienceVariation']; }
			if (isset($aw['fight'])) { $pl->fight = $aw['fight']; }
			if (isset($aw['fightPosition'])) { $pl->fightPosition = $aw['fightPosition']; }
			if (isset($aw['fightVariation'])) { $pl->fightVariation = $aw['fightVariation']; }
			if (isset($aw['trader'])) { $pl->trader = $aw['trader']; }
			if (isset($aw['traderPosition'])) { $pl->traderPosition = $aw['traderPosition']; }
			if (isset($aw['traderVariation'])) { $pl->traderVariation = $aw['traderVariation']; }
			if (isset($aw['armies'])) { $pl->armies = $aw['armies']; }
			if (isset($aw['armiesPosition'])) { $pl->armiesPosition = $aw['armiesPosition']; }
			if (isset($aw['armiesVariation'])) { $pl->armiesVariation = $aw['armiesVariation']; }
			if (isset($aw['resources'])) { $pl->resources = $aw['resources']; }
			if (isset($aw['resourcesPosition'])) { $pl->resourcesPosition = $aw['resourcesPosition']; }
			if (isset($aw['resourcesVariation'])) { $pl->resourcesVariation = $aw['resourcesVariation']; }
			if (isset($aw['butcher'])) { $pl->butcher = $aw['butcher']; }
			if (isset($aw['butcherPosition'])) { $pl->butcherPosition = $aw['butcherPosition']; }
			if (isset($aw['butcherVariation'])) { $pl->butcherVariation = $aw['butcherVariation']; }

			if (isset($aw['color'])) { $pl->color = $aw['color']; }
			if (isset($aw['name'])) { $pl->name = $aw['name']; }
			if (isset($aw['avatar'])) { $pl->avatar = $aw['avatar']; }
			if (isset($aw['status'])) { $pl->status = $aw['status']; }

			$currentT = $this->_Add($pl);
		}
	}

	public function add(playerRanking $pl) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			playerRanking(rRanking, rPlayer, 
				general, generalPosition, generalVariation, 
				experience, experiencePosition, experienceVariation, 
				fight, fightPosition, fightVariation, 
				trader, traderPosition, traderVariation, 
				armies, armiesPosition, armiesVariation, 
				resources, resourcesPosition, resourcesVariation, 
				butcher, butcherPosition, butcherVariation)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$pl->rRanking,
			$pl->rPlayer, 
			$pl->general,
			$pl->generalPosition,
			$pl->generalVariation,
			$pl->experience,
			$pl->experiencePosition,
			$pl->experienceVariation,
			$pl->fight,
			$pl->fightPosition,
			$pl->fightVariation,
			$pl->trader,
			$pl->traderPosition,
			$pl->traderVariation,
			$pl->armies,
			$pl->armiesPosition,
			$pl->armiesVariation,
			$pl->resources,
			$pl->resourcesPosition,
			$pl->resourcesVariation,
			$pl->butcher,
			$pl->butcherPosition,
			$pl->butcherVariation
		));

		$pl->id = $db->lastInsertId();

		$this->_Add($pl);
	}

	public function save() {
		$rankings = $this->_Save();

		foreach ($rankings AS $pl) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE playerRanking
				SET	id = ?,
					rRanking = ?,
					rPlayer = ?, 
					general = ?,
					generalPosition = ?,
					generalVariation = ?,
					experience = ?,
					experiencePosition = ?,
					experienceVariation = ?,
					fight = ?,
					fightPosition = ?,
					fightVariation = ?,
					trader = ?,
					traderPosition = ?,
					traderVariation = ?,
					armies = ?,
					armiesPosition = ?,
					armiesVariation = ?,
					resources = ?,
					resourcesPosition = ?,
					resourcesVariation = ?,
					butcher = ?,
					butcherPosition = ?,
					butcherVariation = ?
				WHERE id = ?');
			$qr->execute(array(
				$pl->id,
				$pl->rRanking,
				$pl->rPlayer, 
				$pl->general,
				$pl->generalPosition,
				$pl->generalVariation,
				$pl->experience,
				$pl->experiencePosition,
				$pl->experienceVariation,
				$pl->fight,
				$pl->fightPosition,
				$pl->fightVariation,
				$pl->trader,
				$pl->traderPosition,
				$pl->traderVariation,
				$pl->armies,
				$pl->armiesPosition,
				$pl->armiesVariation,
				$pl->resources,
				$pl->resourcesPosition,
				$pl->resourcesVariation,
				$pl->butcher,
				$pl->butcherPosition,
				$pl->butcherVariation,
				$pl->id
			));
		}
	}
}
?>