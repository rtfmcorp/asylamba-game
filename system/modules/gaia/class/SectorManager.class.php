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

class SectorManager extends Manager {
	protected $managerType = '_Sector';

	private $sectors = array();

	private $genStats = array(0, 0, 0, 0, 0, 0, 0);
	private $avrStats = array(0, 0, 0, 0, 0, 0, 0);
	private $colStats = array(array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0, 0), array(0, 0, 0, 0, 0, 0, 0));

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

	public function getGenStats($type, $stats) {
		if ($type == 'avr') {
			return $this->avrStats[$stats];
		} else {
			return $this->genStats[$stats];
		}
	}

	public function getColStats($color, $stats) {
		return $this->colStats[$color - 1][$stats];
	}

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
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
				$sector->setPopulation($aw['population']);
				$sector->setLifePlanet($aw['lifePlanet']);

				$this->sectors[] = array($sector, TRUE);

				$this->genStats[0] += $aw['population'];
				$this->genStats[1] += $aw['lifePlanet'];

				if ($aw['rColor']) {
					$this->colStats[$aw['rColor'] - 1][0] += $aw['population'];
					$this->colStats[$aw['rColor'] - 1][1] += $aw['lifePlanet'];
				}

				$k++;

				$this->_Add($sector);
			}

			$this->avrStats = $this->genStats;
			for ($i = 0; $i < count($this->avrStats); $i++) { 
				$this->avrStats[$i] = round($this->avrStats[$i] / $k, 2);
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

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT 
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
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE sector
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