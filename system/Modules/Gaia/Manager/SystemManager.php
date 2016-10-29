<?php

/**
 * System Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 09.07.13
*/
namespace Asylamba\Modules\Gaia\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Library\Utils;

use Asylamba\Modules\Gaia\Model\System;

class SystemManager extends Manager {
	protected $managerType = '_System';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 's.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = Database::getInstance();
		$qr = $db->prepare('SELECT s.*
			FROM system AS s
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

		foreach ($aw AS $s) {
			$system = new System();
			$system->id = $s['id'];
			$system->rSector = $s['rSector'];
			$system->rColor = $s['rColor'];
			$system->xPosition = $s['xPosition'];
			$system->yPosition = $s['yPosition'];
			$system->typeOfSystem = $s['typeOfSystem'];

			$this->_Add($system);
		}
	}

	public function save() {
		$systems = $this->_Save();

		foreach ($systems AS $s) {
			$db = Database::getInstance();
			$qr = $db->prepare('UPDATE system
				SET	rColor = ?
				WHERE id = ?');
			$qr->execute(array(
				$s->rColor,
				$s->id
			));
		}
	}
}
