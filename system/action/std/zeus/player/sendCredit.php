<?php
include_once ATHENA;
include_once GAIA;
# give credit action

# int player 		destination player id
# int quantity 		quantity of credit to send


$player = Utils::getHTTPData('player');
$quantity = Utils::getHTTPData('quantity');

if ($player !== FALSE AND $quantity !== FALSE) {

	$credit = intval($quantity);

	if ($credit > 0) {

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(ASM_UMODE);
		ASM::$pam->load(array('id' => $player));
		ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

		if (ASM::$pam->size() == 2) {
			$receiver = ASM::$pam->get();
			$sender = ASM::$pam->get(1);

			if ($sender->credit >= $credit) {
				$sender->decreaseCredit($credit);
				$receiver->increaseCredit($credit);
						
				$n = new Notification();
				$n->setRPlayer($receiver->id);
				$n->setTitle('Réception de crédits');
				$n->addBeg();
				$n->addLnk('diary/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'));
				$n->addTxt(' vous a envoyé des crédits.');
				$n->addBoxResource('credit', Format::numberFormat($credit), 'crédits reçus');
				$n->addEnd();
				ASM::$ntm->add($n);

				CTR::$alert->add('Crédits envoyés', ALERT_STD_SUCCESS);
					
			} else {
				CTR::$alert->add('envoi de crédits impossible - vous ne pouvez pas envoyer plus que ce que vous possédez', ALERT_STD_ERROR);
			}
		} else {
			if ($player == CTR::$data->get('playerId')) {
				CTR::$alert->add('envoi de crédits impossible - aucun intérêt d\'envoyer des crédits à vous-même !?', ALERT_STD_ERROR);
			} else {
				CTR::$alert->add('envoi de crédits impossible - erreur dans les joueurs', ALERT_STD_ERROR);
			}
		}
		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::$alert->add('envoi de crédits impossible - il faut envoyer un nombre positif', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour envoyer des crédits', ALERT_STD_FILLFORM);
}
