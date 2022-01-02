<?php

/**
 * Place Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 20.05.13
*/
namespace Asylamba\Modules\Gaia\Manager;

use Asylamba\Classes\Entity\EntityManager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;

use Asylamba\Modules\Hermes\Manager\NotificationManager;

use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Model\Report;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Gaia\Model\System;

use Asylamba\Modules\Gaia\Event\PlaceOwnerChangeEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class PlaceManager
{
	public function __construct(
		protected EntityManager $entityManager,
		protected NotificationManager $notificationManager,
		protected EventDispatcher $eventDispatcher
	) {

	}
	
	public function get(int $id): ?Place
	{
		if(($place = $this->entityManager->getRepository(Place::class)->get($id)) !== null) {
			return $place;
		}
		return null;
	}
	
	public function getByIds(array $ids = []): array
	{
		return $this->entityManager->getRepository(Place::class)->getByIds($ids);
	}
	
	public function getSystemPlaces(System $system): array
	{
		return $this->entityManager->getRepository(Place::class)->getSystemPlaces($system->getId());
	}

	public function search(string $search): array
	{
		return $this->entityManager->getRepository(Place::class)->search($search);
	}
	
	public function getPlayerPlaces(): array
	{
		return $this->entityManager->getRepository(Place::class)->getPlayerPlaces();
	}
	
	public function getNpcPlaces(): array
	{
		return $this->entityManager->getRepository(Place::class)->getNpcPlaces();
	}

	public function updateNpcPlaces(): void
	{
		$places = $this->getNpcPlaces();
		$now   = Utils::now();
		$repository = $this->entityManager->getRepository(Place::class);
		$this->entityManager->beginTransaction();
		
		foreach ($places as $place) {
			if (Utils::interval($place->uPlace, $now, 's') === 0) {
				continue;
			}
			# update time
			$hours = Utils::intervalDates($now, $place->uPlace);
			$place->uPlace = $now;
			$initialResources = $place->resources;
			$initialDanger = $place->danger;
			$maxResources = ceil($place->population / Place::COEFFPOPRESOURCE) * Place::COEFFMAXRESOURCE * ($place->maxDanger + 1);

			foreach ($hours as $hour) {
				$place->danger += Place::REPOPDANGER;
				$place->resources += floor(Place::COEFFRESOURCE * $place->population);
			}
			// The repository method will add the new resources. We have to calculate how many resources have been added
			$place->resources = abs($place->resources - $initialResources);
			// If the max is reached, we have to add just the difference between the max and init value
			if ($place->resources > $maxResources) {
				$place->resources = $maxResources - $initialResources;
			}
			$place->danger = abs($place->danger - $initialDanger);
			// Same thing here
			if ($place->danger > $place->maxDanger) {
				$place->danger = $place->maxDanger - $initialDanger;
			}
			$repository->updatePlace($place, true);
		}
		$repository->npcQuickfix();
		$this->entityManager->commit();
		$this->entityManager->clear(Place::class);
	}

	public function updatePlayerPlaces(): void
	{
		$places = $this->getPlayerPlaces();
		$now   = Utils::now();
		$repository = $this->entityManager->getRepository(Place::class);
		$this->entityManager->beginTransaction();
		
		foreach ($places as $place) {
			if (Utils::interval($place->uPlace, $now, 's') === 0) {
				continue;
			}
			# update time
			$hours = Utils::intervalDates($now, $place->uPlace);
			$place->uPlace = $now;
			$initialResources = $place->resources;
			$maxResources = ceil($place->population / Place::COEFFPOPRESOURCE) * Place::COEFFMAXRESOURCE * ($place->maxDanger + 1);
			foreach ($hours as $hour) {
				$place->resources += floor(Place::COEFFRESOURCE * $place->population);
			}
			$place->resources = abs($place->resources - $initialResources);
			if ($place->resources > $maxResources) {
				$place->resources = $maxResources;
			}
			$repository->updatePlace($place);
		}
		$this->entityManager->commit();
		$this->entityManager->clear(Place::class);
	}
    
    public function turnAsEmptyPlace(Place $place): bool
    {
        return $this->entityManager->getRepository(Place::class)->turnAsEmptyPlace($place->getId());
    }
	
	public function turnAsSpawnPlace(int $placeId, int $playerId): bool
	{
		$this->eventDispatcher->dispatch(new PlaceOwnerChangeEvent($this->get($placeId)), PlaceOwnerChangeEvent::NAME);
		
		return $this->entityManager->getRepository(Place::class)->turnAsSpawnPlace($placeId, $playerId);
	}

	public function sendNotif(Place $place, string $case, Commander $commander, Report $report = null): void
	{
		switch ($case) {
			case Place::CHANGESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' est arrivé sur ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('.')
					->addEnd();
				$this->notificationManager->add($notif);
				break;

			case Place::CHANGEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' s\'est posé sur ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('. Il est en garnison car il n\'y avait pas assez de place en orbite.')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::CHANGELOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Déplacement raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId(), $commander->getName())
					->addTxt(' n\'est pas arrivé sur ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('. Cette base ne vous appartient pas. Elle a pu être conquise entre temps.')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::LOOTEMPTYSSUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $place->id, Game::formatCoord($place->xSystem, $place->ySystem, $place->position, $place->rSector))
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResources()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::LOOTEMPTYFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors de l\'attaque de la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $place->id, Game::formatCoord($place->xSystem, $place->ySystem, $place->position, $place->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::LOOTPLAYERWHITBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $place->rPlayer, $place->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResources()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				$this->notificationManager->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($place->rPlayer);
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a pillé votre planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResources()), 'ressources pillées')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::LOOTPLAYERWHITBATTLEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage raté');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors du pillage de la planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $place->rPlayer, $place->playerName)
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				$this->notificationManager->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($place->rPlayer);
				$notif->setTitle('Rapport de combat');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a attaqué votre planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Vous avez repoussé l\'ennemi avec succès.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::LOOTPLAYERWHITOUTBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Pillage réussi');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a pillé la planète non défendue ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $place->rPlayer, $place->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResources()), 'ressources pillées')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addEnd();
				$this->notificationManager->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($place->rPlayer);
				$notif->setTitle('Rapport de pillage');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a pillé votre planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('. Aucune flotte n\'était en position pour la défendre. ')
					->addSep()
					->addBoxResource('resource', Format::number($commander->getResources()), 'ressources pillées')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::LOOTLOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Erreur de coordonnées');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' n\'a pas attaqué la planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt(' car son joueur est de votre faction, sous la protection débutant ou un allié.')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::CONQUEREMPTYSSUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Colonisation réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a colonisé la planète rebelle située aux coordonnées ')  
					->addLnk('map/place-' . $place->id , Game::formatCoord($place->xSystem, $place->ySystem, $place->position, $place->rSector) . '.')
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addTxt('Votre empire s\'étend, administrez votre ')
					->addLnk('bases/base-' . $place->id, 'nouvelle planète')
					->addTxt('.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::CONQUEREMPTYFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Colonisation ratée');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial', $commander->getName())
					->addTxt(' est tombé lors de l\'attaque de la planète rebelle située aux coordonnées ')
					->addLnk('map/place-' . $place->id, Game::formatCoord($place->xSystem, $place->ySystem, $place->position, $place->rSector))
					->addTxt('.')
					->addSep()
					->addTxt('Il a désormais rejoint le Mémorial. Que son âme traverse l\'Univers dans la paix.')
					->addSep()
					->addLnk('fleet/view-archive/report-' . $report, 'voir le rapport')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::CONQUERPLAYERWHITOUTBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a conquis la planète non défendue ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $place->rPlayer, $place->playerName)
					->addTxt('.')
					->addSep()
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addTxt('Elle est désormais votre, vous pouvez l\'administrer ')
					->addLnk('bases/base-' . $place->id, 'ici')
					->addTxt('.')
					->addEnd();
				$this->notificationManager->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($place->rPlayer);
				$notif->setTitle('Planète conquise');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a conquis votre planète non défendue ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('.')
					->addSep()
					->addTxt('Impliquez votre faction dans une action punitive envers votre assaillant.')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::CONQUERLOST:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Erreur de coordonnées');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' n\'a pas attaqué la planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt(' car le joueur est dans votre faction, sous la protection débutant ou votre allié.')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::COMEBACK:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Rapport de retour');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' est de retour sur votre base ')
					->addLnk('map/place-' . $commander->getRBase(), $commander->getBaseName())
					->addTxt(' et rapporte ')
					->addStg(Format::number($commander->getResources()))
					->addTxt(' ressources à vos entrepôts.')
					->addEnd();
				$this->notificationManager->add($notif);
				break;
			
			default: break;
		}
	}

	public function sendNotifForConquest(Place $place, string $case, Commander $commander, array $reports = []): void
	{
		$nbrBattle = count($reports);
		switch($case) {
			case Place::CONQUERPLAYERWHITBATTLESUCCESS:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête réussie');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/commander-' . $commander->getId() . '/sftr-3', $commander->getName())
					->addTxt(' a conquis la planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $place->rPlayer, $place->playerName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addBoxResource('xp', '+ ' . Format::number($commander->earnedExperience), 'expérience de l\'officier')
					->addSep()
					->addTxt('Elle est désormais vôtre, vous pouvez l\'administrer ')
					->addLnk('bases/base-' . $place->id, 'ici')
					->addTxt('.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				$this->notificationManager->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($place->rPlayer);
				$notif->setTitle('Planète conquise');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a conquis votre planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Impliquez votre faction dans une action punitive envers votre assaillant.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				$this->notificationManager->add($notif);
				break;
			case Place::CONQUERPLAYERWHITBATTLEFAIL:
				$notif = new Notification();
				$notif->setRPlayer($commander->getRPlayer());
				$notif->setTitle('Conquête ratée');
				$notif->addBeg()
					->addTxt('Votre officier ')
					->addLnk('fleet/view-memorial/', $commander->getName())
					->addTxt(' est tombé lors de la tentive de conquête de la planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $place->rPlayer, $place->playerName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Il a désormais rejoint de Mémorial. Que son âme traverse l\'Univers dans la paix.');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				$this->notificationManager->add($notif);

				$notif = new Notification();
				$notif->setRPlayer($place->rPlayer);
				$notif->setTitle('Rapport de combat');
				$notif->addBeg()
					->addTxt('L\'officier ')
					->addStg($commander->getName())
					->addTxt(' appartenant au joueur ')
					->addLnk('embassy/player-' . $commander->getRPlayer(), $commander->getPlayerName())
					->addTxt(' a tenté de conquérir votre planète ')
					->addLnk('map/place-' . $place->id, $place->baseName)
					->addTxt('.')
					->addSep()
					->addTxt($nbrBattle . Format::addPlural($nbrBattle, ' combats ont eu lieu.', ' seul combat a eu lieu'))
					->addSep()
					->addTxt('Vous avez repoussé l\'ennemi avec succès. Bravo !');
				for ($i = 0; $i < $nbrBattle; $i++) {
					$notif->addSep();
					$notif->addLnk('fleet/view-archive/report-' . $reports[$i], 'voir le ' . Format::ordinalNumber($i + 1) . ' rapport');
				}
				$notif->addEnd();
				$this->notificationManager->add($notif);
				break;

			default: break;
		}
	}
}
