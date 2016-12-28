<?php

# int player 		id du joueur (facultatif)
# string message 	message à envoyer

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\ConversationMessage;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$parser = $this->getContainer()->get('parser');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$conversationManager = $this->getContainer()->get('hermes.conversation_manager');
$conversationMessageManager = $this->getContainer()->get('hermes.conversation_message_manager');

# protection des inputs
$content = $parser->parse($request->request->get('message'));

if ($content !== FALSE) {
	$S_PAM1 = $playerManager->getCurrentSession();
	$playerManager->newSession(FALSE);
	$playerManager->load(array('id' => $session->get('playerId')));

	if ($playerManager->size() == 1) {
		if ($playerManager->get()->status > Player::PARLIAMENT) {
			$senderID = $playerManager->get()->id;
			$senderColor = $playerManager->get()->rColor;

			if ($content !== '' && strlen($content) < 25000) {
				$playerManager->newSession(FALSE);
				$playerManager->load(
					['statement' => Player::DEAD, 'rColor' => $senderColor],
					['id', 'ASC'],
					[0, 1]
				);

				if ($playerManager->size() == 1) {
					$S_CVM = $conversationManager->getCurrentSession();
					$conversationManager->newSession();
					$conversationManager->load(
						['cu.rPlayer' => $playerManager->get()->id]
					);

					if ($conversationManager->size() == 1) {
						$conv = $conversationManager->get();

						$conv->messages++;
						$conv->dLastMessage = Utils::now();

						# désarchiver tout les users
						$users = $conv->players;
						foreach ($users as $user) {
							$user->convStatement = ConversationUser::CS_DISPLAY;
						}

						# création du message
						$message = new ConversationMessage();

						$message->rConversation = $conv->id;
						$message->rPlayer = $senderID;
						$message->type = ConversationMessage::TY_STD;
						$message->content = $content;
						$message->dCreation = Utils::now();
						$message->dLastModification = NULL;

						$conversationMessageManager->add($message);
					} else {
						throw new ErrorException('La conversation n\'existe pas ou ne vous appartient pas.');
					}
					
					$conversationManager->changeSession($S_CVM);
				}
			} else {
				throw new FormException('Le message est vide ou trop long');
			}
		} else {
			throw new FormException('Vizs n\'avez pas les droits pour poster un message officiel');
		}
	} else {
		throw new FormException('Ce joueur n\'existe pas');
	}

	$playerManager->changeSession($S_PAM1);
} else {
	throw new FormException('Pas assez d\'informations pour écrire un message officiel');
}