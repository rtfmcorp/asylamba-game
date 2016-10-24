<?php

/**
 * CommercialShipping
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 13.11.13
 */
namespace Asylamba\Modules\Athena\Model;

use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Utils;

class CommercialShipping {
	# statement
	const ST_WAITING = 0;		# pret au départ, statique
	const ST_GOING = 1;			# aller
	const ST_MOVING_BACK = 2;	# retour

	const WEDGE = 1000;	# soute

	# attributes
	public $id = 0; 
	public $rPlayer = 0;
	public $rBase = 0;
	public $rBaseDestination = 0;
	public $rTransaction = NULL;			# soit l'un
	public $resourceTransported = NULL;		# soit l'autre
	public $shipQuantity = 0;
	public $dDeparture = '';
	public $dArrival = '';
	public $statement = 0;

	public $baseRSystem;
	public $basePosition;
	public $baseXSystem;
	public $baseYSystem;

	public $destinationRSystem;
	public $destinationPosition;
	public $destinationXSystem;
	public $destinationYSystem;

	public $price;
	public $typeOfTransaction;
	public $quantity;
	public $identifier;
	public $commanderAvatar;
	public $commanderName;
	public $commanderLevel;
	public $commanderVictory;
	public $commanderExperience;

	public function getId() { return $this->id; }

	public function deliver($transaction, $destOB, $commander) {

		if ($transaction !== NULL AND $transaction->statement == Transaction::ST_COMPLETED) {

			switch ($transaction->type) {
				case Transaction::TYP_RESOURCE:
					$destOB->increaseResources($transaction->quantity, TRUE);

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($destOB->getRPlayer());
					$n->setTitle('Ressources reçues');
					$n->addBeg()->addTxt('Vous avez reçu les ' . $transaction->quantity . ' ressources que vous avez achetées au marché.');
					$n->addEnd();
					ASM::$ntm->add($n);

					break;
				case Transaction::TYP_SHIP:
					$destOB->addShipToDock($transaction->identifier, $transaction->quantity);

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($destOB->getRPlayer());
					if ($this->resourceTransported == NULL) {
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
					ASM::$ntm->add($n);
					break;
				case Transaction::TYP_COMMANDER:
					$commander->setStatement(Commander::RESERVE);
					$commander->setRPlayer($destOB->getRPlayer());
					$commander->setRBase($this->rBaseDestination);

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($destOB->getRPlayer());
					$n->setTitle('Commandant reçu');
					$n->addBeg()->addTxt('Le commandant ' . $commander->getName() . ' que vous avez acheté au marché est bien arrivé.');
					$n->addSep()->addTxt('Il se trouve pour le moment dans votre école de commandement');
					$n->addEnd();
					ASM::$ntm->add($n);
					break;
				default:
					CTR::$alert->add('type de transaction inconnue dans deliver()', ALERT_STD_ERROR);
					break;
			}

			$this->statement = self::ST_MOVING_BACK;

		} elseif ($transaction === NULL AND $this->rTransaction == NULL AND $this->resourceTransported != NULL) {
			# resource sending

			$destOB->increaseResources($this->resourceTransported, TRUE);

			# notif for the player who receive the resources
			$n = new Notification();
			$n->setRPlayer($destOB->getRPlayer());
			$n->setTitle('Ressources reçues');
			$n->addBeg()->addTxt('Vous avez bien reçu les ' . $this->resourceTransported . ' ressources sur votre base orbitale ' . $destOB->name . '.');
			$n->addEnd();
			ASM::$ntm->add($n);

			$this->statement = self::ST_MOVING_BACK;
		} else {
			CTR::$alert->add('impossible de délivrer ce chargement', ALERT_STD_ERROR);
		}
	}

	public function render() {
		switch ($this->typeOfTransaction) {
			case Transaction::TYP_RESOURCE: $class = 'resources'; break;
			case Transaction::TYP_COMMANDER: $class = 'commander'; break;
			case Transaction::TYP_SHIP:
				$class = 'ship';
				break;
			default: break;
		}

		echo '<div class="transaction ' . $class . '">';
			if ($this->statement != CommercialShipping::ST_MOVING_BACK) {
				echo '<div class="product">';
					if ($this->statement == CommercialShipping::ST_WAITING) {
						echo '<a href="' . Format::actionBuilder('canceltransaction', ['rtransaction' => $this->rTransaction]) . '" class="hb lt right-link" title="supprimer cette offre coûtera ' . Format::number(floor($this->price * Transaction::PERCENTAGE_TO_CANCEL / 100)) . ' crédits">×</a>';
					}
					if ($this->typeOfTransaction == Transaction::TYP_RESOURCE) {
						echo '<img src="' . MEDIA . 'market/resources-pack-' . Transaction::getResourcesIcon($this->quantity) . '.png" alt="" class="picto" />';
						echo '<div class="offer">';
							if ($this->resourceTransported == NULL) {
								# transaction
								echo Format::numberFormat($this->quantity) . ' <img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
							} else {
								# resources sending
								echo Format::numberFormat($this->resourceTransported) . ' <img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
							}
						echo '</div>';
					} elseif ($this->typeOfTransaction == Transaction::TYP_COMMANDER) {
						echo '<img src="' . MEDIA . 'commander/small/' . $this->commanderAvatar . '.png" alt="" class="picto" />';
						echo '<div class="offer">';
							echo '<strong>' . CommanderResources::getInfo($this->commanderLevel, 'grade') . ' ' . $this->commanderName . '</strong>';
							echo '<em>' . Format::numberFormat($this->commanderExperience) . ' xp | ' . $this->commanderVictory . ' victoire' . Format::addPlural($this->commanderVictory) . '</em>';
						echo '</div>';
					} elseif ($this->typeOfTransaction == Transaction::TYP_SHIP) {
						echo '<img src="' . MEDIA . 'ship/picto/ship' . $this->identifier . '.png" alt="" class="picto" />';
						echo '<div class="offer">';
							echo '<strong>' . $this->quantity . ' ' . ShipResource::getInfo($this->identifier, 'codeName') . Format::plural($this->quantity) . '</strong>';
							echo '<em>' . ShipResource::getInfo($this->identifier, 'name') . ' / ' . ShipResource::getInfo($this->identifier, 'pev') . ' pev</em>';
						echo '</div>';
					}

					if ($this->resourceTransported == NULL) {
						# transaction
						echo '<div class="for">';
							echo '<span>pour</span>';
						echo '</div>';
						echo '<div class="price">';
							echo Format::numberFormat($this->price) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
						echo '</div>';
					} elseif ($this->resourceTransported == 0) {
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

			$totalTime   = Utils::interval($this->dDeparture, $this->dArrival, 's');
			$currentTime = Utils::interval(Utils::now(), $this->dDeparture, 's');

			echo ($this->statement != self::ST_WAITING)
				?'<div class="shipping progress" data-progress-total-time="' . $totalTime . '" data-progress-current-time="' . ($totalTime - $currentTime) . '" data-progress-output="lite">'
				: '<div class="shipping">';
				echo '<span class="progress-container">';
					echo '<span style="width: ' . Format::percent($currentTime, $totalTime) . '%;" class="progress-bar"></span>';
				echo '</span>';

				echo '<div class="ships">';
					echo $this->shipQuantity;
					echo '<img src="' . MEDIA . 'resources/transport.png" alt="" class="icon-color" />';
				echo '</div>';

				if ($this->statement == CommercialShipping::ST_WAITING) {
					echo '<div class="time">à quai</div>';
				} else {
					echo '<div class="time progress-text"></div>';
				}
			echo '</div>';
		echo '</div>';
	}
}