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

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$placeManager = $this->getContainer()->get('demeter.place_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');
$buildingQueueManager = $this->getContainer()->get('athena.building_queue_manager');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');

$baseId = $request->query->get('id');

for ($i = 0; $i < $notificationManager->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = $notificationManager->get('playerBase')->get('ob')->get($i)->get('id');
}

if (count($verif) > 1) {
	$_COM = $commanderManager->getCurrentSession();
	$commanderManager->newSession();
	$commanderManager->load(array('rBase' => $baseId));
	$areAllFleetImmobile = TRUE;
	for ($i = 0; $i < $commanderManager->size(); $i++) {
		if ($commanderManager->get($i)->statement == Commander::MOVING) {
			$areAllFleetImmobile = FALSE;
		}
	}
	if ($areAllFleetImmobile) {
		if ($baseId != FALSE && in_array($baseId, $verif)) {
			$_OBM = $orbitalBaseManager->getCurrentSession();
			$orbitalBaseManager->newSession();
			$orbitalBaseManager->load(array('rPlace' => $baseId, 'rPlayer' => $notificationManager->get('playerId')));

			if ($orbitalBaseManager->size() > 0) {
				$base = $orbitalBaseManager->get();

				if (Utils::interval(Utils::now(), $base->dCreation, 'h') >= OrbitalBase::COOL_DOWN) {

					# delete buildings in queue
					$S_BQM1 = $buildingQueueManager->getCurrentSession();
					$buildingQueueManager->newSession(ASM_UMODE);
					$buildingQueueManager->load(array('rOrbitalBase' => $baseId), array('dEnd'));
					for ($i = $buildingQueueManager->size() - 1; $i >= 0; $i--) {
						$buildingQueueManager->deleteById($buildingQueueManager->get($i)->id);
					}
					$buildingQueueManager->changeSession($S_BQM1);

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

					$_PLM = $placeManager->getCurrentSession();
					$placeManager->newSession();
					$placeManager->load(array('id' => $baseId));

					$S_CRM1 = $commercialRouteManager->getCurrentSession();
					$commercialRouteManager->newSession();
					$commercialRouteManager->load(array('rOrbitalBase' => $baseId));
					$commercialRouteManager->load(array('rOrbitalBaseLinked' => $baseId));
					$S_CRM2 = $commercialRouteManager->getCurrentSession();
					$commercialRouteManager->changeSession($S_CRM1);

					$S_REM1 = $recyclingMissionManager->getCurrentSession();
					$recyclingMissionManager->newSession();
					$recyclingMissionManager->load(array('rBase' => $baseId));
					$S_REM2 = $recyclingMissionManager->getCurrentSession();
					$recyclingMissionManager->changeSession($S_REM1);

					$S_COM2 = $commanderManager->getCurrentSession();
					$commanderManager->newSession(FALSE); # FALSE obligatory, else the umethod make shit
					$commanderManager->load(array('c.rBase' => $baseId));
					$S_COM3 = $commanderManager->getCurrentSession();
					$commanderManager->changeSession($S_COM2);

					$orbitalBaseManager->changeOwnerById($baseId, $base, ID_GAIA, $S_CRM2, $S_REM2, $S_COM3);
					$placeManager->get()->rPlayer = ID_GAIA;

					$placeManager->changeSession($_PLM);
					
					for ($i = 0; $i < $notificationManager->get('playerBase')->get('ob')->size(); $i++) { 
						if ($verif[$i] == $baseId) {
							unset($verif[$i]);
							$verif = array_merge($verif);
						}
					}
					$session->addFlashbag('Base abandonnée', Flashbag::TYPE_SUCCESS);
					$response->redirect(Format::actionBuilder('switchbase', $sessionToken, ['base' => $verif[0]], FALSE));
				} else {
					throw new ErrorException('Vous ne pouvez pas abandonner de base dans les ' . OrbitalBase::COOL_DOWN . ' premières relèves.');	
				}
			} else {
				throw new ErrorException('cette base ne vous appartient pas');	
			}
			$orbitalBaseManager->changeSession($_OBM);
		} else {
			throw new ErrorException('cette base ne vous appartient pas');
		}
	} else {
		throw new ErrorException('toute les flottes de cette base doivent être immobiles');
	}
	$commanderManager->changeSession($_COM);
} else {
	throw new ErrorException('vous ne pouvez pas abandonner votre unique planète');
}
