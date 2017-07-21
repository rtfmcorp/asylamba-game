<?php

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Hermes\Model\ConversationUser;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');

$conversation 	= $request->query->get('conversation');

if ($conversation !== FALSE) {
	# vérifier que c'est l'utilisateur courant

	$S_CVM = $conversationManager->getCurrentSession();
	$conversationManager->newSession();
	$conversationManager->load(
		array('c.id' => $conversation, 'cu.rPlayer' => $session->get('playerId'))
	);
	
	if ($conversationManager->size() == 1) {
		$conv  = $conversationManager->get();
		$users = $conv->players;

		foreach ($users as $user) {
			if ($user->rPlayer == $session->get('playerId')) {
				if ($user->convStatement == ConversationUser::CS_DISPLAY) {
					$user->convStatement = ConversationUser::CS_ARCHIVED;
					$session->addFlashbag('La conversation a été archivée.', Flashbag::TYPE_SUCCESS);
				} else {
					$user->convStatement = ConversationUser::CS_DISPLAY;
					$session->addFlashbag('La conversation a été désarchivée.', Flashbag::TYPE_SUCCESS);
				}
				break;
			}
		}

	} else {
		throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
	}

	$conversationManager->changeSession($S_CVM);
} else {
	throw new ErrorException('Informations manquantes pour quitter la conversation.');
}