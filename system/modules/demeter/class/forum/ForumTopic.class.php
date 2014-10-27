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

class ForumTopic {
	public $id 				= 0;
	public $title			= '';
	public $rPlayer 		= 0;
	public $rForum			= 0;
	public $rColor	 		= 0;
	public $isUP	 		= 0;
	public $isClosed		= 0;
	public $isArchived	 	= 0;
	public $dCreation		= '';
	public $statement		= 1;
	public $dLastMessage	= '';

	# si joueur renseigné lors du chargement
	public $lastView		= NULL;
	public $nbMessage		= 0;

	public function getId() { return $this->id; }

	public function updateLastView($playerId) {
		if ($this->lastView == NULL) {
			$db = DataBase::getInstance();

			$qr = $db->prepare('INSERT INTO forumLastView 
				SET
					rPlayer = ?,
					rTopic = ?,
					dView = ?');
			$aw = $qr->execute(array(
					$playerId,
					$this->id,
					Utils::now()
				)
			);
		} else {
			$db = DataBase::getInstance();

			$qr = $db->prepare('UPDATE forumLastView
				SET
					dView = ?
				WHERE rPlayer = ? AND rTopic = ?');
			$aw = $qr->execute(array(
				Utils::now(),
				$playerId,
				$this->id
			));
		}
	}
}