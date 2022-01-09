<?php

namespace App\Modules\Athena\Handler\Trade;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\DateTimeConverter;
use App\Classes\Library\Utils;
use App\Modules\Ares\Manager\CommanderManager;
use App\Modules\Athena\Manager\CommercialShippingManager;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Manager\TransactionManager;
use App\Modules\Athena\Message\Trade\CommercialShippingMessage;
use App\Modules\Athena\Model\CommercialShipping;
use App\Modules\Athena\Model\Transaction;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Hermes\Model\Notification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CommercialShippingHandler implements MessageHandlerInterface
{
	public function __construct(
		protected CommercialShippingManager $commercialShippingManager,
		protected TransactionManager $transactionManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected CommanderManager $commanderManager,
		protected MessageBusInterface $messageBus,
		protected EntityManager $entityManager,
		protected NotificationManager $notificationManager,
	) {

	}

	public function __invoke(CommercialShippingMessage $message): void
	{
		$cs = $this->commercialShippingManager->get($message->getCommercialShippingId());
		$transaction = $this->transactionManager->get($cs->getTransactionId());
		$orbitalBase = $this->orbitalBaseManager->get($cs->getBaseId());
		$destOB = $this->orbitalBaseManager->get($cs->getDestinationBaseId());
		$commander =
			($transaction !== null && $transaction->type === Transaction::TYP_COMMANDER)
				? $this->commanderManager->get($transaction->identifier)
				: null
		;

		switch ($cs->statement) {
			case CommercialShipping::ST_GOING :
				# shipping arrived, delivery of items to rBaseDestination
				$this->commercialShippingManager->deliver($cs, $transaction, $destOB, $commander);
				# prepare commercialShipping for moving back
				$cs->statement = CommercialShipping::ST_MOVING_BACK;
				$timeToTravel = strtotime($cs->dArrival) - strtotime($cs->dDeparture);
				$cs->dDeparture = $cs->dArrival;
				$cs->dArrival = Utils::addSecondsToDate($cs->dArrival, $timeToTravel);

				$this->messageBus->dispatch(new CommercialShippingMessage($cs->getId()), [DateTimeConverter::to_delay_stamp($cs->getArrivedAt())]);
				break;
			case CommercialShipping::ST_MOVING_BACK :
				# shipping arrived, release of the commercial ships
				# send notification
				$n = new Notification();
				$n->setRPlayer($cs->rPlayer);
				$n->setTitle('Retour de livraison');
				if ($cs->shipQuantity == 1) {
					$n->addBeg()->addTxt('Votre vaisseau commercial est de retour sur votre ');
				} else {
					$n->addBeg()->addTxt('Vos vaisseaux commerciaux sont de retour sur votre ');
				}
				$n->addLnk('map/place-' . $cs->rBase, 'base orbitale')->addTxt(' après avoir livré du matériel sur une autre ');
				$n->addLnk('map/place-' . $cs->rBaseDestination, 'base')->addTxt(' . ');
				if ($cs->shipQuantity == 1) {
					$n->addSep()->addTxt('Votre vaisseau de commerce est à nouveau disponible pour faire d\'autres transactions ou routes commerciales.');
				} else {
					$n->addSep()->addTxt('Vos ' . $cs->shipQuantity . ' vaisseaux de commerce sont à nouveau disponibles pour faire d\'autres transactions ou routes commerciales.');
				}
				$n->addEnd();
				$this->notificationManager->add($n);
				# delete commercialShipping
				$this->entityManager->remove($cs);
				break;
			default :break;
		}
		$this->entityManager->flush();
	}
}
