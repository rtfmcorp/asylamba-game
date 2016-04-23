<?php
include_once ZEUS;
include_once HERMES;

$conversation 	= Utils::getHTTPData('conversation');
$content 		= Utils::getHTTPData('content');

$p = new Parser();
$content = $p->parse($content);

if ($conversation !== FALSE AND $content !== FALSE) {
	if (strlen($content) < 10000) {
		$S_CVM = ASM::$cvm->getCurrentSession();
		ASM::$cvm->newSession();
		ASM::$cvm->load(
			array('c.id' => $conversation, 'cu.rPlayer' => CTR::$data->get('playerId'))
		);

		if (ASM::$cvm->size() == 1) {
			$conv = ASM::$cvm->get();

			if ($conv->type != Conversation::TY_SYSTEM) {
				$DA_recipient;

				$conv->messages++;
				$conv->dLastMessage = Utils::now();

				# désarchiver tout les users
				$users = $conv->players;
				foreach ($users as $user) {
					$user->convStatement = ConversationUser::CS_DISPLAY;

					if ($user->rPlayer == CTR::$data->get('playerId')) {
						$user->dLastView = Utils::now();
					} else {
						$DA_recipient = $user->rPlayer;
					}
				}

				# création du message
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
					$qr->execute([CTR::$data->get('playerId'), $DA_recipient, 2, $content, Utils::now()]);
				}
			}
		} else {
			CTR::$alert->add('La conversation n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
		}
		
		ASM::$cvm->changeSession($S_CVM);
	} else {
		CTR::$alert->add('Le message est trop long.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('Informations manquantes pour démarrer une nouvelle conversation.', ALERT_STD_ERROR);
}