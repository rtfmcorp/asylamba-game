<?php

/**
 * Vote Forum
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
namespace App\Modules\Demeter\Model\Election;

class Vote {
	public $id 					= 0;
	public $rCandidate 			= 0;
	public $rPlayer				= 0;
	public $rElection			= 0;
	public $dVotation			= '';

	public function getId() { return $this->id; }
}
