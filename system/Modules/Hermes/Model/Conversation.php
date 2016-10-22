<?php

namespace Asylamba\Modules\Hermes\Model;

class Conversation {
	const CONVERSATION_BY_PAGE	= 25;

	const TY_USER 				= 1;
	const TY_SYSTEM				= 2;

	public $id					= 0;
	public $title				= NULL;
	public $messages			= 0;
	public $type				= 1;
	public $dCreation			= '';
	public $dLastMessage		= NULL;

	public $players				= array();

	public function getId() { return $this->id; }

	public function getLastPage() {
		return ceil($this->messages / ConversationMessage::MESSAGE_BY_PAGE);
	}
}