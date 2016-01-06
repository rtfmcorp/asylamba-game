<?php
include_once ATHENA;
include_once ZEUS;
# cancel a transaction action

# int rtransaction 		id de la transaction

$rTransaction = Utils::getHTTPData('rtransaction');

if ($rTransaction !== FALSE) {

	$S_TRM1 = ASM::$trm->getCurrentSession();
	ASM::$trm->newSession();
	ASM::$trm->load(array('id' => $rTransaction));
	$transaction = ASM::$trm->get();

	$S_CSM1 = ASM::$csm->getCurrentSession();
	ASM::$csm->newSession();
	ASM::$csm->load(array('rTransaction' => $rTransaction));
	$commercialShipping = ASM::$csm->get();

	if (ASM::$trm->size() == 1 AND ASM::$csm->size() == 1 AND $transaction->statement == Transaction::ST_PROPOSED AND $transaction->rPlayer == CTR::$data->get('playerId')) {

		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $transaction->rPlace));
		$base = ASM::$obm->get();

		if (CTR::$data->get('playerInfo')->get('credit') >= $transaction->getPriceToCancelOffer()) {

			# chargement du joueur
			$S_PAM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession(ASM_UMODE);
			ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

			if (ASM::$pam->size() == 1) {

				$valid = TRUE;

				switch ($transaction->type) {
					case Transaction::TYP_RESOURCE :
						$maxStorage = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $base->getLevelStorage(), 'storageSpace');
						$storageBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
						if ($storageBonus > 0) {
							$maxStorage += ($maxStorage * $storageBonus / 100);
						}
						$storageSpace = $maxStorage - $base->getResourcesStorage();

						if ($storageSpace >= $transaction->quantity) {
							$base->increaseResources($transaction->quantity, TRUE);
						} else {
							$valid = FALSE;
							CTR::$alert->add('Vous n\'avez pas assez de place dans votre Stockage pour stocker les ressources. Videz un peu le hangar et revenez plus tard pour annuler cette offre.', ALERT_STD_INFO);
						}
						break;
					case Transaction::TYP_SHIP :
						$base->addShipToDock($transaction->identifier, $transaction->quantity);
						break;
					case Transaction::TYP_COMMANDER :
						include_once ARES;
						$S_COM1 = ASM::$com->getCurrentSession();
						ASM::$com->newSession(ASM_UMODE);
						ASM::$com->load(array('c.id' => $transaction->identifier));
						$commander = ASM::$com->get();
						$commander->setStatement(Commander::RESERVE);
						ASM::$com->changeSession($S_COM1);
						break;
					default :
						$valid = FALSE;
				}

				if ($valid) {
					// débit des crédits au joueur
					ASM::$pam->get()->decreaseCredit($transaction->getPriceToCancelOffer());

					// annulation de l'envoi commercial (libération des vaisseaux de commerce)
					ASM::$csm->deleteById($commercialShipping->id);

					// update transaction statement
					$transaction->statement = Transaction::ST_CANCELED;
					$transaction->dValidation = Utils::now();

					switch ($transaction->type) {
						case Transaction::TYP_RESOURCE :
							CTR::$alert->add('Annulation de la proposition commerciale. Les vaisseaux commerciaux sont à nouveau disponibles et vous récupérez vos ressources.', ALERT_GAM_MARKET);
							break;
						case Transaction::TYP_SHIP :
							CTR::$alert->add('Annulation de la proposition commerciale. Les vaisseaux commerciaux sont à nouveau disponibles et vous récupérez vos vaisseaux de combat.', ALERT_GAM_MARKET);
							break;
						case Transaction::TYP_COMMANDER :
							CTR::$alert->add('Annulation de la proposition commerciale. Les vaisseaux commerciaux sont à nouveau disponibles et votre commandant est placé à l\'école de commandement.', ALERT_GAM_MARKET);
							break;
					}
				}
			} else {
				CTR::$alert->add('erreur dans l\'annulation de proposition sur le marché, joueur inexistant', ALERT_STD_ERROR);
			}
			ASM::$pam->changeSession($S_PAM1);
		} else {
			CTR::$alert->add('vous n\'avez pas assez de crédits pour annuler la proposition', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('impossible d\'annuler une proposition sur le marché', ALERT_STD_ERROR);
	}
	ASM::$trm->changeSession($S_TRM1);
	ASM::$csm->changeSession($S_CSM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour annuler une proposition sur le marché', ALERT_STD_FILLFORM);
}
?>