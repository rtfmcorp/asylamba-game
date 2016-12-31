<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\ConversationMessage;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$conversationMessageManager = $this->getContainer()->get('hermes.conversation_message_manager');
$conversationUserManager = $this->getContainer()->get('hermes.conversation_user_manager');
$playerManager = $this->getContainer()->get('hermes.player_manager');

$conversation 	= $request->query->add('conversation');
$recipients 	= $request->query->add('recipients');

if ($recipients !== FALSE AND $conversation !== FALSE) {
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
		$conv  = $conversationManager->get();
		$players = $conv->players;

		$playersId = array();
		foreach ($players as $player) {
			$playersId[] = $player->rPlayer;
		}

		# traitement des utilisateurs multiples
		$recipients = explode(',', $recipients);
		$recipients = array_filter($recipients, function($e) {
			return $e == $session->get('playerId') ? FALSE : TRUE;
		});
		$recipients[] = 0;

		if ((count($recipients) + count($players)) <= ConversationUser::MAX_USERS) {
			# chargement des utilisateurs
			$S_PAM = $playerManager->getCurrentSession();
			$playerManager->newSession();
			$playerManager->load(array('id' => $recipients, 'statement' => array(Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY)));

			if ($playerManager->size() >= 1) {
				# création de la date précédente
				$readingDate = date('Y-m-d H:i:s', (strtotime(Utils::now()) - 20));

				# créer la liste des users
				for ($i = 0; $i < $playerManager->size(); $i++) {
					if (!in_array($playerManager->get($i)->id, $playersId)) {
						$user = new ConversationUser();

						$user->rConversation = $conv->id;
						$user->rPlayer = $playerManager->get($i)->id;
						$user->convPlayerStatement = ConversationUser::US_STANDARD;
						$user->convStatement = ConversationUser::CS_DISPLAY;
						$user->dLastView = $readingDate;

						$conversationUserManager->add($user);

						# création du message système
						$message = new ConversationMessage();

						$message->rConversation = $conv->id;
						$message->rPlayer = $playerManager->get($i)->id;
						$message->type = ConversationMessage::TY_SYSTEM;
						$message->content = $playerManager->get($i)->name . ' est entré dans la conversation';
						$message->dCreation = Utils::now();
						$message->dLastModification = NULL;

						$conversationMessageManager->add($message);

						# mise à jour de la conversation
						$conv->messages++;
						$conv->dLastMessage = Utils::now();
					}
				}

				$response->flashbag->add('Le joueur a été ajouté.', Response::FLASHBAG_SUCCESS);
			} else {
				throw new ErrorException('Le joueur n\'est pas joignable.');		
			}

			$playerManager->changeSession($S_PAM);
		} else {
			throw new ErrorException('Nombre maximum de joueur atteint.');
		}
	} else {
		throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
	}

	$conversationManager->changeSession($S_CVM);
} else {
	throw new ErrorException('Informations manquantes pour ajouter un joueur à la conversation.');
}