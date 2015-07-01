<?php

/**
 * RecyclingMission
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @update 09.02.15
 */

class RecyclingMission {

	const ST_DELETED = 0;
	const ST_ACTIVE = 1;
	const ST_BEING_DELETED = 2;

	const RECYCLER_CAPACTIY = 400;
	const RECYCLING_TIME = 28800; # 8 hours, in seconds
	const COEF_SHIP = 1.7; # to convert points to resource for ships
		# coef_ship a été calculé par un ingénieur. Si on change la capacité, il faut rechanger coef_ship

	public $id = 0;
	public $rBase = 0;
	public $rTarget = 0;
	public $cycleTime = 0;
	public $recyclerQuantity = 0;
	public $addToNextMission = 0;
	public $uRecycling = '';
	public $statement = 1;

	public $typeOfPlace;
	public $position;
	public $population;
	public $coefResources;
	public $coefHistory;
	public $resources;
	public $systemId;
	public $xSystem;
	public $ySystem;
	public $typeOfSystem;
	public $sectorId;

	public function getId()	{ return $this->id; }
}