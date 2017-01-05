<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Modules\Zeus\Model\CreditTransaction;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;

# give credit action

# int name 			destination player name
# int quantity 		quantity of credit to send
# [string text] 	facultative text

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$creditTransactionManager = $this->getContainer()->get('zeus.credit_transaction_manager');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$parser = $this->getContainer()->get('parser');

$name = $request->request->get('name');
$quantity = $request->request->get('quantity');
$text = $request->request->get('text');

// input protection
$name = $parser->protect($name);
$text = $parser->parse($text);

if ($name !== FALSE AND $quantity !== FALSE) {
	if (strlen($text) < 500) {
		$credit = intval($quantity);

		if ($credit > 0) {

			$S_PAM1 = $playerManager->getCurrentSession();
			$playerManager->newSession(ASM_UMODE);
			$playerManager->load(array('name' => $name));
			$playerManager->load(array('id' => $session->get('playerId')));

			if ($playerManager->size() == 2) {
				$receiver = $playerManager->get();
				$sender = $playerManager->get(1);

				if ($sender->credit >= $credit) {
					$playerManager->decreaseCredit($sender, $credit);
					$playerManager->increaseCredit($receiver, $credit);

					# create the transaction
					$ct = new CreditTransaction();
					$ct->rSender = $session->get('playerId');
					$ct->type = CreditTransaction::TYP_PLAYER;
					$ct->rReceiver = $receiver->id;
					$ct->amount = $credit;
					$ct->dTransaction = Utils::now();
					$ct->comment = $text;
					$creditTransactionManager->add($ct);
							
					$n = new Notification();
					$n->setRPlayer($receiver->id);
					$n->setTitle('Réception de crédits');
					$n->addBeg();
					$n->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'));
					$n->addTxt(' vous a envoyé des crédits');
					if ($text !== '') {
						$n->addTxt(' avec le message suivant : ')->addBrk()->addTxt('"' . $text . '"');
					} else {
						$n->addTxt('.');
					}
					$n->addBoxResource('credit', Format::numberFormat($credit), ($credit == 1 ? 'crédit reçu' : 'crédits reçus'));
					$n->addEnd();
					$notificationManager->add($n);

					if (DATA_ANALYSIS) {
						$qr = $database->prepare('INSERT INTO 
							DA_CommercialRelation(`from`, `to`, type, weight, dAction)
							VALUES(?, ?, ?, ?, ?)'
						);
						$qr->execute([$ct->rSender, $ct->rReceiver, 5, DataAnalysis::creditToStdUnit($ct->amount), Utils::now()]);
					}

					$session->addFlashbag('Crédits envoyés', Flashbag::TYPE_SUCCESS);
						
				} else {
					throw new ErrorException('envoi de crédits impossible - vous ne pouvez pas envoyer plus que ce que vous possédez');
				}
			} else {
				if ($playerManager->size() == 1) {
					throw new ErrorException('envoi de crédits impossible - aucun intérêt d\'envoyer des crédits à vous-même !?');
				} else {
					throw new ErrorException('envoi de crédits impossible - erreur dans les joueurs');
				}
			}
			$playerManager->changeSession($S_PAM1);
		} else {
			throw new ErrorException('envoi de crédits impossible - il faut envoyer un nombre entier positif');
		}
	} else {
		throw new FormException('le texte ne doit pas dépasser les 500 caractères');
	}
} else {
	throw new FormException('pas assez d\'informations pour envoyer des crédits');
}
