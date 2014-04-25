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

class CommercialShipping {
	# statement
	const ST_WAITING = 0;		# pret au départ, statique
	const ST_GOING = 1;			# aller
	const ST_MOVING_BACK = 2;	# retour

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

	public function deliver() {
		$S_TRM1 = ASM::$trm->getCurrentSession();
		ASM::$trm->newSession(FALSE);
		ASM::$trm->load(array('id' => $this->rTransaction));
		$transaction = ASM::$trm->get();

		if (ASM::$trm->size() == 1 AND $transaction->statement == Transaction::ST_COMPLETED) {
			$S_OBM1 = ASM::$obm->getCurrentSession();
			ASM::$obm->newSession(FALSE);
			ASM::$obm->load(array('rPlace' => $this->rBaseDestination));
			$orbitalBase = ASM::$obm->get();
			switch ($transaction->type) {
				case Transaction::TYP_RESOURCE:
					$orbitalBase->increaseResources($transaction->quantity);
					# notif pour le vendeur
					/*$n = new Notification();
					$n->setRPlayer($this->rPlayer);
					$n->setTitle('Ressources livrées');
					$n->addBeg()->addTxt('Les ' . $transaction->quantity . ' ressources ont bien été livrées à ');
					$n->addLnk('diary/player-' . $orbitalBase->getRPlayer(), 'votre acheteur')->addTxt(' sur sa base ');
					$n->addLnk('map/base-' . $orbitalBase->getRPlace(), $orbitalBase->getName());
					$n->addSep()->addTxt('Vos vaisseaux de transport sont sur le chemin du retour.');
					$n->addEnd();
					ASM::$ntm->add($n);*/

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($orbitalBase->getRPlayer());
					$n->setTitle('Ressources reçues');
					$n->addBeg()->addTxt('Vous avez reçu les ' . $transaction->quantity . ' ressources que vous avez achetées au marché.');
					$n->addEnd();
					ASM::$ntm->add($n);

					break;
				case Transaction::TYP_SHIP:
					$orbitalBase->addShipToDock($transaction->identifier, $transaction->quantity);

					# notif pour le vendeur
					/*$n = new Notification();
					$n->setRPlayer($this->rPlayer);
					if ($transaction->quantity == 1) {
						$n->setTitle('Vaisseau livré');
						$n->addBeg()->addTxt('Le vaisseau de type ' . ShipResource::getInfo($transaction->identifier, 'codeName') . ' a bien été livré à ');
					} else {
						$n->setTitle('Vaisseaux livrés');
						$n->addBeg()->addTxt('Les ' . $transaction->quantity . ' vaisseaux de type ' . ShipResource::getInfo($transaction->identifier, 'codeName') . ' ont bien été livrés à ');
					}
					$n->addLnk('diary/player-' . $orbitalBase->getRPlayer(), 'votre acheteur')->addTxt(' sur sa base ');
					$n->addLnk('map/base-' . $orbitalBase->getRPlace(), $orbitalBase->getName());
					$n->addSep()->addTxt('Vos vaisseaux de transport sont sur le chemin du retour.');
					$n->addEnd();
					ASM::$ntm->add($n);*/

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($orbitalBase->getRPlayer());
					if ($transaction->quantity == 1) {
						$n->setTitle('Vaisseau reçu');
						$n->addBeg()->addTxt('Vous avez reçu le vaisseau de type ' . ShipResource::getInfo($transaction->identifier, 'codeName') . ' que vous avez acheté au marché.');
						$n->addSep()->addTxt('Il a été ajouté à votre hangar.');
					} else {
						$n->setTitle('Vaisseaux reçus');
						$n->addBeg()->addTxt('Vous avez reçu les ' . $transaction->quantity . ' vaisseaux de type ' . ShipResource::getInfo($transaction->identifier, 'codeName') . ' que vous avez achetés au marché.');
						$n->addSep()->addTxt('Ils ont été ajoutés à votre hanger.');
					}
					$n->addEnd();
					ASM::$ntm->add($n);
					break;
				case Transaction::TYP_COMMANDER:
					include_once ARES;
					$S_COM1 = ASM::$com->getCurrentSession();
					ASM::$com->newSession(ASM_UMODE);
					ASM::$com->load(array('c.id' => $transaction->identifier));

					$commander = ASM::$com->get();
					$commander->setStatement(COM_INSCHOOL);
					$commander->setRPlayer($orbitalBase->getRPlayer());
					$commander->setRBase($this->rBaseDestination);

					# notif pour le vendeur
					/*$n = new Notification();
					$n->setRPlayer($this->rPlayer);
					$n->setTitle('Commandant livré');
					$n->addBeg()->addTxt('Le commandant ' . $commander->getName() . ' a bien été livré à ');
					$n->addLnk('diary/player-' . $orbitalBase->getRPlayer(), 'votre acheteur')->addTxt(' sur sa base ');
					$n->addLnk('map/base-' . $orbitalBase->getRPlace(), $orbitalBase->getName());
					$n->addSep()->addTxt('Vos vaisseaux de transport sont sur le chemin du retour.');
					$n->addEnd();
					ASM::$ntm->add($n);*/

					# notif pour l'acheteur
					$n = new Notification();
					$n->setRPlayer($orbitalBase->getRPlayer());
					$n->setTitle('Commandant reçu');
					$n->addBeg()->addTxt('Le commandant ' . $commander->getName() . ' que vous avez acheté au marché est bien arrivé.');
					$n->addSep()->addTxt('Il se trouve pour le moment dans votre école de commandement');
					$n->addEnd();
					ASM::$ntm->add($n);

					ASM::$com->changeSession($S_COM1);
					break;
				default:
					CTR::$alert->add('type de transaction inconnue dans deliver()', ALERT_STD_ERROR);
					break;
			}

			$this->statement = self::ST_MOVING_BACK;

			ASM::$obm->changeSession($S_OBM1);
		} else {
			CTR::$alert->add('impossible de délivrer ce chargement', ALERT_STD_ERROR);
		}

		ASM::$trm->changeSession($S_TRM1);
	}

	public function render() {
		switch ($this->typeOfTransaction) {
			case Transaction::TYP_RESOURCE: $class = 'resources'; break;
			case Transaction::TYP_COMMANDER: $class = 'commander'; break;
			case Transaction::TYP_SHIP:
				include_once ARES;
				$class = 'ship';
				break;
			default: break;
		}

		echo '<div class="transaction ' . $class . '">';
			echo '<div class="product">';
				if ($this->statement != CommercialShipping::ST_MOVING_BACK) {
					if ($this->typeOfTransaction == Transaction::TYP_RESOURCE) {
						echo '<img src="' . MEDIA . 'market/resources-pack-' . Transaction::getResourcesIcon($this->quantity) . '.png" alt="" class="picto" />';
						echo '<div class="offer">';
							echo Format::numberFormat($this->quantity) . ' <img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
						echo '</div>';
					} elseif ($this->typeOfTransaction == Transaction::TYP_COMMANDER) {
						echo '<img src="' . MEDIA . 'commander/small/c1-l3-c1.png" alt="" class="picto" />';
						echo '<div class="offer">';
							echo '<strong>' . CommanderResources::getInfo($this->commanderLevel, 'grade') . ' ' . $this->commanderName . '</strong>';
							echo '<em>' . Format::numberFormat($this->commanderExperience) . ' xp | ' . $this->commanderVictory . ' victoire' . Format::addPlural($this->commanderVictory) . '</em>';
						echo '</div>';
					} elseif ($this->typeOfTransaction == Transaction::TYP_SHIP) {
						echo '<img src="' . MEDIA . 'ship/picto/ship' . $this->identifier . '.png" alt="" class="picto" />';
						echo '<div class="offer">';
							echo '<strong>' . $this->quantity . ' ' . ShipResource::getInfo($this->identifier, 'codeName') . '</strong>';
							echo '<em>' . ShipResource::getInfo($this->identifier, 'name') . ' / ' . ShipResource::getInfo($this->identifier, 'pev') . ' pev</em>';
						echo '</div>';
					}
				}
				echo '<div class="for">';
					echo '<span>pour</span>';
				echo '</div>';
				echo '<div class="price">';
					echo Format::numberFormat($this->price) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" />';
				echo '</div>';
			echo '</div>';

			$totalTime   = Utils::interval($this->dDeparture, $this->dArrival, 's');
			$currentTime = Utils::interval(Utils::now(), $this->dDeparture, 's');

			echo '<div class="shipping progress" data-progress-total-time="' . $totalTime . '" data-progress-current-time="' . ($totalTime - $currentTime) . '" data-progress-output="lite">';
				echo '<span class="progress-container">';
					echo '<span style="width: ' . Format::percent($currentTime, $totalTime) . '%;" class="progress-bar"></span>';
				echo '</span>';

				echo '<div class="ships">';
					echo $this->shipQuantity;
					echo '<img src="' . MEDIA . 'resources/resource.png" alt="" class="icon-color" />';
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