<?php

namespace App\Modules\Hermes\Model;

class ConversationUser {
	# constante
	const MAX_USERS				= 25;

	const US_ADMIN 				= 1;
	const US_STANDARD 			= 2;

	const CS_DISPLAY			= 1;
	const CS_ARCHIVED			= 2;

	public $id 					= 0;
	public $rConversation 		= 0;
	public $rPlayer				= 0;

	public $convPlayerStatement	= 0;
	public $convStatement		= 0;
	public $dLastView			= '';

	public $playerColor			= 0;
	public $playerName			= '';
	public $playerAvatar		= '';
	public $playerStatus		= 0;

	public function getId() { return $this->id; }

	public static function getPlayerStatement($statement) {
		switch ($statement) {
			case self::US_ADMIN: return 'gestionnaire'; break;
			case self::US_STANDARD: return 'normal'; break;
			default: return 'status inconnu'; break;
		}
	}
}
