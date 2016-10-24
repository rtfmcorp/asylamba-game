<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Parser;
use Asylamba\Modules\Zeus\Model\CreditTransaction;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Classes\Library\Format;

# give credit from faction to player action

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

if ($name !== FALSE AND $quantity !== FALSE) {
	if (CTR::$data->get('playerInfo')->get('status') == PAM_TREASURER) {
		if (strlen($text) < 500) {
			$credit = intval($quantity);

			if ($credit > 0) {

				$S_PAM1 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession(ASM_UMODE);
				ASM::$pam->load(array('name' => $name));

				$S_CLM1 = ASM::$clm->getCurrentSession();
				ASM::$clm->newSession();
				ASM::$clm->load(array('id' => CTR::$data->get('playerInfo')->get('color')));
				if (ASM::$clm->size() == 1) {
					if (ASM::$pam->size() == 1) {
						$receiver = ASM::$pam->get();
						$faction = ASM::$clm->get();

						if ($faction->credits >= $credit) {
							$faction->decreaseCredit($credit);
							$receiver->increaseCredit($credit);

							# create the transaction
							$ct = new CreditTransaction();
							$ct->rSender = CTR::$data->get('playerInfo')->get('color');
							$ct->type = CreditTransaction::TYP_F_TO_P;
							$ct->rReceiver = $receiver->id;
							$ct->amount = $credit;
							$ct->dTransaction = Utils::now();
							$ct->comment = $text;
							ASM::$crt->add($ct);
									
							$n = new Notification();
							$n->setRPlayer($receiver->id);
							$n->setTitle('Réception de crédits');
							$n->addBeg();
							$n->addTxt('Votre faction vous a envoyé des crédits');
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
						CTR::$alert->add('envoi de crédits impossible - erreur dans les joueurs', ALERT_STD_ERROR);
					}
				} else {
					CTR::$alert->add('envoi de crédits impossible - erreur dans la faction', ALERT_STD_ERROR);
				}
				ASM::$pam->changeSession($S_PAM1);
			} else {
				CTR::$alert->add('envoi de crédits impossible - il faut envoyer un nombre entier positif', ALERT_STD_ERROR);
			}
		} else {
			CTR::$alert->add('le texte ne doit pas dépasser les 500 caractères', ALERT_STD_FILLFORM);
		}
	} else {
		CTR::$alert->add('Seul le responsable financier de votre faction peut faire cette action.', ALERT_STD_ERROR);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour envoyer des crédits', ALERT_STD_FILLFORM);
}
