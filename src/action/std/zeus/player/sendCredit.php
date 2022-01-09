<?php

use App\Classes\Worker\CTR;
use App\Classes\Worker\ASM;
use App\Classes\Library\Utils;
use App\Classes\Library\Format;
use App\Classes\Library\DataAnalysis;
use App\Modules\Zeus\Model\CreditTransaction;
use App\Modules\Hermes\Model\Notification;
use App\Classes\Library\Flashbag;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

# give credit action

# int name 			destination player name
# int quantity 		quantity of credit to send
# [string text] 	facultative text

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$creditTransactionManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\CreditTransactionManager::class);
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);
$parser = $this->getContainer()->get(\Asylamba\Classes\Library\Parser::class);
$mediaPath = $this->getContainer()->get('media');

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
			$receiver = $playerManager->getByName($name);
			$sender = $playerManager->get($session->get('playerId'));

			if ($receiver !== null && $sender !== null) {
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
					$n->addBoxResource('credit', Format::numberFormat($credit), ($credit == 1 ? 'crédit reçu' : 'crédits reçus'), $mediaPath);
					$n->addEnd();
					$notificationManager->add($n);

					if (true === $this->getContainer()->getParameter('data_analysis')) {
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
				if ($sender !== null) {
					throw new ErrorException('envoi de crédits impossible - aucun intérêt d\'envoyer des crédits à vous-même !?');
				} else {
					throw new ErrorException('envoi de crédits impossible - erreur dans les joueurs');
				}
			}
		} else {
			throw new ErrorException('envoi de crédits impossible - il faut envoyer un nombre entier positif');
		}
	} else {
		throw new FormException('le texte ne doit pas dépasser les 500 caractères');
	}
} else {
	throw new FormException('pas assez d\'informations pour envoyer des crédits');
}
