<?php

/**
 * FactionRanking
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Atlas
 * @update 04.06.14
 */

class FactionRanking {
	
	# attributes
	public $id; 
	public $rRanking;
	public $rFaction; 

	public $general; 				# credits
	public $generalPosition;
	public $generalVariation;

	public $power; 					# sum of general ranking of the players
	public $powerPosition;
	public $powerVariation;

	public $domination; 			# population dominated
	public $dominationPosition;
	public $dominationVariation;

	public function getId() { return $this->id; }
}
