<?php

use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\Conversation;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');

# liste des conv's
$display = 
	($request->query->get('mode') === ConversationUser::CS_ARCHIVED)
	? ConversationUser::CS_ARCHIVED
	: ConversationUser::CS_DISPLAY
;

$page = $request->query->has('page') 
	? $request->query->get('page')
	: 1;

# chargement de toutes les conversations
$conversationManager->newSession();
$conversationManager->load(
	['cu.rPlayer' => $session->get('playerId'), 'cu.convStatement' => $display],
	['c.dLastMessage', 'DESC'],
	[($page - 1) * Conversation::CONVERSATION_BY_PAGE, Conversation::CONVERSATION_BY_PAGE]
);

$conversation_listmode = TRUE;

include COMPONENT . 'conversation/list.php';