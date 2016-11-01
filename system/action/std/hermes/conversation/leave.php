<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Hermes\Model\Conversation;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\ConversationMessage;

$conversation 	= Utils::getHTTPData('conversation');

if ($conversation !== FALSE) {
	# vérifier que c'est l'utilisateur courant

	$S_CVM = ASM::$cvm->getCurrentSession();
	ASM::$cvm->newSession();
	ASM::$cvm->load(
		array('c.id' => $conversation, 'cu.rPlayer' => CTR::$data->get('playerId'))
	);

	if (ASM::$cvm->size() == 1) {
		# vérifier qu'il y a plus de 2 personnes

		$conv  = ASM::$cvm->get();

		if ($conv->type != Conversation::TY_SYSTEM) {
			$players = $conv->players;

			if (count($players) > 2) {
				$admin = 0;
				$playerConv = NULL;

				foreach ($players as $player) {
					if ($player->rPlayer == CTR::$data->get('playerId')) {
						$playerConv = $player;
					} elseif ($player->convPlayerStatement == ConversationUser::US_ADMIN) {
						$admin++;
					}
				}

				# vérifier qu'il y a encore un admin
				if ($admin < 1) {
					foreach ($players as $player) {
						if ($player->rPlayer != CTR::$data->get('playerId')) {
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

				ASM::$cme->add($message);

				# suppresion de l'utilisateur
				ASM::$cum->deleteById($playerConv->id);

				CTR::redirect('message');
			} else {
				CTR::$alert->add('Impossible de quitter une conversation entre deux personnes.', ALERT_STD_ERROR);
			}
		}
	} else {
		CTR::$alert->add('La conversation n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
	}

	ASM::$cvm->changeSession($S_CVM);
} else {
	CTR::$alert->add('Informations manquantes pour quitter la conversation.', ALERT_STD_ERROR);
}