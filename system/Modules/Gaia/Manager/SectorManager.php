<?php

/**
 * Sector Manager
 *
 * @author Expansion
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 20.05.13
*/
namespace Asylamba\Modules\Gaia\Manager;

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Worker\Manager;
use Asylamba\Modules\Gaia\Model\Sector;

class SectorManager extends Manager {
	protected $managerType = '_Sector';

	private $sectors = array();
	
	/**
	 * @param Database $database
	 */
	public function __construct(Database $database) {
		parent::__construct($database);
	}

	public function get($position = 0) {
		if (isset($this->sectors[$position][0])) {
			$this->sectors[$position][1] = FALSE;
			return $this->sectors[$position][0];
		} else {
			return FALSE;
		}
	}

	public function getById($id) {
		foreach ($this->sectors as $k => $v) {
			if ($v[0]->getId() == $id) {
				$this->sectors[$k][1] = FALSE;
				return $this->sectors[$k][0];
			}
		}
		return FALSE;
	}

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT *
			FROM sector
			' . $formatWhere . '
			' . $formatOrder . '
			' . $formatLimit);

		foreach ($where AS $v) {
			$valuesArray[] = $v;
		}

		if (empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		$answer = $qr->fetchAll();
		if (!empty($answer)) {
			$k = 1;

			foreach ($answer as $aw) {
				$sector = new Sector();

				$sector->setId($aw['id']);
				$sector->setRColor($aw['rColor']);
				$sector->rSurrender = $aw['rSurrender'];
				$sector->setXPosition($aw['xPosition']);
				$sector->setYPosition($aw['yPosition']);
				$sector->setXBarycentric($aw['xBarycentric']);
				$sector->setYBarycentric($aw['yBarycentric']);
				$sector->setTax($aw['tax']);
				$sector->setName($aw['name']);
				$sector->setPoints($aw['points']);
				$sector->setPopulation($aw['population']);
				$sector->setLifePlanet($aw['lifePlanet']);

				$this->sectors[] = array($sector, TRUE);

				$k++;

				$this->_Add($sector);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function loadByOr($module, $list = array()) {
		$query = '';
		foreach ($list as $v) { $query .= $module . ' = ? OR '; }
		$query = trim($query, 'OR ');

		$qr = $this->database->prepare('SELECT 
			DISTINCT(se.id),
			se.*
			FROM sector AS se
			LEFT JOIN system AS sy
				ON sy.rSector = se.id
			LEFT JOIN place AS pl
				ON pl.rSystem = sy.id
			WHERE ' . $query);

		if (empty($list)) {
			$qr->execute();
		} else {
			$qr->execute($list);
		}

		$aw = $qr->fetchAll();
		if (!empty($aw)) {
			foreach ($aw as $s) {
				$sector = new Sector();

				$sector->setId($s['id']);
				$sector->setRColor($s['rColor']);
				$sector->rSurrender = $s['rSurrender'];
				$sector->setXPosition($s['xPosition']);
				$sector->setYPosition($s['yPosition']);
				$sector->setXBarycentric($aw['xBarycentric']);
				$sector->setYBarycentric($aw['yBarycentric']);
				$sector->setTax($s['tax']);
				$sector->setName($s['name']);
				$sector->setPoints($s['points']);
				$sector->setPopulation($s['population']);
				$sector->setLifePlanet($s['lifePlanet']);

				$this->sectors[] = array($sector, TRUE);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function save() {
		$sectors = $this->_Save();
		foreach ($sectors AS $s) {
			$qr = $this->database->prepare('UPDATE sector
				SET
					rSurrender = ?,
					tax = ?,
					name = ?
				WHERE id = ?');
			$qr->execute(array(
				$s->rSurrender,
				$s->tax,
				$s->name,
				$s->id
			));
		}
	}

	public function size() {
		return count($this->sectors);
	}
}