<?php
include_once ZEUS;
include_once HERMES;

$recipients 	= Utils::getHTTPData('recipients');
$content 		= Utils::getHTTPData('content');

$p = new Parser();
$content = $p->parse($content);

if ($recipients !== FALSE AND $content !== FALSE) {
	if (strlen($content) < 10000) {
		# traitement des utilisateurs multiples
		$recipients = explode(',', $recipients);
		$recipients = array_filter($recipients, function($e) {
			return $e == CTR::$data->get('playerId') ? FALSE : TRUE;
		});
		$recipients[] = 0;

		if (count($recipients) <= ConversationUser::MAX_USERS) {
			# chargement des utilisateurs
			$S_PAM = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession();
			# player statements
			ASM::$pam->load(array('id' => $recipients, 'statement' => array(PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY)));

			if (ASM::$pam->size() >= 1) {
				# création de la date précédente
				$readingDate = date('Y-m-d H:i:s', (strtotime(Utils::now()) - 20));

				# créer la conversation
				$conv = new Conversation();

				$conv->messages = 1;
				$conv->type = Conversation::TY_USER;
				$conv->dCreation = Utils::now();
				$conv->dLastMessage = Utils::now();

				ASM::$cvm->add($conv);

				# créer le user créateur de la conversation
				$user = new ConversationUser();

				$user->rConversation = $conv->id;
				$user->rPlayer = CTR::$data->get('playerId');
				$user->convPlayerStatement = ConversationUser::US_ADMIN;
				$user->convStatement = ConversationUser::CS_DISPLAY;
				$user->dLastView = Utils::now();

				ASM::$cum->add($user);

				# créer la liste des users
				for ($i = 0; $i < ASM::$pam->size(); $i++) {
					$user = new ConversationUser();

					$user->rConversation = $conv->id;
					$user->rPlayer = ASM::$pam->get($i)->id;
					$user->convPlayerStatement = ConversationUser::US_STANDARD;
					$user->convStatement = ConversationUser::CS_DISPLAY;
					$user->dLastView = $readingDate;

					ASM::$cum->add($user);
				}

				# créer le premier message
				$message = new ConversationMessage();

				$message->rConversation = $conv->id;
				$message->rPlayer = CTR::$data->get('playerId');
				$message->type = ConversationMessage::TY_STD;
				$message->content = $content;
				$message->dCreation = Utils::now();
				$message->dLastModification = NULL;

				ASM::$cme->add($message);

				if (DATA_ANALYSIS) {
					$db = DataBase::getInstance();
					$qr = $db->prepare('INSERT INTO 
						DA_SocialRelation(`from`, `to`, `type`, `message`, dAction)
						VALUES(?, ?, ?, ?, ?)'
					);
					$qr->execute([CTR::$data->get('playerId'), ASM::$pam->get(0)->id, 2, $content, Utils::now()]);
				}

				CTR::$alert->add('La conversation a été créée.', ALERT_STD_SUCCESS);
				CTR::redirect('message/conversation-' . $conv->id);
			} else {
				CTR::$alert->add('Le joueur n\'est pas joignable.', ALERT_STD_ERROR);		
			}

			ASM::$pam->changeSession($S_PAM);
		} else {
			CTR::$alert->add('Nombre maximum de joueur atteint.', ALERT_STD_ERROR);
		}

	} else {
		CTR::$alert->add('Le message est trop long.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Informations manquantes pour démarrer une nouvelle conversation.', ALERT_STD_ERROR);
}