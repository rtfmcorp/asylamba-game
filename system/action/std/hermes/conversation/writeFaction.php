<?php

# int player 		id du joueur (facultatif)
# string message 	message à envoyer

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Parser;
use Asylamba\Modules\Hermes\Model\ConversationUser;
use Asylamba\Modules\Hermes\Model\ConversationMessage;

$content = Utils::getHTTPData('message');

# protection des inputs
$p = new Parser();
$content = $p->parse($content);

if ($content !== FALSE) {
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession(FALSE);
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	if (ASM::$pam->size() == 1) {
		if (ASM::$pam->get()->status > PAM_PARLIAMENT) {
			$senderID = ASM::$pam->get()->id;
			$senderColor = ASM::$pam->get()->rColor;

			if ($content !== '' && strlen($content) < 25000) {
				ASM::$pam->newSession(FALSE);
				ASM::$pam->load(
					['statement' => PAM_DEAD, 'rColor' => $senderColor],
					['id', 'ASC'],
					[0, 1]
				);

				if (ASM::$pam->size() == 1) {
					$S_CVM = ASM::$cvm->getCurrentSession();
					ASM::$cvm->newSession();
					ASM::$cvm->load(
						['cu.rPlayer' => ASM::$pam->get()->id]
					);

					if (ASM::$cvm->size() == 1) {
						$conv = ASM::$cvm->get();

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

						ASM::$cme->add($message);
					} else {
						CTR::$alert->add('La conversation n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
					}
					
					ASM::$cvm->changeSession($S_CVM);
				}
			} else {
				CTR::$alert->add('Le message est vide ou trop long', ALERT_STD_FILLFORM);
			}
		} else {
			CTR::$alert->add('Vizs n\'avez pas les droits pour poster un message officiel', ALERT_STD_FILLFORM);
		}
	} else {
		CTR::$alert->add('Ce joueur n\'existe pas', ALERT_STD_FILLFORM);
	}

	ASM::$pam->changeSession($S_PAM1);
} else {
	CTR::$alert->add('Pas assez d\'informations pour écrire un message officiel', ALERT_STD_FILLFORM);
}