<?php

/**
 * Topic Forum
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
namespace Asylamba\Modules\Demeter\Model\Forum;

class ForumTopic {
	public $id 				= 0;
	public $title			= '';
	public $rPlayer 		= 0;
	public $rForum			= 0;
	public $rColor	 		= 0;
	public $isUp	 		= 0;
	public $isClosed		= 0;
	public $isArchived	 	= 0;
	public $dCreation		= '';
	public $statement		= 1;
	public $dLastMessage	= '';

	# si joueur renseigné lors du chargement
	public $lastView		= NULL;
	public $nbMessage		= 0;

	public function getId() { return $this->id; }
}