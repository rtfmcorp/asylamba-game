<?php

/**
 * RecyclingLog
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @update 09.02.15
 */

namespace App\Modules\Athena\Model;

class RecyclingLog {

	public $id = 0;
	public $rRecycling = 0;
	public $resources = 0;
	public $credits = 0;
	public $ship0 = 0;
	public $ship1 = 0;
	public $ship2 = 0;
	public $ship3 = 0;
	public $ship4 = 0;
	public $ship5 = 0;
	public $ship6 = 0;
	public $ship7 = 0;
	public $ship8 = 0;
	public $ship9 = 0;
	public $ship10 = 0;
	public $ship11 = 0;
	public $dLog = 0;

	public function getId()	{ return $this->id; }
	public function getRRecycling() { return $this->rRecycling; }
	public function getResources() { return $this->resources; }
	public function getCredits() { return $this->credits; }
	public function getShip0() { return $this->ship0; }
	public function getShip1() { return $this->ship1; }
	public function getShip2() { return $this->ship2; }
	public function getShip3() { return $this->ship3; }
	public function getShip4() { return $this->ship4; }
	public function getShip5() { return $this->ship5; }
	public function getShip6() { return $this->ship6; }
	public function getShip7() { return $this->ship7; }
	public function getShip8() { return $this->ship8; }
	public function getShip9() { return $this->ship9; }
	public function getShip10() { return $this->ship10; }
	public function getShip11() { return $this->ship11; }
	public function getDLog() { return $this->dLog; }

	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	public function setRRecycling($rRecycling)
	{
		$this->rRecycling = $rRecycling;
		return $this;
	}
	public function setResouces($resources)
	{
		$this->resources = $resources;
		return $this;
	}
	public function setCredits($credits)
	{
		$this->credits = $credits;
		return $this;
	}
	public function setShip0($ship0)
	{
		$this->ship0 = $ship0;
		return $this;
	}
	public function setShip1($ship1)
	{
		$this->ship1 = $ship1;
		return $this;
	}
	public function setShip2($ship2)
	{
		$this->ship2 = $ship2;
		return $this;
	}
	public function setShip3($ship3)
	{
		$this->ship3 = $ship3;
		return $this;
	}
	public function setShip4($ship4)
	{
		$this->ship4 = $ship4;
		return $this;
	}
	public function setShip5($ship5)
	{
		$this->ship5 = $ship5;
		return $this;
	}
	public function setShip6($ship6)
	{
		$this->ship6 = $ship6;
		return $this;
	}
	public function setShip7($ship7)
	{
		$this->ship7 = $ship7;
		return $this;
	}
	public function setShip8($ship8)
	{
		$this->ship8 = $ship8;
		return $this;
	}
	public function setShip9($ship9)
	{
		$this->ship9 = $ship9;
		return $this;
	}
	public function setShip10($ship10)
	{
		$this->ship10 = $ship10;
		return $this;
	}
	public function setShip11($ship11)
	{
		$this->ship11 = $ship11;
		return $this;
	}
	public function setDLog($dLog)
	{
		$this->dLog = $dLog;
		return $this;
	}
}
