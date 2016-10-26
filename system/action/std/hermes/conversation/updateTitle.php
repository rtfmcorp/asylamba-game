<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Parser;
use Asylamba\Modules\Hermes\Model\ConversationUser;

$conversation 	= Utils::getHTTPData('conversation');
$title 			= Utils::getHTTPData('title');

$title 			= (new Parser())->parse($title);

if ($conversation !== FALSE) {
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
		if (strlen($title) < 255) {
			$conv = ASM::$cvm->get()->title = $title;
		} else {
			CTR::$alert->add('Le titre est trop long.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('La conversation n\'existe pas ou ne vous appartient pas.', ALERT_STD_ERROR);
	}

	ASM::$cvm->changeSession($S_CVM);
} else {
	CTR::$alert->add('Informations manquantes pour ajouter un joueur à la conversation.', ALERT_STD_ERROR);
}