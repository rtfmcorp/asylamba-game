<?php
class ResearchResource {
	/**
	 * 0 = math, 1 = physique, 2 = chimie
	 * 3 = biologie (droit), 4 = médecine (communication)
	 * 5 = économie, 6 = psychologie
	 * 7 = réseaux, 8 = algorithmique, 9 = statistiques
	 **/
	private static $availableResearch = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
	
	public static function isAResearch($research) {
		return (in_array($research, self::$availableResearch)) ? TRUE : FALSE;
	}

	public static function getInfo($research, $info, $level = 0, $sup = 'delfault') {
		if (self::isAResearch($research)) {
			if ($info == 'name' || $info == 'codeName') {
				return self::$research[$research][$info];
			} elseif ($info == 'level') {
				if ($level <= 0) {
					return FALSE;
				}
				if ($sup == 'price') {
					return self::researchPrice($research, $level) * RSM_RESEARCHCOEF;
				}
			} else {
				CTR::$alert->add('Wrong second argument for method getInfo() from ResearchResource', ALT_BUG_ERROR);
			}
		} else {
			CTR::$alert->add('This research doesn\'t exist !', ALT_BUG_ERROR);
		}
		return FALSE;
	}

	public static function isResearchPermit($firstLevel, $secondLevel, $thirdLevel = -1) {
		// compare the levels of technos and say if you can research such techno
		if ($thirdLevel == -1) {
			if (abs($firstLevel - $secondLevel) > RSM_RESEARCHMAXDIFF) { 
				return FALSE;
			} else { return TRUE; }
		} else {
			if (abs($firstLevel - $secondLevel) > RSM_RESEARCHMAXDIFF) {
				return FALSE;
			} elseif (abs($firstLevel - $thirdLevel) > RSM_RESEARCHMAXDIFF) {
				return FALSE;
			} elseif (abs($secondLevel - $thirdLevel) > RSM_RESEARCHMAXDIFF) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	private static function researchPrice($research, $level) {
		switch ($research) {
			case 0 :
				if ($level == 1) {
					return 100;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
					# ancienne : return round((-4451.2 * pow($level, 3)) + (138360 * pow($level, 2)) - (526711 * $level) + 589669);
				}
				break;
			case 1 :
				if ($level == 1) {
					return 3000;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			case 2 :
				if ($level == 1) {
					return 7000;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			case 3 :
				if ($level == 1) {
					return 200;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			case 4 :
				if ($level == 1) {
					return 9000;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			case 5 :
				if ($level == 1) {
					return 200;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			case 6 :
				if ($level == 1) {
					return 9000;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			case 7 :
				if ($level == 1) {
					return 200;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			case 8 :
				if ($level == 1) {
					return 4000;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			case 9 :
				if ($level == 1) {
					return 6000;
				} else {
					return round((0.0901 * pow($level, 5)) - (12.988 * pow($level, 4)) + (579.8 * pow($level, 3)) - (5735.8 * pow($level, 2)) + (28259 * $level) - 25426);
				}
				break;
			default:
				return FALSE;
				break;
		}
	}
	
	private static $research = array(
		array(
			'name' => 'Mathématiques',
			'codeName' => 'mathematics'
			),
		array(
			'name' => 'Physique',
			'codeName' => 'physics'
			),
		array(
			'name' => 'Chimie',
			'codeName' => 'chemistry'
			),
		array(
			'name' => 'Droit',
			'codeName' => 'biology'
			),
		array(
			'name' => 'Communication',
			'codeName' => 'medicine'
			),
		array(
			'name' => 'Economie',
			'codeName' => 'economy'
			),
		array(
			'name' => 'Psychologie',
			'codeName' => 'psychology'
			),
		array(
			'name' => 'Réseaux',
			'codeName' => 'networks'
			),
		array(
			'name' => 'Algorithmique',
			'codeName' => 'algorithmic'
			),
		array(
			'name' => 'Statistiques',
			'codeName' => 'statistics'
		)
	);
}
?>