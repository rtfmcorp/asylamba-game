<?php
# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

use App\Classes\Library\Utils;
use App\Classes\Library\Flashbag;
use App\Classes\Library\Game;
use App\Modules\Ares\Model\Commander;
use App\Modules\Gaia\Model\Place;
use App\Modules\Zeus\Resource\TutorialResource;
use App\Modules\Demeter\Model\Color;
use App\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$commanderManager = $this->getContainer()->get(\App\Modules\Ares\Manager\CommanderManager::class);
$placeManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\PlaceManager::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$colorManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\ColorManager::class);
$sectorManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\SectorManager::class);
$tutorialHelper = $this->getContainer()->get(\App\Modules\Zeus\Helper\TutorialHelper::class);

$commanderId = $request->query->get('commanderid');
$placeId = $request->query->get('placeid');

if ($commanderId !== FALSE AND $placeId !== FALSE) {
	$place = $placeManager->get($placeId);
	if (null === $place->rPlayer || ($player = $playerManager->get($place->rPlayer)) === null) {
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
									$commanderManager->move($commander, $place->getId(), $commander->rBase, Commander::LOOT, $length, $duration) ;
 									$session->addFlashbag('Flotte envoyée.', Flashbag::TYPE_SUCCESS);
									# tutorial
									if ($session->get('playerInfo')->get('stepDone') == FALSE &&
											$session->get('playerInfo')->get('stepTutorial') === TutorialResource::LOOT_PLANET) {
											$tutorialHelper->setStepDone();
									}
										
									if ($request->query->has('redirect')) {
										$response->redirect('map/place-' . $request->query->get('redirect'));
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
	} elseif ($player->level > 1 || $player->statement >= \App\Modules\Zeus\Model\Player::DELETED) {
		if (($commander = $commanderManager->get($commanderId)) !== null && $commander->rPlayer === $session->get('playerId')) {
			if ($place !== null) {
				$color = $colorManager->get($session->get('playerInfo')->get('color'));
				
				if ($session->get('playerInfo')->get('color') != $place->getPlayerColor() && $color->colorLink[$player->rColor] != Color::ALLY) {
					$home = $placeManager->get($commander->getRBase());

					$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
					$duration = Game::getTimeToTravel($home, $place, $session->get('playerBonus'));

					if ($commander->getPev() > 0) {
						$sector = $sectorManager->get($place->rSector);
						$sectorColor = $colorManager->get($sector->rColor);

						$isFactionSector = ($sector->rColor == $commander->playerColor || $sectorColor->colorLink[$session->get('playerInfo')->get('color')] == Color::ALLY) ? TRUE : FALSE;
						
						$commander->destinationPlaceName = $place->baseName;
						if ($length <= Commander::DISTANCEMAX || $isFactionSector) {
							$commanderManager->move($commander, $place->getId(), $commander->rBase, Commander::LOOT, $length, $duration) ;								
							$session->addFlashbag('Flotte envoyée.', Flashbag::TYPE_SUCCESS);

							if ($request->query->has('redirect')) {
								$response->redirect('map/place-' . $request->query->get('redirect'));
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

$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush();
