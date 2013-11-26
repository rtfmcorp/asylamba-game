<?php

/**
 * System
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update xx.xx.xx
*/

class System {
	public $id			 	= 0;
	public $rSector 		= 0;
	public $rColor			= 0;
	public $xPosition		= 0;
	public $yPosition		= 0;
	public $typeOfSystem	= 0;

	public function getId() {return $this->id;}
}