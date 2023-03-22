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
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Library\DateTimeConverter;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Athena\Message\Trade\CommercialShippingMessage;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Modules\Athena\Model\CommercialShipping;
use Asylamba\Modules\Athena\Model\Transaction;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Session\SessionWrapper;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Classes\Exception\ErrorException;
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

	public function render(CommercialShipping $commercialShipping) {
		switch ($commercialShipping->typeOfTransaction) {
			case Transaction::TYP_RESOURCE: $class = 'resources'; break;
			case Transaction::TYP_COMMANDER: $class = 'commander'; break;
			case Transaction::TYP_SHIP:
				$class = 'ship';
				break;
			default: break;
		}

		echo '<div class="transaction ' . $class . '">';
			if ($commercialShipping->statement != CommercialShipping::ST_MOVING_BACK) {
				echo '<div class="product">';
					if ($commercialShipping->statement == CommercialShipping::ST_WAITING) {
						echo '<a href="' . Format::actionBuilder('canceltransaction', $this->sessionWrapper->get('token'), ['rtransaction' => $commercialShipping->rTransaction]) . '" class="hb lt right-link" title="supprimer cette offre coûtera ' . Format::number(floor($commercialShipping->price * Transaction::PERCENTAGE_TO_CANCEL / 100)) . ' crédits">×</a>';
					}
					if ($commercialShipping->typeOfTransaction == Transaction::TYP_RESOURCE) {
						echo '<img src="' . $this->mediaPath . 'market/resources-pack-' . Transaction::getResourcesIcon($commercialShipping->quantity) . '.png" alt="" class="picto" />';
						echo '<div class="offer">';
							if ($commercialShipping->resourceTransported == NULL) {
								# transaction
								echo Format::numberFormat($commercialShipping->quantity) . ' <img src="' . $this->mediaPath . 'resources/resource.png" alt="" class="icon-color" />';
							} else {
								# resources sending
								echo Format::numberFormat($commercialShipping->resourceTransported) . ' <img src="' . $this->mediaPath . 'resources/resource.png" alt="" class="icon-color" />';
							}
						echo '</div>';
					} elseif ($commercialShipping->typeOfTransaction == Transaction::TYP_COMMANDER) {
						echo '<img src="' . $this->mediaPath . 'commander/small/' . $commercialShipping->commanderAvatar . '.png" alt="" class="picto" />';
						echo '<div class="offer">';
							echo '<strong>' . CommanderResources::getInfo($commercialShipping->commanderLevel, 'grade') . ' ' . $commercialShipping->commanderName . '</strong>';
							echo '<em>' . Format::numberFormat($commercialShipping->commanderExperience) . ' xp | ' . $commercialShipping->commanderVictory . ' victoire' . Format::addPlural($commercialShipping->commanderVictory) . '</em>';
						echo '</div>';
					} elseif ($commercialShipping->typeOfTransaction == Transaction::TYP_SHIP) {
						echo '<img src="' . $this->mediaPath . 'ship/picto/ship' . $commercialShipping->identifier . '.png" alt="" class="picto" />';
						echo '<div class="offer">';
							echo '<strong>' . $commercialShipping->quantity . ' ' . ShipResource::getInfo($commercialShipping->identifier, 'codeName') . Format::plural($commercialShipping->quantity) . '</strong>';
							echo '<em>' . ShipResource::getInfo($commercialShipping->identifier, 'name') . ' / ' . ShipResource::getInfo($commercialShipping->identifier, 'pev') . ' pev</em>';
						echo '</div>';
					}

					if ($commercialShipping->resourceTransported === NULL) {
						# transaction
						echo '<div class="for">';
							echo '<span>pour</span>';
						echo '</div>';
						echo '<div class="price">';
							echo Format::numberFormat($commercialShipping->price) . ' <img src="' . $this->mediaPath . 'resources/credit.png" alt="" class="icon-color" />';
						echo '</div>';
					} elseif ($commercialShipping->resourceTransported == 0) {
						# ships sending
						echo '<div class="for"><span></span></div>';
						echo '<div class="price">';
							echo 'envoi de vaisseaux';
						echo '</div>';
					} else {
						# resources sending
						echo '<div class="for"><span></span></div>';
						echo '<div class="price">';
							echo 'envoi de ressources';
						echo '</div>';
					}
				echo '</div>';
			}

			$totalTime   = Utils::interval($commercialShipping->dDeparture, $commercialShipping->dArrival, 's');
			$currentTime = Utils::interval(Utils::now(), $commercialShipping->dDeparture, 's');

			echo ($commercialShipping->statement != CommercialShipping::ST_WAITING)
				?'<div class="shipping progress" data-progress-total-time="' . $totalTime . '" data-progress-current-time="' . ($totalTime - $currentTime) . '" data-progress-output="lite">'
				: '<div class="shipping">';
				echo '<span class="progress-container">';
					echo '<span style="width: ' . Format::percent($currentTime, $totalTime) . '%;" class="progress-bar"></span>';
				echo '</span>';

				echo '<div class="ships">';
					echo $commercialShipping->shipQuantity;
					echo '<img src="' . $this->mediaPath . 'resources/transport.png" alt="" class="icon-color" />';
				echo '</div>';

				if ($commercialShipping->statement == CommercialShipping::ST_WAITING) {
					echo '<div class="time">à quai</div>';
				} else {
					echo '<div class="time progress-text"></div>';
				}
			echo '</div>';
		echo '</div>';
	}
}
