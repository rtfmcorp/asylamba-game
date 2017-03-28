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

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$transactionManager = $this->getContainer()->get('athena.transaction_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$commercialShippingManager = $this->getContainer()->get('athena.commercial_shipping_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');

$rTransaction = $request->query->get('rtransaction');

if ($rTransaction !== FALSE) {
	$transaction = $transactionManager->get($rTransaction);

	$S_CSM1 = $commercialShippingManager->getCurrentSession();
	$commercialShippingManager->newSession();
	$commercialShippingManager->load(array('rTransaction' => $rTransaction));
	$commercialShipping = $commercialShippingManager->get();

	if ($transaction !== null AND $commercialShippingManager->size() == 1 AND $transaction->statement == Transaction::ST_PROPOSED AND $transaction->rPlayer == $session->get('playerId')) {
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
							throw new ErrorException('Vous n\'avez pas assez de place dans votre Stockage pour stocker les ressources. Videz un peu le hangar et revenez plus tard pour annuler cette offre.', ALERT_STD_INFO);
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
					$commercialShippingManager->deleteById($commercialShipping->id);

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
	$commercialShippingManager->changeSession($S_CSM1);
} else {
	throw new FormException('pas assez d\'informations pour annuler une proposition sur le marché');
}
