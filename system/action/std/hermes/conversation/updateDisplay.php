<?php
include_once HERMES;

$conversation 	= Utils::getHTTPData('conversation');

if ($conversation !== FALSE) {
	# vérifier que c'est l'utilisateur courant

	$S_CVM = ASM::$cvm->getCurrentSession();
	ASM::$cvm->newSession();
	ASM::$cvm->load(
		array('c.id' => $conversation, 'cu.rPlayer' => CTR::$data->get('playerId'))
	);

	if (ASM::$cvm->size() == 1) {
		$conv  = ASM::$cvm->get();
		$users = $conv->players;

		foreach ($users as $user) {
			if ($user->rPlayer == CTR::$data->get('playerId')) {
				if ($user->convStatement == ConversationUser::CS_DISPLAY) {
					$user->convStatement = ConversationUser::CS_ARCHIVED;
					CTR::$alert->add('La conversation a été archivée.', ALERT_STD_SUCCESS);
				} else {
					$user->convStatement = ConversationUser::CS_DISPLAY;
					CTR::$alert->add('La conversation a été désarchivée.', ALERT_STD_SUCCESS);
				}
				break;
			}
		}

	} else {
		CTR::$alert->add('La conversation n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
	}

	ASM::$cvm->changeSession($S_CVM);
} else {
	CTR::$alert->add('Informations manquantes pour quitter la conversation.', ALERT_STD_ERROR);
}