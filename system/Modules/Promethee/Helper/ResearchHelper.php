<?php

namespace Asylamba\Modules\Promethee\Helper;

use Asylamba\Modules\Promethee\Resource\ResearchResource;
use Asylamba\Classes\Exception\ErrorException;

class ResearchHelper
{
	public function __construct(
		protected int $researchCoeff,
		protected int $researchMaxDiff
	) {
	}
	
	public function isAResearch($research) {
		return in_array($research, ResearchResource::$availableResearch);
	}

	public function getInfo($research, $info, $level = 0, $sup = 'delfault')
	{
		if ($this->isAResearch($research)) {
			if ($info == 'name' || $info == 'codeName') {
				return ResearchResource::$research[$research][$info];
			} elseif ($info == 'level') {
				if ($level <= 0) {
					return FALSE;
				}
				if ($sup == 'price') {
					return $this->researchPrice($research, $level) * $this->researchCoeff;
				}
			} else {
				throw new ErrorException('Wrong second argument for method getInfo() from ResearchResource');
			}
		} else {
			throw new ErrorException('This research doesn\'t exist !');
		}
		return FALSE;
	}

	public function isResearchPermit(int $firstLevel, int $secondLevel, int $thirdLevel = -1): bool
	{
		// compare the levels of technos and say if you can research such techno
		if ($thirdLevel == -1) {
			if (abs($firstLevel - $secondLevel) > $this->researchMaxDiff) {
				return FALSE;
			} else { return TRUE; }
		} else {
			if (abs($firstLevel - $secondLevel) > $this->researchMaxDiff) {
				return FALSE;
			} elseif (abs($firstLevel - $thirdLevel) > $this->researchMaxDiff) {
				return FALSE;
			} elseif (abs($secondLevel - $thirdLevel) > $this->researchMaxDiff) {
				return FALSE;
			} else {
				return TRUE;
			}
		}
	}

	private function researchPrice($research, $level) {
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
		}
	}
}
