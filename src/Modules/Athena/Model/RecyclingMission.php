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
namespace App\Modules\Athena\Model;

class RecyclingMission {

	const ST_DELETED = 0;
	const ST_ACTIVE = 1;
	const ST_BEING_DELETED = 2;

	const RECYCLER_CAPACTIY = 400;
	const RECYCLING_TIME = 28800; # 8 hours, in seconds
	const COEF_SHIP = 1.6; # to convert points to resource for ships
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
	public function getRBase() { return $this->rBase; }
	public function getRTarget() { return $this->rTarget; }
	public function getCycleTime() { return $this->cycleTime; }
	public function getRecyclerQuantity() { return $this->recyclerQuantity; }
	public function getAddToNextMission() { return $this->addToNextMission; }
	public function getURecycling() { return $this->uRecycling; }
	public function getStatement() { return $this->statement; }

	public function getTypeOfPlace() { return $this->typeOfPlace; }
	public function getPosition() { return $this->position; }
	public function getPopulation() { return $this->population; }
	public function getCoefResources() { return $this->coefResources; }
	public function getCoefHistory() { return $this->coefHistory; }
	public function getResources() { return $this->resources; }
	public function getSystemId() { return $this->systemId; }
	public function getXSystem() { return $this->xSystem; }
	public function getYSystem() { return $this->ySystem; }
	public function getTypeOfSystem() { return $this->typeOfSystem; }
	public function getSectorId() { return $this->sectorId; }

	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	public function setRBase($rBase)
	{
		$this->rBase = $rBase;
		return $this;
	}
	public function setCycleTime($cycleTime)
	{
		$this->cycleTime = $cycleTime;
		return $this;
	}
	public function setRecyclerQuantity($recyclerQuantity)
	{
		$this->recyclerQuantity = $recyclerQuantity;
		return $this;
	}
	public function setAddToNextMission($addToNextMission)
	{
		$this->addToNextMission = $addToNextMission;
		return $this;
	}
	public function setURecycling($uRecycling)
	{
		$this->uRecycling = $uRecycling;
		return $this;
	}
	public function setStatement($statement)
	{
		$this->$statement = $statement;
		return $this;
	}

	public function setTypeOfPplace($typeOfPlace)
	{
		$this->typeOfPlace = $typeOfPlace;
		return $this;
	}
	public function setPosition($position)
	{
		$this->position = $position;
		return $this;
	}
	public function setPopulation($population)
	{
		$this->population = $population;
		return $this;
	}
	public function setCoefResources($coefResources)
	{
		$this->coefResources = $coefResources;
		return $this;
	}
	public function setCoefHistory($coefHistory)
	{
		$this->coefHistory = $coefHistory;
		return $this;
	}
	public function setResources($resources)
	{
		$this->resources = $resources;
		return $this;
	}
	public function setSystemId($systemId)
	{
		$this->systemId = $systemId;
		return $this;
	}
	public function setXSystem($xSystem)
	{
		$this->xSystem = $xSystem;
		return $this;
	}
	public function setYSystem($ySystem)
	{
		$this->ySystem = $ySystem;
		return $this;
	}
	public function setTypeOfSystem($typeOfSystem)
	{
		$this->typeOfSystem = $typeOfSystem;
		return $this;
	}
	public function setSectorId($sectorId)
	{
		$this->sectorId = $sectorId;
		return $this;
	}
}
