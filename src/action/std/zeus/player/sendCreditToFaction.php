<?php
# give credit action

# int quantity 		quantity of credit to send

use App\Modules\Zeus\Model\CreditTransaction;
use App\Classes\Library\Flashbag;
use App\Classes\Library\Utils;
use App\Classes\Exception\ErrorException;
use App\Classes\Exception\FormException;

$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);
$creditTransactionManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\CreditTransactionManager::class);
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$quantity = $request->request->get('quantity');

if ($quantity !== FALSE) {
	$credit = intval($quantity);

	if ($credit > 0) {
		if (($sender = $playerManager->get($session->get('playerId'))) !== null) {
			if ($sender->rColor > 0) {
				if ($sender->credit >= $credit) {
					if (($faction = $colorManager->get($sender->rColor)) !== null) {
						# make the transaction
						$playerManager->decreaseCredit($sender, $credit);
						$faction->increaseCredit($credit);

						# create the transaction
						$ct = new CreditTransaction();
						$ct->rSender = $session->get('playerId');
						$ct->type = CreditTransaction::TYP_FACTION;
						$ct->rReceiver = $sender->rColor;
						$ct->amount = $credit;
						$ct->dTransaction = Utils::now();
						$ct->comment = NULL;
						$creditTransactionManager->add($ct);

						$session->addFlashbag('Crédits envoyés', Flashbag::TYPE_SUCCESS);
						$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush();
					} else {
						throw new ErrorException('envoi de crédits impossible - faction introuvable');
					}	
				} else {
					throw new ErrorException('envoi de crédits impossible - vous ne pouvez pas envoyer plus que ce que vous possédez');
				}
			} else {
				throw new ErrorException('envoi de crédits impossible - vous n\'avez pas de faction');
			}
		} else {
			throw new ErrorException('envoi de crédits impossible - erreur dans le joueur');
		}
	} else {
		throw new ErrorException('envoi de crédits impossible - il faut envoyer un nombre entier positif');
	}
} else {
	throw new FormException('pas assez d\'informations pour envoyer des crédits');
}
