<?php

/**
 * SpyReport
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 26.03.14
 */

class SpyReport {
	# constants
	const TYP_NOT_CAUGHT = 0;
	const TYP_ANONYMOUSLY_CAUGHT = 1;
	const TYP_CAUGHT = 2;
	
	# attributes
	public $id = 0; 
	public $rPlayer = NULL; 
	public $price;
	public $rPlace;
	public $placeColor = NULL;

	public $typeOfBase; # 0=empty, 1=ms1, 2=ms2, 3=ms3, 4=ob
	public $typeOfOrbitalBase; # 0=neutral, 1=commercial, 2=military, 3=capital
	public $placeName;
	public $points;

	public $rEnemy;
	public $enemyName;
	public $enemyAvatar;
	public $enemyLevel;

	public $resources; # from place OR base
	public $commanders;

	public $success; # from 0 to 100
	public $type; # see constants
	public $dSpying;

	# additional attributes
	# from place
	public $typeOfPlace;
	public $position;
	public $population;
	public $coefResources;
	public $coefHistory;
	# from system
	public $rSector;
	public $xPosition;
	public $yPosition;
	public $typeOfSystem;

	public function getId() { return $this->id; }
}