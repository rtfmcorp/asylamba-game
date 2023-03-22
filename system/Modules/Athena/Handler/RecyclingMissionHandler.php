<?php

namespace Asylamba\Modules\Athena\Handler;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\DateTimeConverter;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Athena\Manager\RecyclingLogManager;
use Asylamba\Modules\Athena\Manager\RecyclingMissionManager;
use Asylamba\Modules\Athena\Message\RecyclingMissionMessage;
use Asylamba\Modules\Athena\Model\RecyclingLog;
use Asylamba\Modules\Athena\Model\RecyclingMission;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Gaia\Manager\PlaceManager;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class RecyclingMissionHandler implements MessageHandlerInterface
{
	public function __construct(
		protected RecyclingMissionManager $recyclingMissionManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected PlaceManager $placeManager,
		protected PlayerManager $playerManager,
		protected NotificationManager $notificationManager,
		protected RecyclingLogManager $recyclingLogManager,
		protected EntityManager $entityManager,
		protected MessageBusInterface $messageBus,
	) {

	}

	public function __invoke(RecyclingMissionMessage $message): void
	{
		$mission = $this->recyclingMissionManager->get($message->getRecyclingMissionId());
		$orbitalBase = $this->orbitalBaseManager->get($mission->rBase);
		$targetPlace = $this->placeManager->get($mission->rTarget);

		$player = $this->playerManager->get($orbitalBase->getRPlayer());
		if ($targetPlace->typeOfPlace != Place::EMPTYZONE) {
			# make the recycling : decrease resources on the target place
			$totalRecycled = $mission->recyclerQuantity * RecyclingMission::RECYCLER_CAPACTIY;
			$targetPlace->resources -= $totalRecycled;
			# if there is no more resource
			if ($targetPlace->resources <= 0) {
				// Avoid out of range errors
				$targetPlace->resources = 0;
				# the place become an empty place
				$this->placeManager->turnAsEmptyPlace($targetPlace);

				# stop the mission
				$mission->statement = RecyclingMission::ST_DELETED;

				# send notification to the player
				$n = new Notification();
				$n->setRPlayer($player->id);
				$n->setTitle('Arrêt de mission de recyclage');
				$n->addBeg()->addTxt('Un ');
				$n->addLnk('map/place-' . $mission->rTarget, 'lieu');
				$n->addTxt(' que vous recycliez est désormais totalement dépourvu de ressources et s\'est donc transformé en lieu vide.');
				$n->addSep()->addTxt('Vos recycleurs restent donc stationnés sur votre ');
				$n->addLnk('map/place-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
				$n->addEnd();
				$this->notificationManager->add($n);
			}

			# if the sector change its color between 2 recyclings
			if ($player->rColor != $targetPlace->sectorColor && $targetPlace->sectorColor != ColorResource::NO_FACTION) {
				# stop the mission
				$mission->statement = RecyclingMission::ST_DELETED;

				# send notification to the player
				$n = new Notification();
				$n->setRPlayer($player->id);
				$n->setTitle('Arrêt de mission de recyclage');
				$n->addBeg()->addTxt('Le secteur d\'un ');
				$n->addLnk('map/place-' . $mission->rTarget, 'lieu');
				$n->addTxt(' que vous recycliez est passé à l\'ennemi, vous ne pouvez donc plus y envoyer vos recycleurs. La mission est annulée.');
				$n->addSep()->addTxt('Vos recycleurs restent donc stationnés sur votre ');
				$n->addLnk('map/place-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
				$n->addEnd();
				$this->notificationManager->add($n);
			}

			$creditRecycled = round($targetPlace->population * $totalRecycled * 10 / 100);
			$resourceRecycled = round($targetPlace->coefResources * $totalRecycled / 100);
			$shipRecycled = round($targetPlace->coefHistory * $totalRecycled / 100);

			# diversify a little (resource and credit)
			$percent = rand(-5, 5);
			$diffAmountCredit = round($creditRecycled * $percent / 100);
			$diffAmountResource = round($resourceRecycled * $percent / 100);
			$creditRecycled += $diffAmountCredit;
			$resourceRecycled -= $diffAmountResource;

			if ($creditRecycled < 0) { $creditRecycled = 0; }
			if ($resourceRecycled < 0) { $resourceRecycled = 0; }

			# convert shipRecycled to real ships
			$pointsToRecycle = round($shipRecycled * RecyclingMission::COEF_SHIP);
			$shipsArray1 = array();
			$buyShip = array();
			for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) {
				if (floor($pointsToRecycle / ShipResource::getInfo($i, 'resourcePrice')) > 0) {
					$shipsArray1[] = array(
						'ship' => $i,
						'price' => ShipResource::getInfo($i, 'resourcePrice'),
						'canBuild' => TRUE);
				}
				$buyShip[] = 0;
			}

			shuffle($shipsArray1);
			$shipsArray = array();
			$onlyThree = 0;
			foreach ($shipsArray1 as $key => $value) {
				$onlyThree++;
				$shipsArray[] = $value;
				if ($onlyThree == 3) {
					break;
				}
			}
			$continue = TRUE;
			if (count($shipsArray) > 0) {
				while($continue) {
					foreach ($shipsArray as $key => $line) {
						if ($line['canBuild']) {
							$nbmax = floor($pointsToRecycle / $line['price']);
							if ($nbmax < 1) {
								$shipsArray[$key]['canBuild'] = FALSE;
							} else {
								$qty = rand(1, $nbmax);
								$pointsToRecycle -= $qty * $line['price'];
								$buyShip[$line['ship']] += $qty;
							}
						}
					}

					$canBuild = FALSE;
					# verify if we can build one more ship
					foreach ($shipsArray as $key => $line) {
						if ($line['canBuild']) {
							$canBuild = TRUE;
							break;
						}
					}
					if (!$canBuild) {
						# if the 3 types of ships can't be build anymore --> stop
						$continue = FALSE;
					}
				}
			}

			# create a RecyclingLog
			$rl = new RecyclingLog();
			$rl->rRecycling = $mission->id;
			$rl->resources = $resourceRecycled;
			$rl->credits = $creditRecycled;
			$rl->ship0 = $buyShip[0];
			$rl->ship1 = $buyShip[1];
			$rl->ship2 = $buyShip[2];
			$rl->ship3 = $buyShip[3];
			$rl->ship4 = $buyShip[4];
			$rl->ship5 = $buyShip[5];
			$rl->ship6 = $buyShip[6];
			$rl->ship7 = $buyShip[7];
			$rl->ship8 = $buyShip[8];
			$rl->ship9 = $buyShip[9];
			$rl->ship10 = $buyShip[10];
			$rl->ship11 = $buyShip[11];
			$rl->dLog = Utils::addSecondsToDate($mission->uRecycling, $mission->cycleTime);
			$this->recyclingLogManager->add($rl);

			# give to the orbitalBase ($orbitalBase) and player what was recycled
			$this->orbitalBaseManager->increaseResources($orbitalBase, $resourceRecycled);
			for ($i = 0; $i < ShipResource::SHIP_QUANTITY; $i++) {
				$this->orbitalBaseManager->addShipToDock($orbitalBase, $i, $buyShip[$i]);
			}
			$this->playerManager->increaseCredit($player, $creditRecycled);

			# add recyclers waiting to the mission
			$mission->recyclerQuantity += $mission->addToNextMission;
			$mission->addToNextMission = 0;

			# if a mission is stopped by the user, delete it
			if ($mission->statement == RecyclingMission::ST_BEING_DELETED) {
				$mission->statement = RecyclingMission::ST_DELETED;
			}

			# update u
			$mission->uRecycling = Utils::addSecondsToDate($mission->uRecycling, $mission->cycleTime);
			// Schedule the next mission if there is still resources
			if ($mission->getStatement() !== RecyclingMission::ST_DELETED) {
				$this->messageBus->dispatch(
					new RecyclingMissionMessage($mission->getId()),
					[DateTimeConverter::to_delay_stamp($mission->uRecycling)]
				);
            }
		} else {
			# the place become an empty place
			$targetPlace->resources = 0;

			# stop the mission
			$mission->statement = RecyclingMission::ST_DELETED;

			# send notification to the player
			$n = new Notification();
			$n->setRPlayer($player->id);
			$n->setTitle('Arrêt de mission de recyclage');
			$n->addBeg()->addTxt('Un ');
			$n->addLnk('map/place-' . $mission->rTarget, 'lieu');
			$n->addTxt(' que vous recycliez est désormais totalement dépourvu de ressources et s\'est donc transformé en lieu vide.');
			$n->addSep()->addTxt('Vos recycleurs restent donc stationnés sur votre ');
			$n->addLnk('map/place-' . $orbitalBase->rPlace, 'base orbitale')->addTxt(' le temps que vous programmiez une autre mission.');
			$n->addEnd();
			$this->notificationManager->add($n);
		}
		$this->entityManager->flush();
	}
}
