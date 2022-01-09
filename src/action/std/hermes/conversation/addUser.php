<?php

use App\Classes\Library\Utils;
use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Modules\Zeus\Model\Player;
use App\Modules\Hermes\Model\ConversationUser;
use App\Modules\Hermes\Model\ConversationMessage;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$conversationManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationManager::class);
$conversationMessageManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationMessageManager::class);
$conversationUserManager = $this->getContainer()->get(\App\Modules\Hermes\Manager\ConversationUserManager::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);

$conversation 	= $request->query->get('conversation');
$recipients 	= $request->request->get('recipients');

if ($recipients !== FALSE AND $conversation !== FALSE) {
	$S_CVM = $conversationManager->getCurrentSession();
	$conversationManager->newSession();
	$conversationManager->load([
		'c.id' => $conversation,
		'cu.rPlayer' => $session->get('playerId'),
		'cu.playerStatement' => ConversationUser::US_ADMIN
	]);

	if ($conversationManager->size() == 1) {
		$conv  = $conversationManager->get();
		$players = $conv->players;

		$playersId = array();
		foreach ($players as $player) {
			$playersId[] = $player->rPlayer;
		}

		# traitement des utilisateurs multiples
		$recipients = explode(',', $recipients);
		$recipients = array_filter($recipients, function($e) use ($session) {
			return $e == $session->get('playerId') ? FALSE : TRUE;
		});
		$recipients[] = 0;

		if ((count($recipients) + count($players)) <= ConversationUser::MAX_USERS) {
			# chargement des utilisateurs
			$newPlayers = $playerManager->getByIdsAndStatements($recipients, [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]);
			if (count($newPlayers) >= 1) {
				# création de la date précédente
				$readingDate = date('Y-m-d H:i:s', (strtotime(Utils::now()) - 20));

				# créer la liste des users
				foreach ($newPlayers as $newPlayer) {
					if (!in_array($newPlayer->id, $playersId)) {
						$user = new ConversationUser();

						$user->rConversation = $conv->id;
						$user->rPlayer = $newPlayer->id;
						$user->convPlayerStatement = ConversationUser::US_STANDARD;
						$user->convStatement = ConversationUser::CS_DISPLAY;
						$user->dLastView = $readingDate;

						$conversationUserManager->add($user);

						# création du message système
						$message = new ConversationMessage();

						$message->rConversation = $conv->id;
						$message->rPlayer = $newPlayer->id;
						$message->type = ConversationMessage::TY_SYSTEM;
						$message->content = $newPlayer->name . ' est entré dans la conversation';
						$message->dCreation = Utils::now();
						$message->dLastModification = NULL;

						$conversationMessageManager->add($message);

						# mise à jour de la conversation
						$conv->messages++;
						$conv->dLastMessage = Utils::now();
					}
				}

				$session->addFlashbag('Le joueur a été ajouté.', Flashbag::TYPE_SUCCESS);
			} else {
				throw new ErrorException('Le joueur n\'est pas joignable.');		
			}
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
