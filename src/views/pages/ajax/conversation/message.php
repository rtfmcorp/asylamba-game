<?php

use App\Classes\Library\Utils;
use App\Modules\Hermes\Model\ConversationMessage;

$container = $this->getContainer();
$componentPath = $container->getParameter('component');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$conversationManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationManager::class);
$conversationMessageManager = $container->get(\App\Modules\Hermes\Manager\ConversationMessageManager::class);
$conversationUserManager = $container->get(\App\Modules\Hermes\Manager\ConversationUserManager::class);

$page = $request->query->has('page') 
	? $request->query->get('page')
	: 1;

# chargement d'une conversation
$conversationManager->newSession();
$conversationManager->load(
	['c.id' => $request->query->get('conversation'), 'cu.rPlayer' => $session->get('playerId')]
);

if ($conversationManager->size() == 1) {
	# chargement des infos d'une conversation
	$conversationUserManager->newSession();
	$conversationUserManager->load(['c.rConversation' => $request->query->get('conversation')]);

	# mis à jour de l'heure de la dernière vue
	for ($i = 0; $i < $conversationUserManager->size(); $i++) { 
		if ($conversationUserManager->get($i)->rPlayer == $session->get('playerId')) {
			$dPlayerLastMessage = $conversationUserManager->get($i)->dLastView;
			$currentUser = $conversationUserManager->get($i);

			$conversationUserManager->get($i)->dLastView = Utils::now();
		}
	}

	# chargement des messages
	$conversationMessageManager->newSession();
	$conversationMessageManager->load(
		['c.rConversation' => $request->query->get('conversation')],
		['c.dCreation', 'DESC'],
		[($page - 1) * ConversationMessage::MESSAGE_BY_PAGE, ConversationMessage::MESSAGE_BY_PAGE]
	);

	$message_listmode = TRUE;

	include $componentPath . 'conversation/messages.php';
}
