<?php
include_once ZEUS;
include_once HERMES;

$conversation 	= Utils::getHTTPData('conversation');
$recipients 	= Utils::getHTTPData('recipients');

if ($recipients !== FALSE AND $conversation !== FALSE) {
	$S_CVM = ASM::$cvm->getCurrentSession();
	ASM::$cvm->newSession();
	ASM::$cvm->load(
		array(
			'c.id' => $conversation,
			'cu.rPlayer' => CTR::$data->get('playerId'),
			'cu.playerStatement' => ConversationUser::US_ADMIN
		)
	);

	if (ASM::$cvm->size() == 1) {
		$conv  = ASM::$cvm->get();
		$players = $conv->players;

		$playersId = array();
		foreach ($players as $player) {
			$playersId[] = $player->rPlayer;
		}

		# traitement des utilisateurs multiples
		$recipients = explode(',', $recipients);
		$recipients = array_filter($recipients, function($e) {
			return $e == CTR::$data->get('playerId') ? FALSE : TRUE;
		});
		$recipients[] = 0;

		if ((count($recipients) + count($players)) <= ConversationUser::MAX_USERS) {
			# chargement des utilisateurs
			$S_PAM = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession();
			ASM::$pam->load(array('id' => $recipients, 'statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

			if (ASM::$pam->size() >= 1) {
				# création de la date précédente
				$readingDate = date('Y-m-d H:i:s', (strtotime(Utils::now()) - 20));

				# créer la liste des users
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					if (!in_array(ASM::$pam->get($i)->id, $playersId)) {
						$user = new ConversationUser();

						$user->rConversation = $conv->id;
						$user->rPlayer = ASM::$pam->get($i)->id;
						$user->convPlayerStatement = ConversationUser::US_STANDARD;
						$user->convStatement = ConversationUser::CS_DISPLAY;
						$user->dLastView = $readingDate;

						ASM::$cum->add($user);

						# création du message système
						$message = new ConversationMessage();

						$message->rConversation = $conv->id;
						$message->rPlayer = ASM::$pam->get($i)->id;
						$message->type = ConversationMessage::TY_SYSTEM;
						$message->content = ASM::$pam->get($i)->name . ' est entré dans la conversation';
						$message->dCreation = Utils::now();
						$message->dLastModification = NULL;

						ASM::$cme->add($message);

						# mise à jour de la conversation
						$conv->messages++;
						$conv->dLastMessage = Utils::now();
					}
				}

				CTR::$alert->add('Le joueur a été ajouté.', ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('Le joueur n\'est pas joignable.', ALERT_STD_ERROR);		
			}

			ASM::$pam->changeSession($S_PAM);
		} else {
			CTR::$alert->add('Nombre maximum de joueur atteint.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('La conversation n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
	}

	ASM::$cvm->changeSession($S_CVM);
} else {
	CTR::$alert->add('Informations manquantes pour ajouter un joueur à la conversation.', ALERT_STD_ERROR);
}