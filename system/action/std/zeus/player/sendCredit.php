<?php
include_once ZEUS;
# give credit action

# int name 			destination player name
# int quantity 		quantity of credit to send
# [string text] 	facultative text

$name = Utils::getHTTPData('name');
$quantity = Utils::getHTTPData('quantity');
$text = Utils::getHTTPData('text');

// input protection
$p = new Parser();
$name = $p->protect($name);
$text = $p->parse($text);

if ($name !== FALSE AND $quantity !== FALSE AND strlen($text) < 500) {
	$credit = intval($quantity);

	if ($credit > 0) {

		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(ASM_UMODE);
		ASM::$pam->load(array('name' => $name));
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
				$n->addTxt(' vous a envoyé des crédits');
				if ($text !== '') {
					$n->addTxt(' avec le message suivant : ')->addBrk()->addTxt('"' . $text . '"');
				} else {
					$n->addTxt('.');
				}
				$n->addBoxResource('credit', Format::numberFormat($credit), 'crédits reçus');
				$n->addEnd();
				ASM::$ntm->add($n);

				CTR::$alert->add('Crédits envoyés', ALERT_STD_SUCCESS);
					
			} else {
				CTR::$alert->add('envoi de crédits impossible - vous ne pouvez pas envoyer plus que ce que vous possédez', ALERT_STD_ERROR);
			}
		} else {
			if (ASM::$pam->size() == 1) {
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
