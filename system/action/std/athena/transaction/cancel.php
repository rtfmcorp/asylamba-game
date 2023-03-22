<?php
# cancel a transaction action

# int rtransaction 		id de la transaction

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$transactionManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\TransactionManager::class);
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);
$commercialShippingManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialShippingManager::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);

$rTransaction = $request->query->get('rtransaction');

if ($rTransaction !== FALSE) {
	$transaction = $transactionManager->get($rTransaction);

	$commercialShipping = $commercialShippingManager->getByTransactionId($rTransaction);

	if ($transaction !== null AND $commercialShipping !== null AND $transaction->statement == Transaction::ST_PROPOSED AND $transaction->rPlayer == $session->get('playerId')) {
		$base = $orbitalBaseManager->get($transaction->rPlace);

		if ($session->get('playerInfo')->get('credit') >= $transaction->getPriceToCancelOffer()) {
			if (($player = $playerManager->get($session->get('playerId'))) !== null) {

				$valid = TRUE;

				switch ($transaction->type) {
					case Transaction::TYP_RESOURCE :
						$maxStorage = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $base->getLevelStorage(), 'storageSpace');
						$storageBonus = $session->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
						if ($storageBonus > 0) {
							$maxStorage += ($maxStorage * $storageBonus / 100);
						}
						$storageSpace = $maxStorage - $base->getResourcesStorage();

						if ($storageSpace >= $transaction->quantity) {
							$orbitalBaseManager->increaseResources($base, $transaction->quantity, TRUE);
						} else {
							$valid = FALSE;
							throw new ErrorException('Vous n\'avez pas assez de place dans votre Stockage pour stocker les ressources. Videz un peu le hangar et revenez plus tard pour annuler cette offre.');
						}
						break;
					case Transaction::TYP_SHIP :
						$orbitalBaseManager->addShipToDock($base, $transaction->identifier, $transaction->quantity);
						break;
					case Transaction::TYP_COMMANDER :
						$commander = $commanderManager->get($transaction->identifier);
						$commander->setStatement(Commander::RESERVE);
						break;
					default :
						$valid = FALSE;
				}

				if ($valid) {
					// débit des crédits au joueur
					$playerManager->decreaseCredit($player, $transaction->getPriceToCancelOffer());

					// annulation de l'envoi commercial (libération des vaisseaux de commerce)
					$entityManager->remove($commercialShipping);

					// update transaction statement
					$transaction->statement = Transaction::ST_CANCELED;
					$transaction->dValidation = Utils::now();

					switch ($transaction->type) {
						case Transaction::TYP_RESOURCE :
							$session->addFlashbag('Annulation de la proposition commerciale. Les vaisseaux commerciaux sont à nouveau disponibles et vous récupérez vos ressources.', Flashbag::TYPE_MARKET_SUCCESS);
							break;
						case Transaction::TYP_SHIP :
							$session->addFlashbag('Annulation de la proposition commerciale. Les vaisseaux commerciaux sont à nouveau disponibles et vous récupérez vos vaisseaux de combat.', Flashbag::TYPE_MARKET_SUCCESS);
							break;
						case Transaction::TYP_COMMANDER :
							$session->addFlashbag('Annulation de la proposition commerciale. Les vaisseaux commerciaux sont à nouveau disponibles et votre commandant est placé à l\'école de commandement.', Flashbag::TYPE_MARKET_SUCCESS);
							break;
					}
				}
			} else {
				throw new ErrorException('erreur dans l\'annulation de proposition sur le marché, joueur inexistant');
			}
		} else {
			throw new ErrorException('vous n\'avez pas assez de crédits pour annuler la proposition');
		}
	} else {
		throw new ErrorException('impossible d\'annuler une proposition sur le marché');
	}
} else {
	throw new FormException('pas assez d\'informations pour annuler une proposition sur le marché');
}
$entityManager->flush();
