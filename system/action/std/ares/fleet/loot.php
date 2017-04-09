<?php
# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Zeus\Resource\TutorialResource;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');
$tutorialHelper = $this->getContainer()->get('zeus.tutorial_helper');

$commanderId = $request->query->get('commanderid');
$placeId = $request->query->get('placeid');

if ($commanderId !== FALSE AND $placeId !== FALSE) {
	$place = $placeManager->get($placeId);
	if (($player = $playerManager->get($place->rPlayer)) === null) {
		if (($commander = $commanderManager->get($commanderId)) !== null && $commander->rPlayer === $session->get('playerId')) {
			if ($place !== null) {
				if ($place->typeOfPlace == Place::TERRESTRIAL) {
					if ($session->get('playerInfo')->get('color') != $place->getPlayerColor()) {
						$home = $placeManager->get($commander->getRBase());

						$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
						$duration = Game::getTimeToTravel($home, $place, $session->get('playerBonus'));

						if ($commander->getPev() > 0) {
							if ($commander->statement == Commander::AFFECTED) {

								$sector = $sectorManager->get($place->rSector);

								$sectorColor = $colorManager->get($sector->rColor);
								$isFactionSector = ($sector->rColor == $commander->playerColor || $sectorColor->colorLink[$session->get('playerInfo')->get('color')] == Color::ALLY) ? TRUE : FALSE;
								
								$commander->destinationPlaceName = $place->baseName;
								if ($length <= Commander::DISTANCEMAX || $isFactionSector) {
									if ($commanderManager->move($commander, $place->getId(), $commander->rBase, Commander::LOOT, $length, $duration)) {

										# tutorial
										if ($session->get('playerInfo')->get('stepDone') == FALSE &&
											$session->get('playerInfo')->get('stepTutorial') === TutorialResource::LOOT_PLANET) {
												$tutorialHelper->setStepDone();
										}
										
										if ($request->query->has('redirect')) {
											$response->redirect('map/place-' . $request->query->get('redirect'));
										}
									}
								} else {
									throw new ErrorException('Cet emplacement est trop éloigné.');
								}
							} else {
								throw new ErrorException('Cet officier est déjà en déplacement.');
							}
						} else {
							throw new ErrorException('Vous devez affecter au moins un vaisseau à votre officier.');
						}		
					} else {
						throw new ErrorException('Vous ne pouvez pas attaquer un lieu appartenant à votre Faction.');
					}
				} else {
					throw new ErrorException('Ce lieu n\'est pas habité.');
				}
			} else {
				throw new ErrorException('Ce lieu n\'existe pas.');
			}
		} else {
			throw new ErrorException('Ce commandant ne vous appartient pas ou n\'existe pas.');
		}
	} elseif ($player->level > 1 || $player->statement >= Player::DELETED) {
		if (($commander = $commanderManager->get($commanderId)) !== null && $commander->rPlayer === $session->get('playerId')) {
			if ($place !== null) {
				$color = $colorManager->get($session->get('playerInfo')->get('color'));
				
				if ($session->get('playerInfo')->get('color') != $place->getPlayerColor() && $color->colorLink[$player->rColor] != Color::ALLY) {
					$home = $placeManager->get($commander->getRBase());

					$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
					$duration = Game::getTimeToTravel($home, $place, $session->get('playerBonus'));

					if ($commander->getPev() > 0) {
						$sector = $sectorManager->get($place->rSector);

						$isFactionSector = ($sector->rColor == $commander->playerColor || $sectorColor->colorLink[$session->get('playerInfo')->get('color')] == Color::ALLY) ? TRUE : FALSE;
						
						$commander->destinationPlaceName = $place->baseName;
						if ($length <= Commander::DISTANCEMAX || $isFactionSector) {
							if ($commanderManager->move($commander, $place->getId(), $commander->rBase, Commander::LOOT, $length, $duration)) {
								$commander->dStart = Utils::now();
								$session->addFlashbag('Flotte envoyée.', Flashbag::TYPE_SUCCESS);

								if ($request->query->has('redirect')) {
									$response->redirect('map/place-' . $request->query->get('redirect'));
								}
							}
						} else {
							throw new ErrorException('Ce lieu est trop éloigné.');		
						}
					} else {
						throw new ErrorException('Vous devez affecter au moins un vaisseau à votre officier.');	
					}	
				} else {
					throw new ErrorException('Vous ne pouvez pas attaquer un lieu appartenant à votre Faction ou d\'une faction alliée.');
				}
			} else {
				throw new ErrorException('Ce lieu n\'existe pas.');
			}
		} else {
			throw new ErrorException('Ce commandant ne vous appartient pas ou n\'existe pas.');
		}
	} else {
		throw new ErrorException('Vous ne pouvez pas piller un joueur de niveau 1.');
	}
} else {
	throw new ErrorException('Manque de précision sur le commandant ou la position.');
}

$this->getContainer()->get('entity_manager')->flush();