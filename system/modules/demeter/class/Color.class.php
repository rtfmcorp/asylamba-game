<?php

/**
 * Message Forum
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/

class Color {
	const ALIVE 			= 1;
	const DEAD 				= 0;

	const MANDATE		 	= 1;
	const CAMPAIGN		 	= 2;
	const ELECTION 			= 3;

	public $id 					= 0;
	public $alive 				= 0;
	public $credits				= 0;
	public $players 			= 0;
	public $activePlayers 		= 0;
	public $points				= 0;
	public $sectors				= 0;
	public $electionStatement	= 0;
	public $dLastElection		= '';

	public function getId() { return $this->id; }


	public function uElection(){}
}