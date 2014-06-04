<?php

/**
 * UserRanking
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Atlas
 * @update 04.06.14
 */

class UserRanking {
	
	# attributes
	public $id; 
	public $rRanking;
	public $rPlayer; 

	public $general;			# pts des bases + flottes + commandants
	public $generalPosition;
	public $generalVariation;

	public $experience;
	public $experiencePosition;
	public $experienceVariation;

	public $victory;
	public $victoryPosition;
	public $victoryVariation;

	public $defeat;
	public $defeatPosition;
	public $defeatVariation;

	public $ratio; 				# ratio victory - defeat 
	public $ratioPosition;
	public $ratioVariation;

	# additional attributes
	public $color;
	public $name;
	# public $...
	

	public function getId() { return $this->id; }
}
