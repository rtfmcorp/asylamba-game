<?php

/**
 * CommercialShippingManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @version 19.11.13
 **/
namespace App\Modules\Athena\Manager;

use App\Classes\Library\DateTimeConverter;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Message\Trade\CommercialShippingMessage;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Classes\Library\Utils;
use App\Classes\Entity\EntityManager;
use App\Modules\Athena\Model\CommercialShipping;
use App\Modules\Athena\Model\Transaction;
use App\Classes\Library\Format;
use App\Classes\Library\Session\SessionWrapper;
use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Hermes\Model\Notification;
use App\Modules\Ares\Resource\CommanderResources;
use App\Modules\Ares\Model\Commander;
use App\Classes\Exception\ErrorException;
use Symfony\Component\Messenger\MessageBusInterface;

class CommercialShippingManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected NotificationManager $notificationManager,
		protected MessageBusInterface $messageBus,
		protected SessionWrapper $sessionWrapper,
		protected string $mediaPath,
	) {
	}
	
	public function scheduleShippings()
	{
		$shippings = $this->entityManager->getRepository(CommercialShipping::class)->getMoving();

		/** @var CommercialShipping $commercialShipping */
		foreach ($shippings as $commercialShipping) {
			$this->messageBus->dispatch(
				new CommercialShippingMessage($commercialShipping->getId()),
				[DateTimeConverter::to_delay_stamp($commercialShipping->getArrivedAt())],
			);
		}
	}
	
	public function get(int $id): ?CommercialShipping
	{
		return $this->entityManager->getRepository(CommercialShipping::class)->get($id);
	}
	
	public function getByTransactionId(int $id): ?CommercialShipping
	{
		return $this->entityManager->getRepository(CommercialShipping::class)->getByTransactionId($id);
	}
	
	public function getByBase(int $orbitalBaseId): array
	{
		return $this->entityManager->getRepository(CommercialShipping::class)->getByBase($orbitalBaseId);
	}

	public function add(CommercialShipping $commercialShipping): void
	{
		$this->entityManager->persist($commercialShipping);
		$this->entityManager->flush($commercialShipping);

		if (CommercialShipping::ST_WAITING !== $commercialShipping->getStatement()) {
			$this->messageBus->dispatch(
				new CommercialShippingMessage($commercialShipping->getId()),
				[DateTimeConverter::to_delay_stamp($commercialShipping->getArrivedAt())],
			);
		}
	}

	public function deliver(CommercialShipping $commercialShipping, $transaction, $destOB, $commander) {
		if ($transaction !== NULL AND $transaction->statement == Transaction::ST_COMPLETED) {

			switch ($transaction->type) {
				case Transaction::TYP_RESOURCE:
					$this->orbitalBaseManager->increaseResources($destOB, $transaction->quantity, TRUE);

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($destOB->getRPlayer());
					$n->setTitle('Ressources reçues');
					$n->addBeg()->addTxt('Vous avez reçu les ' . $transaction->quantity . ' ressources que vous avez achetées au marché.');
					$n->addEnd();
					$this->notificationManager->add($n);

					break;
				case Transaction::TYP_SHIP:
					$this->orbitalBaseManager->addShipToDock($destOB, $transaction->identifier, $transaction->quantity);

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($destOB->getRPlayer());
					if ($commercialShipping->resourceTransported == NULL) {
						# transaction
						if ($transaction->quantity == 1) {
							$n->setTitle('Vaisseau reçu');
							$n->addBeg()->addTxt('Vous avez reçu le vaisseau de type ' . ShipResource::getInfo($transaction->identifier, 'codeName') . ' que vous avez acheté au marché.');
							$n->addSep()->addTxt('Il a été ajouté à votre hangar.');
						} else {
							$n->setTitle('Vaisseaux reçus');
							$n->addBeg()->addTxt('Vous avez reçu les ' . $transaction->quantity . ' vaisseaux de type ' . ShipResource::getInfo($transaction->identifier, 'codeName') . ' que vous avez achetés au marché.');
							$n->addSep()->addTxt('Ils ont été ajoutés à votre hangar.');
						}
					} else {
						# ships sending
						if ($transaction->quantity == 1) {
							$n->setTitle('Vaisseau reçu');
							$n->addBeg()->addTxt('Vous avez reçu le vaisseau de type ' . ShipResource::getInfo($transaction->identifier, 'codeName') . ' envoyé par un marchand galactique.');
							$n->addSep()->addTxt('Il a été ajouté à votre hangar.');
						} else {
							$n->setTitle('Vaisseaux reçus');
							$n->addBeg()->addTxt('Vous avez reçu les ' . $transaction->quantity . ' vaisseaux de type ' . ShipResource::getInfo($transaction->identifier, 'codeName') . ' envoyés par un marchand galactique.');
							$n->addSep()->addTxt('Ils ont été ajoutés à votre hangar.');
						}
					}
					$n->addEnd();
					$this->notificationManager->add($n);
					break;
				case Transaction::TYP_COMMANDER:
					$commander->setStatement(Commander::RESERVE);
					$commander->setRPlayer($destOB->getRPlayer());
					$commander->setRBase($commercialShipping->rBaseDestination);

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($destOB->getRPlayer());
					$n->setTitle('Commandant reçu');
					$n->addBeg()->addTxt('Le commandant ' . $commander->getName() . ' que vous avez acheté au marché est bien arrivé.');
					$n->addSep()->addTxt('Il se trouve pour le moment dans votre école de commandement');
					$n->addEnd();
					$this->notificationManager->add($n);
					break;
				default:
					throw new ErrorException('type de transaction inconnue dans deliver()');
			}

			$commercialShipping->statement = CommercialShipping::ST_MOVING_BACK;

		} elseif ($transaction === NULL AND $commercialShipping->rTransaction == NULL AND $commercialShipping->resourceTransported != NULL) {
			# resource sending

			$this->orbitalBaseManager->increaseResources($destOB, $commercialShipping->resourceTransported, TRUE);

			# notif for the player who receive the resources
			$n = new Notification();
			$n->setRPlayer($destOB->getRPlayer());
			$n->setTitle('Ressources reçues');
			$n->addBeg()->addTxt('Vous avez bien reçu les ' . $commercialShipping->resourceTransported . ' ressources sur votre base orbitale ' . $destOB->name . '.');
			$n->addEnd();
			$this->notificationManager->add($n);

			$commercialShipping->statement = CommercialShipping::ST_MOVING_BACK;
		} else {
			throw new ErrorException('impossible de délivrer ce chargement');
		}
	}
}
