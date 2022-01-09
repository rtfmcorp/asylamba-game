<?php

/**
 * Candidate Forum
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
namespace App\Modules\Demeter\Model\Election;

class Candidate {
	public $id					= 0;
	public $rElection 			= 0;
	public $rPlayer				= 0;
	public $name 				= '';
	public $avatar 				= '';
	public $factionPoint 		= 0;
	public $status 				= 0;
	public $chiefChoice			= 0;
	public $treasurerChoice		= 0;
	public $warlordChoice		= 0;
	public $ministerChoice		= 0;
	public $program				= '';
	public $dPresentation		= '';

	public function getId() { return $this->id; }
}
