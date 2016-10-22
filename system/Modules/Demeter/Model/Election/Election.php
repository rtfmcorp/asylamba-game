<?php

/**
 * election Forum
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
namespace Asylamba\Modules\Demeter\Model\Election;

class Election {
	public $id 					= 0;
	public $rColor 				= 0;
	public $dElection			= 0;

	public function getId() { return $this->id; }
}