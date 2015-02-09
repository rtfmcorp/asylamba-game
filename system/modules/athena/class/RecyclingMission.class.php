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

	public $id = 0;
	public $rBase = 0;
	public $rTarget = 0;
	public $cycleTime = 0;
	public $recyclerQuantity = 0;
	public $uRecycling = 0;
	public $statement = 0;

	public function getId()	{ return $this->id; }
}