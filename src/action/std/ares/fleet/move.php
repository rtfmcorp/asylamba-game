<?php
# send a fleet to move to a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place de destination

use App\Classes\Library\Utils;
use App\Classes\Library\Game;
use App\Modules\Ares\Model\Commander;
use App\Classes\Library\DataAnalysis;
use App\Modules\Athena\Resource\ShipResource;
use App\Classes\Exception\ErrorException;

$container = $this->getContainer();
$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$placeManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\PlaceManager::class);
$sectorManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\SectorManager::class);
$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');

$commanderId = $request->query->get('commanderid');
$placeId = $request->query->get('placeid');


if ($commanderId !== FALSE AND $placeId !== FALSE) {
	if (($commander = $commanderManager->get($commanderId)) !== null && $commander->rPlayer === $session->get('playerId')) {
		if (($place = $placeManager->get($placeId)) !== null) {
			if ($commander->playerColor == $place->playerColor) {
				$home = $placeManager->get($commander->getRBase());

				$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
				$duration = Game::getTimeToTravel($home, $place, $session->get('playerBonus'));
			
				if ($commander->statement === Commander::AFFECTED) {
					$sector = $sectorManager->get($place->rSector);
					$isFactionSector = ($sector->rColor == $commander->playerColor) ? TRUE : FALSE;
					
					$commander->destinationPlaceName = $place->baseName;

					if ($length <= Commander::DISTANCEMAX || $isFactionSector) {
						$commanderManager->move($commander, $place->getId(), $commander->rBase, Commander::MOVE, $length, $duration);

						if (true === $container->getParameter('data_analysis')) {
							$qr = $database->prepare('INSERT INTO 
								DA_CommercialRelation(`from`, `to`, type, weight, dAction)
								VALUES(?, ?, ?, ?, ?)'
							);
							$ships = $commander->getNbrShipByType();
							$price = 0;

							for ($i = 0; $i < count($ships); $i++) { 
								$price += DataAnalysis::resourceToStdUnit(ShipResource::getInfo($i, 'resourcePrice') * $ships[$i]);
							}

							$qr->execute([$commander->rPlayer, $place->rPlayer, 7, $price, Utils::now()]);
						}
					} else {
						throw new ErrorException('Cet emplacement est trop éloigné.');	
					}
				} else {
					throw new ErrorException('Cet officier est déjà en déplacement.');	
				}
			} else {
				throw new ErrorException('Vous ne pouvez pas envoyer une flotte sur une planète qui ne vous appartient pas.');
			}
		} else {
			throw new ErrorException('Ce lieu n\'existe pas.');
		}
	} else {
		throw new ErrorException('Ce commandant ne vous appartient pas ou n\'existe pas.');
	}
} else {
	throw new ErrorException('Manque de précision sur le commandant ou la position.');
}

$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush();