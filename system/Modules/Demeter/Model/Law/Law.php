<?php

/**
 * loi
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 29.09.14
*/
namespace Asylamba\Modules\Demeter\Model\Law;

use Asylamba\Classes\Worker\ASM;

class Law {
	const VOTATION					= 0;
	const EFFECTIVE					= 1;
	const OBSOLETE					= 2;
	const REFUSED					= 3;

	const SECTORTAX					= 1;
	const SECTORNAME				= 2;
	const COMTAXEXPORT				= 3;
	const COMTAXIMPORT				= 4;
	const MILITARYSUBVENTION		= 5;
	const TECHNOLOGYTRANSFER		= 6;
	const PEACEPACT					= 7;
	const WARDECLARATION			= 8;
	const TOTALALLIANCE				= 9;
	const NEUTRALPACT				= 10;
	const PUNITION					= 11;


	const VOTEDURATION 				= 86400;

	public $id					= 0;
	public $rColor				= 0;
	public $type 				= '';
	public $options 			= array();
	public $statement 			= 0;
	public $dEndVotation		= '';
	public $dEnd 	 			= '';
	public $dCreation 			= '';

	public $forVote 			= 0;
	public $againstVote			= 0;

	public function getId() { return $this->id; }
}