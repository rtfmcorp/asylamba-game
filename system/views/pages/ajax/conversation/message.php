<?php
# inclusion des modules
include_once HERMES;
include_once DEMETER;

$page = CTR::$get->exist('page') 
	? CTR::$get->get('page')
	: 1;

# chargement d'une conversation
ASM::$cvm->newSession();
ASM::$cvm->load(
	['c.id' => CTR::$get->get('conversation'), 'cu.rPlayer' => CTR::$data->get('playerId')]
);

if (ASM::$cvm->size() == 1) {
	# chargement des infos d'une conversation
	ASM::$cum->newSession();
	ASM::$cum->load(['c.rConversation' => CTR::$get->get('conversation')]);

	# mis à jour de l'heure de la dernière vue
	for ($i = 0; $i < ASM::$cum->size(); $i++) { 
		if (ASM::$cum->get($i)->rPlayer == CTR::$data->get('playerId')) {
			$dPlayerLastMessage = ASM::$cum->get($i)->dLastView;
			$currentUser = ASM::$cum->get($i);

			ASM::$cum->get($i)->dLastView = Utils::now();
		}
	}

	# chargement des messages
	ASM::$cme->newSession();
	ASM::$cme->load(
		['c.rConversation' => CTR::$get->get('conversation')],
		['c.dCreation', 'DESC'],
		[($page - 1) * ConversationMessage::MESSAGE_BY_PAGE, ConversationMessage::MESSAGE_BY_PAGE]
	);

	$message_listmode = TRUE;

	include COMPONENT . 'conversation/messages.php';
}