<?php

use App\Modules\Zeus\Model\CreditTransaction;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Zeus\Model\Player;
use App\Classes\Library\Format;
use App\Classes\Library\Utils;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;
use App\Classes\Library\Flashbag;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);
$creditTransactionManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\CreditTransactionManager::class);
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);
$mediaPath = $this->getContainer()->getParameter('media');

# give credit from faction to player action

# int name 			destination player name
# int quantity 		quantity of credit to send
# [string text] 	facultative text

$name = $request->request->get('name');
$quantity = $request->request->get('quantity');
$text = $request->request->get('text');

// input protection
$p = $this->getContainer()->get(\Asylamba\Classes\Library\Parser::class);
$name = $p->protect($name);
$text = $p->parse($text);

if ($name !== FALSE AND $quantity !== FALSE) {
	if ($session->get('playerInfo')->get('status') == Player::TREASURER) {
		if (strlen($text) < 500) {
			$credit = intval($quantity);

			if ($credit > 0) {
				if (($faction = $colorManager->get($session->get('playerInfo')->get('color'))) !== null) {
					if (($receiver = $playerManager->getByName($name)) !== null) {
						if ($faction->credits >= $credit) {
							$faction->decreaseCredit($credit);
							$playerManager->increaseCredit($receiver, $credit);

							# create the transaction
							$ct = new CreditTransaction();
							$ct->rSender = $session->get('playerInfo')->get('color');
							$ct->type = CreditTransaction::TYP_F_TO_P;
							$ct->rReceiver = $receiver->id;
							$ct->amount = $credit;
							$ct->dTransaction = Utils::now();
							$ct->comment = $text;
							$creditTransactionManager->add($ct);

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
							$n->addBoxResource('credit', Format::numberFormat($credit), ($credit == 1 ? 'crédit reçu' : 'crédits reçus'), $mediaPath);
							$n->addEnd();
							$notificationManager->add($n);
							$session->addFlashbag('Crédits envoyés', Flashbag::TYPE_SUCCESS);
							$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush();
						} else {
							throw new ErrorException('envoi de crédits impossible - vous ne pouvez pas envoyer plus que ce que vous possédez');
						}
					} else {
						throw new ErrorException('envoi de crédits impossible - erreur dans les joueurs');
					}
				} else {
					throw new ErrorException('envoi de crédits impossible - erreur dans la faction');
				}
			} else {
				throw new ErrorException('envoi de crédits impossible - il faut envoyer un nombre entier positif');
			}
		} else {
			throw new FormException('le texte ne doit pas dépasser les 500 caractères');
		}
	} else {
		throw new ErrorException('Seul le responsable financier de votre faction peut faire cette action.');
	}
} else {
	throw new FormException('pas assez d\'informations pour envoyer des crédits');
}
