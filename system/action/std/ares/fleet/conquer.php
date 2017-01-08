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
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$placeManager = $this->getContainer()->get('gaia.place_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');
$technologyManager = $this->getContainer()->get('promethee.technology_manager');
$session = $this->getContainer()->get('app.session');
$conquestCost = $this->getContainer()->getParameter('ares.coeff.conquest_cost');

$S_COM1 = $commanderManager->getCurrentSession();
$commanderManager->newSession(ASM_UMODE);
$commanderManager->load(array('c.id' => $commanderId, 'c.rPlayer' => $session->get('playerId')));

$S_PLM1 = $placeManager->getCurrentSession();
$placeManager->newSession(ASM_UMODE);
$placeManager->load(array('id' => $placeId));

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
	$S_COM2 = $commanderManager->getCurrentSession();
	$commanderManager->newSession();
	$commanderManager->load(array('c.rPlayer' => $session->get('playerId'), 'c.statement' => Commander::MOVING));
	for ($i = 0; $i < $commanderManager->size(); $i++) { 
		if ($commanderManager->get($i)->travelType == Commander::COLO) {
			$coloQuantity++;
		}
	}
	$commanderManager->changeSession($S_COM2);
	$totalBases = $obQuantity + $msQuantity + $coloQuantity;
	if ($totalBases >= $maxBasesQuantity) {
		throw new ErrorException('Vous avez assez de conquête en cours ou un niveau d\'administration étendue trop faible.');
	}

		$targetPlayer = $playerManager->get($placeManager->get()->rPlayer);
		
		if ($targetPlayer->level > 3 || $targetPlayer->statement >= Player::DELETED) {
			if ($commanderManager->size() > 0) {
				if ($placeManager->size() > 0) {
					$commander = $commanderManager->get();
					$place = $placeManager->get();

					$_CLM1 = $colorManager->getCurrentSession();
					$colorManager->newSession();
					$colorManager->load(array('id' => $session->get('playerInfo')->get('color')));
					$color = $colorManager->get();

					if ($session->get('playerInfo')->get('color') != $place->getPlayerColor() && $color->colorLink[$targetPlayer->rColor] != Color::ALLY) {
						$placeManager->load(array('id' => $commander->getRBase()));
						$home = $placeManager->getById($commander->getRBase());

						$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
						$duration = Game::getTimeToTravel($home, $place, $session->get('playerBonus'));

						# compute price
						$price = $totalBases * $conquestCost;

						# calcul du bonus
						$_CLM46 = $colorManager->getCurrentSession();
						$colorManager->newSession();
						$colorManager->load(['id' => $session->get('playerInfo')->get('color')]);

						if (in_array(ColorResource::COLOPRICEBONUS, $colorManager->get()->bonus)) {
							$price -= round($price * ColorResource::BONUS_CARDAN_COLO / 100);
						}
						$colorManager->changeSession($_CLM46);

						if ($session->get('playerInfo')->get('credit') >= $price) {
							if ($commander->getPev() > 0) {
								if ($commander->statement == Commander::AFFECTED) {
									$S_SEM = $sectorManager->getCurrentSession();
									$sectorManager->newSession();
									$sectorManager->load(array('id' => $place->rSector));

									$_CLM2 = $colorManager->getCurrentSession();
									$colorManager->newSession();
									$colorManager->load(array('id' => $sectorManager->get()->rColor));

									$sectorColor = $colorManager->get();
									$isFactionSector = ($sectorManager->get()->rColor == $commander->playerColor || $sectorColor->colorLink[$session->get('playerInfo')->get('color')] == Color::ALLY) ? TRUE : FALSE;

									$sectorManager->changeSession($S_SEM);
									$colorManager->changeSession($_CLM2);

									if ($length <= Commander::DISTANCEMAX || $isFactionSector) {
										$commander->destinationPlaceName = $place->baseName;

										if ($commanderManager->move($commander, $place->getId(), $commander->rBase, Commander::COLO, $length, $duration)) {
											# debit credit
											$playerManager->decreaseCredit($playerManager->get($session->get('playerId')), $price);

											#throw new ErrorException('Flotte envoyée.', ALERT_STD_SUCCESS);

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
							throw new ErrorException('Vous n\'avez pas assez de crédits pour conquérir cette base.');
						}		
					} else {
						throw new ErrorException('Vous ne pouvez pas attaquer un lieu appartenant à votre Faction ou d\'une faction alliée.');
					}

					$colorManager->changeSession($_CLM1);
				} else {
					throw new ErrorException('Ce lieu n\'existe pas.');
				}
			} else {
				throw new ErrorException('Ce commandant ne vous appartient pas ou n\'existe pas.');
			}
		} else {
			throw new ErrorException('Vous ne pouvez pas conquérir un joueur de niveau 3 ou moins.');
		}
$placeManager->changeSession($S_PLM1);
$commanderManager->changeSession($S_COM1);