<?php

use App\Classes\Library\Utils;
use App\Modules\Hermes\Model\Conversation;
use App\Modules\Hermes\Model\ConversationUser;
use App\Modules\Hermes\Model\ConversationMessage;
use App\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$conversationManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationManager::class);
$conversationUserManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationUserManager::class);
$conversationMessageManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationMessageManager::class);

$conversation 	= $request->query->get('conversation');

if ($conversation !== FALSE) {
	# vérifier que c'est l'utilisateur courant

	$S_CVM = $conversationManager->getCurrentSession();
	$conversationManager->newSession();
	$conversationManager->load(
		array('c.id' => $conversation, 'cu.rPlayer' => $session->get('playerId'))
	);

	if ($conversationManager->size() == 1) {
		# vérifier qu'il y a plus de 2 personnes

		$conv  = $conversationManager->get();

		if ($conv->type != Conversation::TY_SYSTEM) {
			$players = $conv->players;

			if (count($players) > 2) {
				$admin = 0;
				$playerConv = NULL;

				foreach ($players as $player) {
					if ($player->rPlayer == $session->get('playerId')) {
						$playerConv = $player;
					} elseif ($player->convPlayerStatement == ConversationUser::US_ADMIN) {
						$admin++;
					}
				}

				# vérifier qu'il y a encore un admin
				if ($admin < 1) {
					foreach ($players as $player) {
						if ($player->rPlayer != $session->get('playerId')) {
							$player->convPlayerStatement = ConversationUser::US_ADMIN;
							break;
						}
					}
				}

				# mise à jour de la conversation
				$conv->messages++;
				$conv->dLastMessage = Utils::now();

				# création du message système
				$message = new ConversationMessage();

				$message->rConversation = $conv->id;
				$message->rPlayer = $playerConv->rPlayer;
				$message->type = ConversationMessage::TY_SYSTEM;
				$message->content = $playerConv->playerName . ' a quitté la conversation';
				$message->dCreation = Utils::now();
				$message->dLastModification = NULL;

				$conversationMessageManager->add($message);

				# suppresion de l'utilisateur
				$conversationUserManager->deleteById($playerConv->id);

				$this->getContainer()->get('app.response')->redirect('message');
			} else {
				throw new ErrorException('Impossible de quitter une conversation entre deux personnes.');
			}
		}
	} else {
		throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
	}

	$conversationManager->changeSession($S_CVM);
} else {
	throw new ErrorException('Informations manquantes pour quitter la conversation.');
}
