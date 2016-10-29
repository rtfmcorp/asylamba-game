<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Parser;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Zeus\Model\CreditTransaction;
use Asylamba\Modules\Hermes\Model\Notification;

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

if ($name !== FALSE AND $quantity !== FALSE) {
	if (strlen($text) < 500) {
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

					# create the transaction
					$ct = new CreditTransaction();
					$ct->rSender = CTR::$data->get('playerId');
					$ct->type = CreditTransaction::TYP_PLAYER;
					$ct->rReceiver = $receiver->id;
					$ct->amount = $credit;
					$ct->dTransaction = Utils::now();
					$ct->comment = $text;
					ASM::$crt->add($ct);
							
					$n = new Notification();
					$n->setRPlayer($receiver->id);
					$n->setTitle('Réception de crédits');
					$n->addBeg();
					$n->addLnk('embassy/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'));
					$n->addTxt(' vous a envoyé des crédits');
					if ($text !== '') {
						$n->addTxt(' avec le message suivant : ')->addBrk()->addTxt('"' . $text . '"');
					} else {
						$n->addTxt('.');
					}
					$n->addBoxResource('credit', Format::numberFormat($credit), 'crédits reçus');
					$n->addEnd();
					ASM::$ntm->add($n);

					if (DATA_ANALYSIS) {
						$db = Database::getInstance();
						$qr = $db->prepare('INSERT INTO 
							DA_CommercialRelation(`from`, `to`, type, weight, dAction)
							VALUES(?, ?, ?, ?, ?)'
						);
						$qr->execute([$ct->rSender, $ct->rReceiver, 5, DataAnalysis::creditToStdUnit($ct->amount), Utils::now()]);
					}

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
			CTR::$alert->add('envoi de crédits impossible - il faut envoyer un nombre entier positif', ALERT_STD_ERROR);
		}
	} else {
		CTR::$alert->add('le texte ne doit pas dépasser les 500 caractères', ALERT_STD_FILLFORM);
	}
} else {
	CTR::$alert->add('pas assez d\'informations pour envoyer des crédits', ALERT_STD_FILLFORM);
}
