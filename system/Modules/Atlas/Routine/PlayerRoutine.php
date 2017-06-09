<?php

namespace Asylamba\Modules\Atlas\Routine;

class PlayerRoutine extends AbstractRoutine
{

	const COEF_RESOURCE = 0.001;
	
	public function execute()
	{
		
	}

	protected function cmpGeneral($a, $b)
	{
		if($a['general'] == $b['general']) {
			return 0;
		}
		return ($a['general'] > $b['general']) ? -1 : 1;
	}

	protected function cmpResources($a, $b) {
		if($a['resources'] == $b['resources']) {
			return 0;
		}
		return ($a['resources'] > $b['resources']) ? -1 : 1;
	}

	protected function cmpExperience($a, $b) {
		if($a['experience'] == $b['experience']) {
			return 0;
		}
		return ($a['experience'] > $b['experience']) ? -1 : 1;
	}

	protected function cmpFight($a, $b) {
		if($a['fight'] == $b['fight']) {
			return 0;
		}
		return ($a['fight'] > $b['fight']) ? -1 : 1;
	}

	protected function cmpArmies($a, $b) {
		if($a['armies'] == $b['armies']) {
			return 0;
		}
		return ($a['armies'] > $b['armies']) ? -1 : 1;
	}

	protected function cmpButcher($a, $b) {
		if($a['butcher'] == $b['butcher']) {
			return 0;
		}
		return ($a['butcher'] > $b['butcher']) ? -1 : 1;
	}

	protected function cmpTrader($a, $b) {
		if($a['trader'] == $b['trader']) {
			return 0;
		}
		return ($a['trader'] > $b['trader']) ? -1 : 1;
	}
}