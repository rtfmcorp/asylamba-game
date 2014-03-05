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
}
?>