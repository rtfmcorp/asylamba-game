<?php
# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$request = $this->getContainer()->get('app.request');

if (($commanderId = $request->query->get('commanderid')) === null || ($placeId = $request->query->get('placeid')) === null) {
	throw new ErrorException('Manque de précision sur le commandant ou la position.');
}

$response = $this->getContainer()->get('app.response');
$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$playerManager = $this->getContainer()->get(\Asylamba\Modules\Zeus\Manager\PlayerManager::class);
$placeManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\PlaceManager::class);
$colorManager = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class);
$sectorManager = $this->getContainer()->get(\Asylamba\Modules\Gaia\Manager\SectorManager::class);
$technologyManager = $this->getContainer()->get(\Asylamba\Modules\Promethee\Manager\TechnologyManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$conquestCost = $this->getContainer()->getParameter('ares.coeff.conquest_cost');

$place = $placeManager->get($placeId);

	// load the technologies
$technologies = $technologyManager->getPlayerTechnology($session->get('playerId'));

# check si technologie CONQUEST débloquée
if ($technologies->getTechnology(Technology::CONQUEST) !== 1) {
	throw new ErrorException('Vous devez débloquer la technologie de conquête.');
}
	# check si la technologie BASE_QUANTITY a un niveau assez élevé
	$maxBasesQuantity = $technologies->getTechnology(Technology::BASE_QUANTITY) + 1;
	$obQuantity = $session->get('playerBase')->get('ob')->size();
	$msQuantity = $session->get('playerBase')->get('ms')->size();
	$coloQuantity = 0;
	$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::MOVING]);
	foreach ($commanders as $commander) { 
		if ($commander->travelType == Commander::COLO) {
			$coloQuantity++;
		}
	}
	$totalBases = $obQuantity + $msQuantity + $coloQuantity;
	if ($totalBases >= $maxBasesQuantity) {
		throw new ErrorException('Vous avez assez de conquête en cours ou un niveau d\'administration étendue trop faible.');
	}

		$targetPlayer = $playerManager->get($place->rPlayer);
		
		if ($targetPlayer->level > 3 || $targetPlayer->statement >= Player::DELETED) {
			if (($commander = $commanderManager->get($commanderId)) !== null && $commander->rPlayer = $session->get('playerId')) {
				if ($place !== null) {

					$color = $colorManager->get($session->get('playerInfo')->get('color'));

					if ($session->get('playerInfo')->get('color') != $place->getPlayerColor() && $color->colorLink[$targetPlayer->rColor] != Color::ALLY) {
						$home = $placeManager->get($commander->getRBase());

						$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
						$duration = Game::getTimeToTravel($home, $place, $session->get('playerBonus'));

						# compute price
						$price = $totalBases * $conquestCost;

						# calcul du bonus
						if (in_array(ColorResource::COLOPRICEBONUS, $colorManager->get($session->get('playerInfo')->get('color'))->bonus)) {
							$price -= round($price * ColorResource::BONUS_CARDAN_COLO / 100);
						}

						if ($session->get('playerInfo')->get('credit') >= $price) {
							if ($commander->getPev() > 0) {
								if ($commander->statement == Commander::AFFECTED) {
									$sector = $sectorManager->get($place->rSector);

									$sectorColor = $colorManager->get($sector->rColor);
									$isFactionSector = ($sector->rColor == $commander->playerColor || $sectorColor->colorLink[$session->get('playerInfo')->get('color')] == Color::ALLY) ? TRUE : FALSE;

									if ($length <= Commander::DISTANCEMAX || $isFactionSector) {
										$commander->destinationPlaceName = $place->baseName;

										$commanderManager->move($commander, $place->getId(), $commander->rBase, Commander::COLO, $length, $duration) ;
										# debit credit
										$playerManager->decreaseCredit($playerManager->get($session->get('playerId')), $price);

										#throw new ErrorException('Flotte envoyée.', ALERT_STD_SUCCESS);

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
							throw new ErrorException('Vous n\'avez pas assez de crédits pour conquérir cette base.');
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
			throw new ErrorException('Vous ne pouvez pas conquérir un joueur de niveau 3 ou moins.');
		}
$this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class)->flush();
