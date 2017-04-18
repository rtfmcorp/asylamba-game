<?php
# send a fleet to move to a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place de destination

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Classes\Library\DataAnalysis;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Classes\Exception\ErrorException;

$commanderManager = $this->getContainer()->get('ares.commander_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');
$database = $this->getContainer()->get('database');
$session = $this->getContainer()->get('app.session');
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

						if (DATA_ANALYSIS) {
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

$this->getContainer()->get('entity_manager')->flush();