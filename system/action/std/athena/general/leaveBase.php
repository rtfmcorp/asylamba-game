<?php
// supprime toutes les routes
// 	-> avec message

# int id 		id (rPlace) de la base orbitale

use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Event\PlaceOwnerChangeEvent;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$orbitalBaseManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\OrbitalBaseManager::class);
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);
$placeManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\PlaceManager::class);
$commercialRouteManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialRouteManager::class);
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);
$eventDispatcher = $this->getContainer()->get('event_dispatcher');

$baseId = $request->query->get('id');

for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('id');
}

if (count($verif) > 1) {
	$baseCommanders = $commanderManager->getBaseCommanders($baseId);
	$areAllFleetImmobile = TRUE;
	// @TODO Break when expected result is found in the loop
	foreach ($baseCommanders as $commander) {
		if ($commander->statement == Commander::MOVING) {
			$areAllFleetImmobile = FALSE;
		}
	}
	if ($areAllFleetImmobile) {
		if ($baseId != FALSE && in_array($baseId, $verif)) {
			if (($base = $orbitalBaseManager->getPlayerBase($baseId, $session->get('playerId'))) !== null) {
				if (Utils::interval(Utils::now(), $base->dCreation, 'h') >= OrbitalBase::COOL_DOWN) {

					# delete buildings in queue
					foreach ($base->buildingQueues as $buildingQueue) {
						$entityManager->remove($buildingQueue);
					}

					# change base type if it is a capital
					if ($base->typeOfBase == OrbitalBase::TYP_CAPITAL) {
						if (rand(0,1) == 0) {
							$newType = OrbitalBase::TYP_COMMERCIAL;
						} else {
							$newType = OrbitalBase::TYP_MILITARY;
						}
						# delete extra buildings
						for ($i = 0; $i < OrbitalBaseResource::BUILDING_QUANTITY; $i++) { 
							$maxLevel = $orbitalBaseHelper->getBuildingInfo($i, 'maxLevel', $newType);
							if ($base->getBuildingLevel($i) > $maxLevel) {
								$base->setBuildingLevel($i, $maxLevel);
							}
						}
						# change base type
						$base->typeOfBase = $newType;
					}
					$place = $placeManager->get($baseId);

					$orbitalBaseManager->changeOwnerById($baseId, $base, ID_GAIA, $baseCommanders);
					$place->rPlayer = ID_GAIA;
					$entityManager->flush();
					$eventDispatcher->dispatch(new PlaceOwnerChangeEvent($place), PlaceOwnerChangeEvent::NAME);
					
					for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
						if ($verif[$i] == $baseId) {
							unset($verif[$i]);
							$verif = array_merge($verif);
						}
					}
					$session->addFlashbag('Base abandonnée', Flashbag::TYPE_SUCCESS);
					$response->redirect(Format::actionBuilder('switchbase', $session->get('token'), ['base' => $verif[0]], FALSE));
				} else {
					throw new ErrorException('Vous ne pouvez pas abandonner de base dans les ' . OrbitalBase::COOL_DOWN . ' premières relèves.');	
				}
			} else {
				throw new ErrorException('cette base ne vous appartient pas');	
			}
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
	} else {
		throw new ErrorException('toute les flottes de cette base doivent être immobiles');
	}
} else {
	throw new ErrorException('vous ne pouvez pas abandonner votre unique planète');
}
