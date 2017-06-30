<?php

/**
 * Building Queue
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 10.02.14
*/
namespace Asylamba\Modules\Athena\Model;

class BuildingQueue {
	// ATTRIBUTES
	public $id;
	public $rOrbitalBase;
	public $buildingNumber;
	public $targetLevel;
	public $dStart;
	public $dEnd;

	public function getId() {
		return $this->id;
	}
	public function getROrbitalBase()
	{
		return $this->rOrbitalBase;
	}
	public function getBuildingNumber()
	{
		return $this->buildingNumber;
	}
	public function getTargetLevel()
	{
		return $this->targetLevel;
	}
	public function getDStart()
	{
		return $this->dStart;
	}
	public function getDEnd()
	{
		return $this->dEnd;
	}

	public function setId($id)
	{
		$this->id=$id;
		return $this;
	}
	public function setROrbitalBase($rOrbitalBase)
	{
		$this->rOrbitalBase=$rOrbitalBase;
		return $this;
	}
	public function setBuildingLevel($buildingLevel)
	{
		$this->buildingLevel=$buildingLevel;
		return $this;
	}
	public function setTargetLevel($targetLevel)
	{
		$this->targetLevel=$targetLevel;
		return $this;
	}
	public function setDStart($dStart)
	{
		$this->dStart=$dStart;
		return $this;
	}
	public function setDEnd($dEnd)
	{
		$this->dEnd=$dEnd;
		return $this;
	}
}
