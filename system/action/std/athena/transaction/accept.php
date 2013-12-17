<?php
include_once ATHENA;
include_once ZEUS;
include_once GAIA;
# accept a transaction action

# int rplace 		id de la base orbitale
# int rtransaction 	id de la transaction

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
}

$rPlace = Utils::getHTTPData('rplace');
$rTransaction = Utils::getHTTPData('rtransaction');

if ($rPlace !== FALSE AND $rTransaction !== FALSE AND in_array($rPlace, $verif)) {

	$S_TRM1 = ASM::$trm->getCurrentSession();
	ASM::$trm->newSession();
	ASM::$trm->load(array('id' => $rTransaction));
	$transaction = ASM::$trm->get();

	$S_CSM1 = ASM::$csm->getCurrentSession();
	ASM::$csm->newSession();
	ASM::$csm->load(array('rTransaction' => $rTransaction));
	$commercialShipping = ASM::$csm->get();

	if (ASM::$trm->size() == 1 AND ASM::$csm->size() == 1 AND $transaction->statement == Transaction::ST_PROPOSED) {

		$S_OBM1 = ASM::$obm->getCurrentSession();
		ASM::$obm->newSession(ASM_UMODE);
		ASM::$obm->load(array('rPlace' => $rPlace));
		$base = ASM::$obm->get();

		if (CTR::$data->get('playerInfo')->get('credit') >= $transaction->price) {

			# chargement des joueurs
			$S_PAM1 = ASM::$pam->getCurrentSession();
			ASM::$pam->newSession(ASM_UMODE);
			ASM::$pam->load(array('id' => CTR::$data->get('playerId')));
			ASM::$pam->load(array('id' => $transaction->rPlayer));

			if (ASM::$pam->size() == 2) {

				# transfert des crédits entre joueurs
				ASM::$pam->get(0)->decreaseCredit($transaction->price);
				ASM::$pam->get(1)->increaseCredit($transaction->price);

				# gain d'expérience
				$experience = $transaction->getExperienceEarned();
				ASM::$pam->get(1)->increaseExperience($experience);

				# load places to compute travel time
				$S_PLM1 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession(ASM_UMODE);
				ASM::$plm->load(array('id' => $commercialShipping->rBase));
				ASM::$plm->load(array('id' => $rPlace));
				$timeToTravel = Game::getTimeToTravel(ASM::$plm->get(0), ASM::$plm->get(1));
				$departure = Utils::now();
				$arrival = Utils::addSecondsToDate($departure, $timeToTravel);

				# update commercialShipping
				$commercialShipping->rBaseDestination = $rPlace;
				$commercialShipping->dDeparture = $departure;
				$commercialShipping->dArrival = $arrival;
				$commercialShipping->statement = CommercialShipping::ST_GOING;

				# update transaction statement
				$transaction->statement = Transaction::ST_COMPLETED;
				$transaction->dValidation = Utils::now();
				# update exchange rate
				$transaction->currentRate = Game::calculateCurrentRate(ASM::$trm->getExchangeRate($transaction->type), $transaction->type, $transaction->quantity, $transaction->identifier, $transaction->price);

				# notif pour le proposeur
				$n = new Notification();
				$n->setRPlayer($transaction->rPlayer);
				$n->setTitle('Transaction validée');
				$n->addBeg()->addLnk('diary/player-' . CTR::$data->get('playerId'), CTR::$data->get('playerInfo')->get('name'));
				$n->addTxt(' a accepté une de vos propositions dans le marché. Des vaisseaux commerciaux viennent de partir de votre ');
				$n->addLnk('map/base-' . $commercialShipping->rBase, 'base')->addTxt(' et se dirigent vers ');
				$n->addLnk('map/place-' . $base->getRPlace(), $base->getName())->addTxt(' pour acheminer la marchandise. ');
				$n->addSep()->addTxt('Vous gagnez ' . Format::numberFormat($transaction->price) . ' crédits et ' . Format::numberFormat($experience) . ' points d\'expérience.');
				$n->addSep()->addLnk('bases/base-' . $commercialShipping->rBase . '/view-commercialplateforme/mode-market', 'En savoir plus ?');
				$n->addEnd();
				ASM::$ntm->add($n);

				CTR::$alert->add('Proposition acceptée. Les vaisseaux commerciaux sont en route vers votre base orbitale.', ALERT_STD_SUCCESS);
			} else {
				CTR::$alert->add('erreur dans les propositions sur le marché, joueur inexistant', ALERT_STD_ERROR);
			}
			ASM::$pam->changeSession($S_PAM1);
		} else {
			CTR::$alert->add('vous n\'avez pas assez de crédits pour accepter cette proposition', ALERT_STD_ERROR);
		}
		ASM::$obm->changeSession($S_OBM1);
	} else {
		CTR::$alert->add('erreur dans les propositions sur le marché', ALERT_STD_ERROR);
	}
	ASM::$trm->changeSession($S_TRM1);
	ASM::$csm->changeSession($S_CSM1);
} else {
	CTR::$alert->add('pas assez d\'informations pour accepter une proposition sur le marché', ALERT_STD_FILLFORM);
}
?>