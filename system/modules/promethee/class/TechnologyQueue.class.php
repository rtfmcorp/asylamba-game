<?php

/**
 * Technology Queue
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 25.03.13
*/

class TechnologyQueue {
	// ATTRIBUTES
	public $id 				= 0;
	public $rPlayer			= 0;
	public $rPlace		 	= 0;
	public $technology 		= 0;
	public $targetLevel		= 0;
	public $remainingTime 	= 0; // en secondes
	public $position 		= 0;

	public function getId() {
		return $this->id;
	}
}
?>