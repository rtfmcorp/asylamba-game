<?php

/**
 * Technology Queue
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 10.02.14
*/

class TechnologyQueue {
	// ATTRIBUTES
	public $id;
	public $rPlayer;
	public $rPlace;
	public $technology;
	public $targetLevel;
	public $dStart;
	public $dEnd;

	public function getId() {
		return $this->id;
	}
}
?>