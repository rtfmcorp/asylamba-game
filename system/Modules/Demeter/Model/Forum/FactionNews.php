<?php

/**
 * news de faction
 *
 * @author Noé Zufferey
 * @copyright Asylamba
 *
 * @package Demeter
 * @update 09.01.15
*/
namespace Asylamba\Modules\Demeter\Model\Forum;

class FactionNews {
	const STANDARD 		= 0;
	const PINNED 		= 1;

	public $id 				= 0;
	public $rFaction		= 0;
	public $title 			= 0;
	public $oContent 		= 0;
	public $pContent 		= 0;
	public $pinned 			= 0;
	public $statement 		= 0;
	public $dCreation		= '';

	public function getId() { return $this->id; }
}