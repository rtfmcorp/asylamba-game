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

	public $power; 					# sum of player rankings
	public $powerPosition;
	public $powerVariation;

	public $domination; 			# sectors/population
	public $dominationPosition;
	public $dominationVariation;

	public function getId() { return $this->id; }
}
