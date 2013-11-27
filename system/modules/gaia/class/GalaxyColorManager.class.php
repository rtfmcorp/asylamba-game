<?php
class GalaxyColorManager {
	protected $system = array();
	protected $sector = array();

	public static function apply() {
		$gcm = new GalaxyColorManager();
		$gcm->loadSystem();
		$gcm->loadSector();
		$gcm->changeColorSystem();
		$gcm->changeColorSector();
		$gcm->saveSystem();
		$gcm->saveSector();
	}

	public function loadSystem() {
		$db = DataBase::getInstance();
		$qr = $db->query('SELECT
			se.id AS id,
			se.rSector AS sector,
			se.rColor AS color,
			(SELECT COUNT(pl.id) FROM place AS pl WHERE pl.rSystem = se.id) AS nbPlace,
			(SELECT COUNT(pa.rColor) FROM place AS pl LEFT JOIN player AS pa ON pl.rPlayer = pa.id WHERE pl.rSystem = se.id AND pa.rColor = 1) AS color1,
			(SELECT COUNT(pa.rColor) FROM place AS pl LEFT JOIN player AS pa ON pl.rPlayer = pa.id WHERE pl.rSystem = se.id AND pa.rColor = 2) AS color2,
			(SELECT COUNT(pa.rColor) FROM place AS pl LEFT JOIN player AS pa ON pl.rPlayer = pa.id WHERE pl.rSystem = se.id AND pa.rColor = 3) AS color3,
			(SELECT COUNT(pa.rColor) FROM place AS pl LEFT JOIN player AS pa ON pl.rPlayer = pa.id WHERE pl.rSystem = se.id AND pa.rColor = 4) AS color4,
			(SELECT COUNT(pa.rColor) FROM place AS pl LEFT JOIN player AS pa ON pl.rPlayer = pa.id WHERE pl.rSystem = se.id AND pa.rColor = 5) AS color5,
			(SELECT COUNT(pa.rColor) FROM place AS pl LEFT JOIN player AS pa ON pl.rPlayer = pa.id WHERE pl.rSystem = se.id AND pa.rColor = 6) AS color6,
			(SELECT COUNT(pa.rColor) FROM place AS pl LEFT JOIN player AS pa ON pl.rPlayer = pa.id WHERE pl.rSystem = se.id AND pa.rColor = 7) AS color7
		FROM system AS se
		ORDER BY se.id');

		while ($aw = $qr->fetch()) {
			$this->system[$aw['id']] = array(
				'sector' => $aw['sector'],
				'systemColor' => $aw['color'],
				'nbPlace' => $aw['nbPlace'],
				'color' => array(
					'1' => $aw['color1'],
					'2' => $aw['color2'],
					'3' => $aw['color3'],
					'4' => $aw['color4'],
					'5' => $aw['color5'],
					'6' => $aw['color6'],
					'7' => $aw['color7']),
				'hasChanged' => FALSE
			);
		}
	}

	public function saveSystem() {
		$db = DataBase::getInstance();
		foreach ($this->system as $k => $v) {
			if ($v['hasChanged'] == TRUE) {
				$qr = $db->prepare('UPDATE system SET rColor = ? WHERE id = ?');
				$qr->execute(array($v['systemColor'], $k));
			}
		}
	}

	public function loadSector() {
		$db = DataBase::getInstance();
		$qr = $db->query('SELECT id, rColor FROM sector	ORDER BY id');
		while ($aw = $qr->fetch()) {
			$this->sector[$aw['id']] = array(
				'color' => $aw['rColor'],
				'hasChanged' => FALSE
			);
		}
	}

	public function saveSector() {
		$db = DataBase::getInstance();
		foreach ($this->sector as $k => $v) {
			if ($v['hasChanged'] == TRUE) {
				$qr = $db->prepare('UPDATE sector SET rColor = ? WHERE id = ?');
				$qr->execute(array($v['color'], $k));
			}
		}
	}

	public function changeColorSystem() {
		foreach ($this->system as $k => $v) {
			if (($v['systemColor'] + array_sum($v['color'])) != 0) {
				$currColor = $v['systemColor'];

				$usedArray = $v['color'];

				$frsNumber = max($usedArray);
				$temp = array_keys($usedArray, max($usedArray));
				$frsColor  = $temp[0];

				unset($usedArray[$frsColor]);

				$secNumber = max($usedArray);
				$temp = array_keys($usedArray, max($usedArray));
				$secColor  = $temp[0];

				if ($secNumber == 0) {
					if ($v['systemColor'] != $frsColor) {
						$this->system[$k]['systemColor'] = $frsColor;
						$this->system[$k]['hasChanged'] = TRUE;
					}
				} else {
					if ($frsNumber > $secNumber AND $frsColor != $v['systemColor']) {
						$this->system[$k]['systemColor'] = $frsColor;
						$this->system[$k]['hasChanged'] = TRUE;
					}
				}
			}
		}
	}

	public function changeColorSector() {
		foreach ($this->sector as $k => $v) {
			$colorRepartition = array(0, 0, 0, 0, 0, 0, 0);
			foreach ($this->system as $m => $n) {
				if ($n['sector'] == $k) {
					if ($n['systemColor'] != 0) {
						$colorRepartition[$n['systemColor'] - 1]++;
					}
				}
			}
			if (array_sum($colorRepartition) != 0) {
				$nbrColor = max($colorRepartition);
				$maxColor = array_keys($colorRepartition, max($colorRepartition));
				if ($nbrColor > $colorRepartition[$v['color'] - 1] AND ($maxColor[0] + 1) != $v['color']) {
					$this->sector[$k]['color'] = $maxColor[0] + 1;
					$this->sector[$k]['hasChanged'] = TRUE;
				}
			}
		}
	}
}