<?php

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;

$id 		= Utils::getHTTPData('id');

if ($id !== FALSE) {
	$_TOM = ASM::$tom->getCurrentsession();
	ASM::$tom->newSession();
	ASM::$tom->load(array('id' => $id));

	if (ASM::$tom->size() == 1) {
		if (in_array(CTR::$data->get('playerInfo')->get('status'), [Player::CHIEF, Player::WARLORD, Player::TREASURER, Player::MINISTER])) {
			$topic = ASM::$tom->get();

			if ($topic->isUp) {
				$topic->isUp = FALSE;
			} else {
				$topic->isUp = TRUE;
			}
		} else {
			CTR::$alert->add('Vous ne disposez pas des droits nécessaires pour cette action.', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('Le sujet demandé n\'existe pas.', ALERT_STD_ERROR);
	}
	
	CTR::redirect('faction/view-forum/forum-' . ASM::$tom->get()->rForum . '/topic-' . ASM::$tom->get()->id . '/sftr-2');
	ASM::$tom->changeSession($_TOM);
} else {
	CTR::$alert->add('Manque d\'information.', ALERT_STD_ERROR);
}