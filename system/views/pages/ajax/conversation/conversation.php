<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Hermes\Model\ConversationUser;

# liste des conv's
$display = CTR::$get->equal('mode', ConversationUser::CS_ARCHIVED)
	? ConversationUser::CS_ARCHIVED
	: ConversationUser::CS_DISPLAY;

$page = CTR::$get->exist('page') 
	? CTR::$get->get('page')
	: 1;

# chargement de toutes les conversations
ASM::$cvm->newSession();
ASM::$cvm->load(
	['cu.rPlayer' => CTR::$data->get('playerId'), 'cu.convStatement' => $display],
	['c.dLastMessage', 'DESC'],
	[($page - 1) * Conversation::CONVERSATION_BY_PAGE, Conversation::CONVERSATION_BY_PAGE]
);

$conversation_listmode = TRUE;

include COMPONENT . 'conversation/list.php';