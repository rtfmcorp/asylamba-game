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
					ASM::$com->load(array('id' => $transaction->identifier));

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
}