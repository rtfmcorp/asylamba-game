<?php

# send a fleet to loot a place

# int commanderid 			id du commandant à envoyer
# int placeid				id de la place attaquée

use App\Classes\Library\Game;
use App\Modules\Promethee\Model\Technology;
use App\Modules\Ares\Model\Commander;
use App\Modules\Gaia\Model\Place;
use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Demeter\Model\Color;
use App\Classes\Exception\ErrorException;

$commanderManager = $this->getContainer()->get(\App\Modules\Ares\Manager\CommanderManager::class);
$placeManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\PlaceManager::class);
$technologyManager = $this->getContainer()->get(\App\Modules\Promethee\Manager\TechnologyManager::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$colorManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\ColorManager::class);
$sectorManager = $this->getContainer()->get(\App\Modules\Gaia\Manager\SectorManager::class);
$database = $this->getContainer()->get(\App\Classes\Database\Database::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$request = $this->getContainer()->get('app.request');
$colonizationCost = $this->getContainer()->getParameter('ares.coeff.colonization_cost');
$conquestCost = $this->getContainer()->getParameter('ares.coeff.conquest_cost');

$commanderId = $request->query->get('commanderid');
$placeId = $request->query->get('placeid');


if ($commanderId !== FALSE AND $placeId !== FALSE) {
	# load the technologies
	$technologies = $technologyManager->getPlayerTechnology($session->get('playerId'));

	# check si technologie CONQUEST débloquée
	if ($technologies->getTechnology(Technology::COLONIZATION) == 1) {
		# check si la technologie BASE_QUANTITY a un niveau assez élevé
		$maxBasesQuantity = $technologies->getTechnology(Technology::BASE_QUANTITY) + 1;
		$obQuantity = $session->get('playerBase')->get('ob')->size();

		# count ob quantity via request to be sure (the session is sometimes not valid)
		$qr = $database->prepare('SELECT COUNT(*) AS count FROM `orbitalBase` WHERE `rPlayer`=?'); 
		$qr->execute([$session->get('playerId')]);
		$aw = $qr->fetch();
		$obQuantity = $aw['count'];

		$coloQuantity = 0;
		$commanders = $commanderManager->getPlayerCommanders($session->get('playerId'), [Commander::MOVING]);
		foreach ($commanders as $commander) { 
			if ($commander->travelType == Commander::COLO) {
				$coloQuantity++;
			}
		}
		$totalBases = $obQuantity + $coloQuantity;
		if ($totalBases < $maxBasesQuantity) {
			if (($commander = $commanderManager->get($commanderId)) !== null && $commander->rPlayer = $session->get('playerId')) {
				if (($place = $placeManager->get($placeId)) !== null) {
					if ($place->typeOfPlace == Place::TERRESTRIAL) {

						$home = $placeManager->get($commander->getRBase());

						$length = Game::getDistance($home->getXSystem(), $place->getXSystem(), $home->getYSystem(), $place->getYSystem());
						$duration = Game::getTimeToTravel($home, $place, $session->get('playerBonus'));

						# compute price
						$price = $totalBases * $colonizationCost;

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
                                            
											if ($request->query->has('redirect')) {
												$this->getContainer()->get('app.response')->redirect('map/place-' . $request->query->get('redirect'));
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
							throw new ErrorException('Vous n\'avez pas assez de crédits pour coloniser cette planète.');
						}
					} else {
						throw new ErrorException('Ce lieu n\'est pas habitable.');
					}
				} else {
					throw new ErrorException('Ce lieu n\'existe pas.');
				}
			} else {
				throw new ErrorException('Ce commandant ne vous appartient pas ou n\'existe pas.');
			}
		} else {
			throw new ErrorException('Vous avez assez de conquête en cours ou un niveau administration étendue trop bas.');
		}
	} else {
		throw new ErrorException('Vous devez développer votre technologie colonisation.');
	}
} else {
	throw new ErrorException('Manque de précision sur le commandant ou la position.');
}

$this->getContainer()->get(\App\Classes\Entity\EntityManager::class)->flush();
