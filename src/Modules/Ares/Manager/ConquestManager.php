<?php

namespace App\Modules\Ares\Manager;

use App\Classes\Entity\EntityManager;
use App\Classes\Library\Utils;
use App\Modules\Ares\Model\Commander;
use App\Modules\Ares\Model\LiveReport;
use App\Modules\Ares\Model\Report;
use App\Modules\Athena\Manager\OrbitalBaseManager;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Demeter\Manager\ColorManager;
use App\Modules\Demeter\Model\Color;
use App\Modules\Gaia\Event\PlaceOwnerChangeEvent;
use App\Modules\Gaia\Manager\PlaceManager;
use App\Modules\Gaia\Model\Place;
use App\Modules\Hermes\Manager\NotificationManager;
use App\Modules\Zeus\Manager\PlayerBonusManager;
use App\Modules\Zeus\Manager\PlayerManager;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ConquestManager
{
	public function __construct(
		protected CommanderManager $commanderManager,
		protected PlaceManager $placeManager,
		protected PlayerManager $playerManager,
		protected ColorManager $colorManager,
		protected OrbitalBaseManager $orbitalBaseManager,
		protected PlayerBonusManager $playerBonusManager,
		protected ReportManager $reportManager,
		protected EntityManager $entityManager,
		protected EventDispatcherInterface $eventDispatcher,
		protected NotificationManager $notificationManager,
	) {

	}

	public function conquer(Commander $commander): void
	{
		$place = $this->placeManager->get($commander->rDestinationPlace);
		$place->commanders = $this->commanderManager->getBaseCommanders($place->id);
		$placeBase = $this->orbitalBaseManager->get($place->id);
		$commanderPlace = $this->placeManager->get($commander->rBase);
		$commanderPlayer = $this->playerManager->get($commander->rPlayer);
		$commanderColor = $this->colorManager->get($commanderPlayer->rColor);
		$baseCommanders = $this->commanderManager->getBaseCommanders($place->getId());
		$playerBonus = $this->playerBonusManager->getBonusByPlayer($commanderPlayer);
		$this->playerBonusManager->load($playerBonus);
		# conquete
		if (null !== $place->rPlayer) {
			$placePlayer = $this->playerManager->get($place->rPlayer);
			if (($place->playerColor != $commander->getPlayerColor() && $place->playerLevel > 3 && $commanderColor->colorLink[$place->playerColor] != Color::ALLY) || ($place->playerColor == 0)) {
				$tempCom = array();

				for ($i = 0; $i < count($place->commanders); $i++) {
					if ($place->commanders[$i]->line <= 1) {
						$tempCom[] = $place->commanders[$i];
					}
				}
				for ($i = 0; $i < count($place->commanders); $i++) {
					if ($place->commanders[$i]->line >= 2) {
						$tempCom[] = $place->commanders[$i];
					}
				}

				$place->commanders = $tempCom;

				$nbrBattle = 0;
				$reportIds   = array();
				$reportArray = array();

				while ($nbrBattle < count($place->commanders)) {
					if ($place->commanders[$nbrBattle]->statement == Commander::AFFECTED) {
						LiveReport::$type = Commander::COLO;
						LiveReport::$dFight = $commander->dArrival;

						if ($commanderColor->colorLink[$place->playerColor] == Color::ALLY || $commanderColor->colorLink[$place->playerColor] == Color::PEACE) {
							LiveReport::$isLegal = Report::ILLEGAL;
						} else {
							LiveReport::$isLegal = Report::LEGAL;
						}

						$this->commanderManager->startFight($place, $commander, $commanderPlayer, $place->commanders[$nbrBattle], $placePlayer, TRUE);

						$report = $this->commanderManager->createReport($place);
						$reportArray[] = $report;
						$reportIds[] = $report->id;
						# PATCH DEGUEU POUR LES MUTLIS-COMBATS
						$this->entityManager->clear($report);
						$reports = $this->reportManager->getByAttackerAndPlace($commander->rPlayer, $place->id, $commander->dArrival);
						foreach($reports as $r) {
							if ($r->id == $report->id) {
								continue;
							}
							$r->statementAttacker = Report::DELETED;
							$r->statementDefender = Report::DELETED;
						}
						$this->entityManager->flush(Report::class);
						$this->entityManager->clear(Report::class);
						########################################

						# mettre à jour armyInBegin si prochain combat pour prochain rapport
						for ($j = 0; $j < count($commander->armyAtEnd); $j++) {
							for ($i = 0; $i < 12; $i++) {
								$commander->armyInBegin[$j][$i] = $commander->armyAtEnd[$j][$i];
							}
						}
						for ($j = 0; $j < count($place->commanders[$nbrBattle]->armyAtEnd); $j++) {
							for ($i = 0; $i < 12; $i++) {
								$place->commanders[$nbrBattle]->armyInBegin[$j][$i] = $place->commanders[$nbrBattle]->armyAtEnd[$j][$i];
							}
						}

						$nbrBattle++;
						# mort du commandant
						# arrêt des combats
						if ($commander->getStatement() == Commander::DEAD) {
							break;
						}
					} else {
						$nbrBattle++;
					}
				}

				# victoire
				if ($commander->getStatement() != Commander::DEAD) {
					if ($nbrBattle == 0) {
						$this->placeManager->sendNotif($place, Place::CONQUERPLAYERWHITOUTBATTLESUCCESS, $commander, NULL);
					} else {
						$this->placeManager->sendNotifForConquest($place, Place::CONQUERPLAYERWHITBATTLESUCCESS, $commander, $reportIds);
					}


					#attribuer le joueur à la place
					$place->commanders = array();
					$place->playerColor = $commander->playerColor;
					$place->rPlayer = $commander->rPlayer;

					# changer l'appartenance de la base (et de la place)
					$this->orbitalBaseManager->changeOwnerById($place->id, $placeBase, $commander->getRPlayer(), $baseCommanders);
					$place->commanders[] = $commander;

					$commander->rBase = $place->id;
					$this->commanderManager->endTravel($commander, Commander::AFFECTED);
					$commander->line = 2;

					$this->eventDispatcher->dispatch(new PlaceOwnerChangeEvent($place), PlaceOwnerChangeEvent::NAME);

					# PATCH DEGUEU POUR LES MUTLIS-COMBATS
					$this->notificationManager->patchForMultiCombats($commander->rPlayer, $place->rPlayer, $commander->dArrival);
					# défaite
				} else {
					for ($i = 0; $i < count($place->commanders); $i++) {
						if ($place->commanders[$i]->statement == Commander::DEAD) {
							unset($place->commanders[$i]);
							$place->commanders = array_merge($place->commanders);
						}
					}

					$this->placeManager->sendNotifForConquest($place, Place::CONQUERPLAYERWHITBATTLEFAIL, $commander, $reportIds);
				}

			} else {
				# si c'est la même couleur
				if ($place->rPlayer == $commander->rPlayer) {
					# si c'est une de nos planètes
					# on tente de se poser
					$this->commanderManager->uChangeBase($commander);
				} else {
					# si c'est une base alliée
					# on repart
					$this->commanderManager->comeBack($place, $commander, $commanderPlace, $playerBonus);
					$this->placeManager->sendNotif($place, Place::CHANGELOST, $commander);
				}
			}

			# colonisation
		} else {
			# faire un combat
			LiveReport::$type = Commander::COLO;
			LiveReport::$dFight = $commander->dArrival;
			LiveReport::$isLegal = Report::LEGAL;

			$this->commanderManager->startFight($place, $commander, $commanderPlayer);

			# victoire
			if ($commander->getStatement() !== Commander::DEAD) {
				# attribuer le rPlayer à la Place !
				$place->rPlayer = $commander->rPlayer;
				$place->commanders[] = $commander;
				$place->playerColor = $commander->playerColor;
				$place->typeOfBase = 4;

				# créer une base
				$ob = new OrbitalBase();
				$ob->rPlace = $place->id;
				$ob->setRPlayer($commander->getRPlayer());
				$ob->setName('colonie');
				$ob->iSchool = 500;
				$ob->iAntiSpy = 500;
				$ob->resourcesStorage = 2000;
				$ob->uOrbitalBase = Utils::now();
				$ob->dCreation = Utils::now();
				$this->orbitalBaseManager->updatePoints($ob);

				$this->orbitalBaseManager->add($ob);

				# attibuer le commander à la place
				$commander->rBase = $place->id;
				$this->commanderManager->endTravel($commander, Commander::AFFECTED);
				$commander->line = 2;

				# création du rapport
				$report = $this->commanderManager->createReport($place);

				$place->danger = 0;

				$this->placeManager->sendNotif($place, Place::CONQUEREMPTYSSUCCESS, $commander, $report->id);

				$this->eventDispatcher->dispatch(new PlaceOwnerChangeEvent($place), PlaceOwnerChangeEvent::NAME);

				# défaite
			} else {
				# création du rapport
				$report = $this->commanderManager->createReport($place);

				# mise à jour du danger
				$percentage = (($report->pevAtEndD + 1) / ($report->pevInBeginD + 1)) * 100;
				$place->danger = round(($percentage * $place->danger) / 100);

				$this->placeManager->sendNotif($place, Place::CONQUEREMPTYFAIL, $commander);

				# enlever le commandant de la place
				foreach ($commanderPlace->commanders as $placeCommander) {
					if ($placeCommander->getId() == $commander->getId()) {
						unset($placeCommander);
						$commanderPlace->commanders = array_merge($commanderPlace->commanders);
					}
				}
			}
		}
		$this->entityManager->flush(Commander::class);
		$this->entityManager->flush($place);
	}
}
