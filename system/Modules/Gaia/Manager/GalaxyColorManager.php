<?php

namespace Asylamba\Modules\Gaia\Manager;

use Asylamba\Classes\Database\Database;

class GalaxyColorManager {
	/** @var Database **/
	protected $database;
	/** @var boolean **/
	protected $mustApply = true;
	/** @var array **/
	protected $availableFactions;
	/** @var int **/
	protected $limitConquestSector;
	
	public function apply() {
		$this->mustApply = true;
	}
	
	public function mustApply()
	{
		return $this->mustApply;
	}

	/**
	 * @param Database $database
	 * @param array $availableFactions
	 * @param int $limitConquestSector
	 */
	public function __construct(Database $database, $availableFactions, $limitConquestSector)
	{
		$this->database = $database;
		$this->availableFactions = $availableFactions;
		$this->limitConquestSector = $limitConquestSector;
	}
	
	public function applyAndSave() {
		$this->loadSystem();
		$this->loadSector();
		$this->changeColorSystem();
		$this->changeColorSector();
		$this->saveSystem();
		$this->saveSector();
	}

	protected $system = array();
	protected $sector = array();

	public function loadSystem() {
		$requestPart = '';
		foreach ($this->availableFactions as $faction) {
			$requestPart .= '(SELECT COUNT(pa.rColor) FROM place AS pl LEFT JOIN player AS pa ON pl.rPlayer = pa.id WHERE pl.rSystem = se.id AND pa.rColor = ' . $faction . ') AS color' . $faction . ',';
		}
		$requestPart = rtrim($requestPart, ","); # to remove last comma

		$query = 'SELECT
			se.id AS id,
			se.rSector AS sector,
			se.rColor AS color,
			(SELECT COUNT(pl.id) FROM place AS pl WHERE pl.rSystem = se.id) AS nbPlace,
			' . $requestPart . '
		FROM system AS se
		ORDER BY se.id';
		$qr = $this->database->query($query);
		
		while ($aw = $qr->fetch()) {
			$colors = [];
			foreach ($this->availableFactions as $faction) {
				$colors[$faction] = $aw['color' . $faction]; 
			}
			$this->system[$aw['id']] = array(
				'sector' => $aw['sector'],
				'systemColor' => $aw['color'],
				'nbPlace' => $aw['nbPlace'],
				'color' => $colors,
				'hasChanged' => FALSE
			);
		}
	}

	public function saveSystem() {
		foreach ($this->system as $k => $v) {
			if ($v['hasChanged'] == TRUE) {
				$qr = $this->database->prepare('UPDATE system SET rColor = ? WHERE id = ?');
				$qr->execute(array($v['systemColor'], $k));
			}
		}
	}

	public function loadSector() {
		$qr = $this->database->query('SELECT id, rColor, prime FROM sector ORDER BY id');
		while ($aw = $qr->fetch()) {
			$this->sector[$aw['id']] = array(
				'color' => $aw['rColor'],
				'prime' => $aw['prime'],
				'hasChanged' => FALSE
			);
		}
	}

	public function saveSector() {
		foreach ($this->sector as $k => $v) {
			if ($v['hasChanged'] == TRUE) {
				$qr = $this->database->prepare('UPDATE sector SET rColor = ?, prime = ? WHERE id = ?');
				$qr->execute(array($v['color'], $v['prime'], $k));
			}
		}
	}

	public function changeColorSystem() {
		foreach ($this->system as $k => $v) {

			if ($v['systemColor'] + array_sum($v['color']) == 0) {
				# system blanc qui ne change pas
			} elseif ($v['systemColor'] != 0 && array_sum($v['color']) == 0) {
				# system pas blanc devient blanc

				$this->system[$k]['systemColor'] = 0;
				$this->system[$k]['hasChanged'] = TRUE;
			} else {
				# autre cas

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
		$sectorUpdatedColor = [];

		foreach ($this->sector as $k => $v) {
			$colorRepartition = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

			foreach ($this->system as $m => $n) {
				if ($n['sector'] == $k) {
					if ($n['systemColor'] != 0) {
						$colorRepartition[$n['systemColor'] - 1]++;
					}
				}
			}

			$nbrColor = max($colorRepartition);

			if ($v['color'] == 0) {
				$nbrColorSector = NULL;
			} else {
				$nbrColorSector = $colorRepartition[$v['color'] - 1];
			}

			if ($nbrColor >= $this->limitConquestSector) {
				$maxColor = array_keys($colorRepartition, max($colorRepartition));
				$this->sector[$k]['prime'] = FALSE;
				
				if ($nbrColorSector == NULL) {
					$sectorUpdatedColor[] = $this->sector[$k]['color'];
					$this->sector[$k]['color'] = $maxColor[0] + 1;
					$this->sector[$k]['hasChanged'] = TRUE;
				} elseif ($nbrColor > $nbrColorSector AND ($maxColor[0] + 1) != $v['color']) {
					$sectorUpdatedColor[] = $this->sector[$k]['color'];
					$this->sector[$k]['color'] = $maxColor[0] + 1;
					$this->sector[$k]['hasChanged'] = TRUE;
				}
			} else {
				# ne modifie pas un secteur prime s'il n'y a pas assez de joueur dedans
				if ($this->sector[$k]['prime'] == 0) {
					$sectorUpdatedColor[] = $this->sector[$k]['color'];
					$this->sector[$k]['color'] = 0;
					$this->sector[$k]['hasChanged'] = TRUE;
				}
			}
		}
	}
}