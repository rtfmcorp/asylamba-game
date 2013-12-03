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

class SectorManager {
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

	public function getBYId($id) {
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
				$sector->setXPosition($aw['xPosition']);
				$sector->setYPosition($aw['yPosition']);
				$sector->setXBarycentric($aw['xBarycentric']);
				$sector->setYBarycentric($aw['yBarycentric']);
				$sector->setTax($aw['tax']);
				$sector->setPopulation($aw['population']);
				$sector->setLifePlanet($aw['lifePlanet']);
				$sector->setRuine($aw['ruine']);
				$sector->setNebuleuse($aw['nebuleuse']);
				$sector->setGeante($aw['geante']);
				$sector->setNJaune($aw['nJaune']);
				$sector->setNRouge($aw['nRouge']);
				$sector->setDescription($aw['description']);

				$this->sectors[] = array($sector, TRUE);

				$this->genStats[0] += $aw['population'];
				$this->genStats[1] += $aw['lifePlanet'];
				$this->genStats[2] += $aw['ruine'];
				$this->genStats[3] += $aw['nebuleuse'];
				$this->genStats[4] += $aw['geante'];
				$this->genStats[5] += $aw['nJaune'];
				$this->genStats[6] += $aw['nRouge'];

				if ($aw['rColor']) {
					$this->colStats[$aw['rColor'] - 1][0] += $aw['population'];
					$this->colStats[$aw['rColor'] - 1][1] += $aw['lifePlanet'];
					$this->colStats[$aw['rColor'] - 1][2] += $aw['ruine'];
					$this->colStats[$aw['rColor'] - 1][3] += $aw['nebuleuse'];
					$this->colStats[$aw['rColor'] - 1][4] += $aw['geante'];
					$this->colStats[$aw['rColor'] - 1][5] += $aw['nJaune'];
					$this->colStats[$aw['rColor'] - 1][6] += $aw['nRouge'];
				}

				$k++;
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
				$sector->setXPosition($s['xPosition']);
				$sector->setYPosition($s['yPosition']);
				$sector->setXBarycentric($aw['xBarycentric']);
				$sector->setYBarycentric($aw['yBarycentric']);
				$sector->setTax($s['tax']);
				$sector->setPopulation($s['population']);
				$sector->setLifePlanet($s['lifePlanet']);
				$sector->setRuine($s['ruine']);
				$sector->setNebuleuse($s['nebuleuse']);
				$sector->setGeante($s['geante']);
				$sector->setNJaune($s['nJaune']);
				$sector->setNRouge($s['nRouge']);
				$sector->setDescription($s['description']);

				$this->sectors[] = array($sector, TRUE);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}

	# TODO: function save

	public function size() {
		return count($this->sectors);
	}
}