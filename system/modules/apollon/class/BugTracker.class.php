<?php

/**
 * BugTracker
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Apollon
 * @update 15.07.13
*/

class BugTracker {
	// CONSTANTS
	const TYPE_BUG = 0;
	const TYPE_ORTHOGRAPH = 1;
	const TYPE_DISPLAY = 2;
	const TYPE_CALIBRATION = 3;
	const TYPE_IMPROVEMENT = 4;
	// state
	const ST_WAITING = 0;
	const ST_ARCHIVED = 1;
	const ST_DELETED = 2;

	// ATTRIBUTES
	public $id;
	public $url;
	public $rPlayer;
	public $bindKey;
	public $type;
	public $dSending;
	public $message;
	public $statement;

	public function getId() { return $this->id; }
}
?>