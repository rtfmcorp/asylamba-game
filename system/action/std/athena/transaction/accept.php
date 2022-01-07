<?php
# accept a transaction action

# int rplace 		id de la base orbitale
# int rtransaction 	id de la transaction

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Hermes\Model\Notification;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$transactionManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\TransactionManager::class);
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$commercialShippingManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialShippingManager::class);
$commercialTaxManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialTaxManager::class);
$placeManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\PlaceManager::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);
$notificationManager = $this->getContainer()->get(\Asylamba\Modules\Hermes\Manager\NotificationManager::class);
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

$rPlace = $request->query->get('rplace');
$rTransaction = $request->query->get('rtransaction');

if ($rPlace !== FALSE AND $rTransaction !== FALSE AND in_array($rPlace, $verif)) {
	$transaction = $transactionManager->get($rTransaction);

	$commercialShipping = $commercialShippingManager->getByTransactionId($rTransaction);

	if ($transaction !== null AND $commercialShipping !== null AND $transaction->statement == Transaction::ST_PROPOSED) {
		$base = $orbitalBaseManager->get($rPlace);

		$exportTax = 0;
		$importTax = 0;

		#compute total price
		$S_CTM1 = $commercialTaxManager->getCurrentSession();
		$commercialTaxManager->newSession();
		$commercialTaxManager->load(array());

		for ($i = 0; $i < $commercialTaxManager->size(); $i++) { 
			$comTax = $commercialTaxManager->get($i);

			if ($comTax->faction == $transaction->sectorColor AND $comTax->relatedFaction == $base->sectorColor) {
				$exportTax = $comTax->exportTax;
			}
			if ($comTax->faction == $base->sectorColor AND $comTax->relatedFaction == $transaction->sectorColor) {
				$importTax = $comTax->importTax;
			}
		}

		$exportTax = round($transaction->price * $exportTax / 100);
		$importTax = round($transaction->price * $importTax / 100);

		$totalPrice = $transaction->price + $exportTax + $importTax;

		if ($session->get('playerInfo')->get('credit') >= $totalPrice) {
			# chargement des joueurs
			$buyer = $playerManager->get($session->get('playerId'));
			$seller = $playerManager->get($transaction->rPlayer);

			if ($buyer !== null && $seller !== null) {
				# transfert des crédits entre joueurs
				$playerManager->decreaseCredit($buyer, $totalPrice);
				$playerManager->increaseCredit($seller, $transaction->price);

				# transfert des crédits aux alliances
				if ($transaction->sectorColor != 0) {
					$exportFaction = $colorManager->get($transaction->sectorColor);
					$exportFaction->increaseCredit($exportTax);
				}

				if ($base->sectorColor != 0) {
					$importFaction = $colorManager->get($base->sectorColor);
					$importFaction->increaseCredit($importTax);
				}

				# gain d'expérience
				$experience = $transaction->getExperienceEarned();
				$playerManager->increaseExperience($seller, $experience);

				# load places to compute travel time
				$startPlace = $placeManager->get($commercialShipping->rBase);
				$destinationPlace = $placeManager->get($rPlace);
				$timeToTravel = Game::getTimeToTravelCommercial($startPlace, $destinationPlace);
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
				$transaction->currentRate = Game::calculateCurrentRate($transactionManager->getExchangeRate($transaction->type), $transaction->type, $transaction->quantity, $transaction->identifier, $transaction->price);
				
				# notif pour le proposeur
				$n = new Notification();
				$n->setRPlayer($transaction->rPlayer);
				$n->setTitle('Transaction validée');
				$n->addBeg()->addLnk('embassy/player-' . $session->get('playerId'), $session->get('playerInfo')->get('name'));
				$n->addTxt(' a accepté une de vos propositions dans le marché. Des vaisseaux commerciaux viennent de partir de votre ');
				$n->addLnk('map/place-' . $commercialShipping->rBase, 'base')->addTxt(' et se dirigent vers ');
				$n->addLnk('map/place-' . $base->getRPlace(), $base->getName())->addTxt(' pour acheminer la marchandise. ');
				$n->addSep()->addTxt('Vous gagnez ' . Format::numberFormat($transaction->price) . ' crédits et ' . Format::numberFormat($experience) . ' points d\'expérience.');
				$n->addSep()->addLnk('action/a-switchbase/base-' . $commercialShipping->rBase . '/page-sell', 'En savoir plus ?');
				$n->addEnd();
				$notificationManager->add($n);

				if (true === $this->getContainer()->getParameter('data_analysis')) {
					$qr = $database->prepare('INSERT INTO 
						DA_CommercialRelation(`from`, `to`, type, weight, dAction)
						VALUES(?, ?, ?, ?, ?)'
					);
					$qr->execute([$transaction->rPlayer, $session->get('playerId'), $transaction->type, DataAnalysis::creditToStdUnit($transaction->price), Utils::now()]);
				}

				$session->addFlashbag('Proposition acceptée. Les vaisseaux commerciaux sont en route vers votre base orbitale.', Flashbag::TYPE_MARKET_SUCCESS);
			} else {
				throw new ErrorException('erreur dans les propositions sur le marché, joueur inexistant');
			}
		} else {
			throw new ErrorException('vous n\'avez pas assez de crédits pour accepter cette proposition');
		}
		$commercialTaxManager->changeSession($S_CTM1);
	} else {
		throw new ErrorException('erreur dans les propositions sur le marché');
	}
} else {
	throw new FormException('pas assez d\'informations pour accepter une proposition sur le marché');
}
$entityManager->flush();
