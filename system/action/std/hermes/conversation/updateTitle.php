<?php

use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$parser = $this->getContainer()->get('parser');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');

$conversation 	= $request->query->get('conversation');
$title 			= $request->request->get('title');

$title 			= $parser->parse($title);

if ($conversation !== FALSE) {
	$S_CVM = $conversationManager->getCurrentSession();
	$conversationManager->newSession();
	$conversationManager->load(
		array(
			'c.id' => $conversation,
			'cu.rPlayer' => $session->get('playerId'),
			'cu.playerStatement' => ConversationUser::US_ADMIN
		)
	);

	if ($conversationManager->size() == 1) {
		if (strlen($title) < 255) {
			$conv = $conversationManager->get()->title = $title;
		} else {
			throw new ErrorException('Le titre est trop long.');
		}
	} else {
		throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
	}

	$conversationManager->changeSession($S_CVM);
} else {
	throw new ErrorException('Informations manquantes pour ajouter un joueur à la conversation.');
}