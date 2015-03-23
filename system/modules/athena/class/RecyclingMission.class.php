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

	const RECYCLER_CAPACTIY = 1000;
	const RECYCLING_TIME = 7200; # 2 hours, in seconds
	const COEF_SHIP = 0.4; # to convert points to resource for ships

	public $id = 0;
	public $rBase = 0;
	public $rTarget = 0;
	public $cycleTime = 0;
	public $recyclerQuantity = 0;
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