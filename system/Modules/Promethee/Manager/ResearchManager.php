<?php

/**
 * ResearchManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 20.05.13
*/

namespace Asylamba\Modules\Promethee\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Promethee\Model\Research;

class ResearchManager extends Manager {
	protected $managerType = '_Research';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT *
			FROM research
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

		if (empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		while($aw = $qr->fetch()) {
			$res = new Research();

			$res->rPlayer = $aw['rPlayer'];
			$res->mathLevel = $aw['mathLevel'];
			$res->physLevel = $aw['physLevel'];
			$res->chemLevel = $aw['chemLevel'];
			$res->bioLevel = $aw['bioLevel'];
			$res->mediLevel = $aw['mediLevel'];
			$res->econoLevel = $aw['econoLevel'];
			$res->psychoLevel = $aw['psychoLevel'];
			$res->networkLevel = $aw['networkLevel'];
			$res->algoLevel = $aw['algoLevel'];
			$res->statLevel = $aw['statLevel'];
			$res->naturalTech = $aw['naturalTech'];
			$res->lifeTech = $aw['lifeTech'];
			$res->socialTech = $aw['socialTech'];
			$res->informaticTech = $aw['informaticTech'];
			$res->naturalToPay = $aw['naturalToPay'];
			$res->lifeToPay = $aw['lifeToPay'];
			$res->socialToPay = $aw['socialToPay'];
			$res->informaticToPay = $aw['informaticToPay'];
			
			$this->_Add($res);
		}
	}

	public function add(Research $res) {
		$db = Database::getInstance();
		$qr = $db->prepare('INSERT INTO
			research(rPlayer, mathLevel, physLevel, chemLevel, bioLevel, mediLevel, econoLevel, psychoLevel, networkLevel, algoLevel, statLevel, naturalTech, lifeTech, socialTech, informaticTech, naturalToPay, lifeToPay, socialToPay, informaticToPay)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$res->rPlayer,
			$res->mathLevel,
			$res->physLevel,
			$res->chemLevel,
			$res->bioLevel,
			$res->mediLevel,
			$res->econoLevel,
			$res->psychoLevel,
			$res->networkLevel,
			$res->algoLevel,
			$res->statLevel,
			$res->naturalTech,
			$res->lifeTech,
			$res->socialTech,
			$res->informaticTech,
			$res->naturalToPay,
			$res->lifeToPay,
			$res->socialToPay,
			$res->informaticToPay
		));

		$this->_Add($res);
	}

	public function save() {
		$researches = $this->_Save();

		foreach ($researches AS $k => $res) {
			$db = Database::getInstance();
			$qr = $db->prepare('UPDATE research
				SET	rPlayer = ?,
					mathLevel = ?,
					physLevel = ?,
					chemLevel = ?,
					bioLevel = ?,
					mediLevel = ?,
					econoLevel = ?,
					psychoLevel = ?,
					networkLevel = ?,
					algoLevel = ?,
					statLevel = ?,
					naturalTech = ?,
					lifeTech = ?,
					socialTech = ?,
					informaticTech = ?,
					naturalToPay = ?,
					lifeToPay = ?,
					socialToPay = ?,
					informaticToPay = ?
				WHERE rPlayer = ?');
			$qr->execute(array(
				$res->rPlayer,
				$res->mathLevel,
				$res->physLevel,
				$res->chemLevel,
				$res->bioLevel,
				$res->mediLevel,
				$res->econoLevel,
				$res->psychoLevel,
				$res->networkLevel,
				$res->algoLevel,
				$res->statLevel,
				$res->naturalTech,
				$res->lifeTech,
				$res->socialTech,
				$res->informaticTech,
				$res->naturalToPay,
				$res->lifeToPay,
				$res->socialToPay,
				$res->informaticToPay,
				$res->rPlayer
			));
		}
	}

	/* This will no longer work
	public static function deleteByRPlayer($rPlayer) {
		try {
			$db = Database::getInstance();
			$qr = $db->prepare('DELETE FROM research WHERE rPlayer = ?');
			$qr->execute(array($rPlayer));
			return TRUE;
		} catch(Exception $e) {
			$_SESSION[SERVERSESS]['alert'][] = array($e->getMessage(), $e->getCode());
		}
	}*/
}