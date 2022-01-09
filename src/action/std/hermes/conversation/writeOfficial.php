<?php

use App\Classes\Library\Utils;
use App\Classes\Exception\ErrorException;
use App\Modules\Hermes\Model\ConversationMessage;
use App\Modules\Hermes\Model\ConversationUser;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$parser = $this->getContainer()->get(\Asylamba\Classes\Library\Parser::class);
$conversationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationManager::class);
$conversationMessageManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\ConversationMessageManager::class);

$content = $parser->parse($request->request->get('message'));

if ($session->get('playerInfo')->get('admin') == FALSE) {
	$response->redirect('profil');
} else {
	if ($content !== FALSE) {
		if (strlen($content) < 10000) {
			$S_CVM = $conversationManager->getCurrentSession();
			$conversationManager->newSession();
			$conversationManager->load(
				['cu.rPlayer' => ID_JEANMI]
			);

			if ($conversationManager->size() == 1) {
				$conv = $conversationManager->get();

				$conv->messages++;
				$conv->dLastMessage = Utils::now();

				# désarchiver tous les users
				$users = $conv->players;
				foreach ($users as $user) {
					$user->convStatement = ConversationUser::CS_DISPLAY;
				}

				# création du message
				$message = new ConversationMessage();

				$message->rConversation = $conv->id;
				$message->rPlayer = ID_JEANMI;
				$message->type = ConversationMessage::TY_STD;
				$message->content = $content;
				$message->dCreation = Utils::now();
				$message->dLastModification = NULL;

				$conversationMessageManager->add($message);
			} else {
				throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
			}
			
			$conversationManager->changeSession($S_CVM);
		} else {
			throw new ErrorException('Le message est trop long.');
		}
	} else {
		throw new ErrorException('Informations manquantes pour démarrer une nouvelle conversation.');
	}
}