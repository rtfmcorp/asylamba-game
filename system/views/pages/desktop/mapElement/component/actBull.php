<?php

use Asylamba\Classes\Library\Chronos;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Athena\Model\RecyclingMission;
use Asylamba\Modules\Ares\Model\Commander;

$session = $this->getContainer()->get('app.session');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');
$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$sessionToken = $session->get('token');

# display part
echo '<div class="column act">';
	echo '<div class="top">';
		if ($place->typeOfPlace == 1) {
			$available = (($place->rPlayer != 0 && $place->playerColor != $session->get('playerInfo')->get('color')) || ($place->rPlayer == 0 && $place->typeOfPlace == 1)) ? NULL : 'grey';
			echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="1"><img src="' . MEDIA . 'map/action/loot.png" alt="" /></a>';
			echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="2"><img src="' . MEDIA . 'map/action/colo.png" alt="" /></a>';

			$available = (($place->rPlayer == $session->get('playerId') && $place->getId() != $defaultBase->getId()) || ($place->playerColor == $session->get('playerInfo')->get('color'))) ? NULL : 'grey';
			echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="3"><img src="' . MEDIA . 'map/action/move.png" alt="" /></a>';

			$available = ($place->rPlayer != 0 && $place->getId() != $defaultBase->getId()) ? NULL : 'grey';
			echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="4"><img src="' . MEDIA . 'map/action/rc.png" alt="" /></a>';

			$available = (($place->rPlayer != 0 && $place->playerColor != $session->get('playerInfo')->get('color')) || ($place->rPlayer == 0 && $place->typeOfPlace == 1)) ? NULL : 'grey';
			echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="5"><img src="' . MEDIA . 'map/action/spy.png" alt="" /></a>';
		} else {
			$available = ($place->sectorColor == $session->get('playerInfo')->get('color') || $place->sectorColor == ColorResource::NO_FACTION) ? NULL : 'grey';
			echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="1"><img src="' . MEDIA . 'orbitalbase/recycler.png" alt=""></a>';
		}
	echo '</div>';
	
	echo '<div class="bottom">';
		if ($place->typeOfPlace == 1) {
		echo '<div class="box" data-id="1">';
			echo '<h2>Lancer un pillage</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer == 0 && $place->typeOfPlace != 1) {
					echo 'Vous ne pouvez pas attaquer une planète non-habitable';
				} elseif ($place->typeOfPlace == 1 && $place->playerColor == $session->get('playerInfo')->get('color')) {
					echo 'Vous ne pouvez pas attaquer un joueur de votre faction';
				} elseif ($place->typeOfPlace == 1 && $place->playerLevel == 1 && !$place->playerColor == 0) {
					echo 'Ce joueur est sous protection débutant';
				} else {
					echo '<div class="commander-tile">';
						echo '<div class="item no-commander">';
							echo 'Aucun commandant selectionné. Sélectionnez-en un sur la barre latérale gauche.<br/><br />Si aucun commandant n\'est visible, vous pouvez en affecter un depuis l\'école de commandement.';
						echo '</div>';
						echo '<div class="item too-far">';
							echo 'Ce commandant est trop éloigné pour piller cette planète.';
						echo '</div>';
						echo '<div class="item move">';
							echo '<strong class="name"></strong><br />';
							echo 'Temps de l\'attaque : ' . Chronos::secondToFormat(Game::getTimeTravel($defaultBase->system, $defaultBase->position, $defaultBase->xSystem, $defaultBase->ySystem, $place->rSystem, $place->position, $place->xSystem, $place->ySystem, $session->get('playerBonus')), 'lite') . ' <img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="" /><br />';
							echo 'Capacité de la soute : <span class="wedge"></span> <img src="' . MEDIA . 'resources/resource.png" class="icon-color" alt="" /><br />';
							echo '<a class="button" href="#" data-url="' . Format::actionBuilder('loot', $sessionToken, ['commanderid' => '{id}', 'placeid' => $place->id]) . '">Lancer l\'attaque</a>';
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		$maxBasesQuantity = $technologies->getTechnology(Technology::BASE_QUANTITY) + 1;
		$obQuantity = $session->get('playerBase')->get('ob')->size();
		$msQuantity = $session->get('playerBase')->get('ms')->size();
		$coloQuantity = 0;

		$S_COM3 = $commanderManager->getCurrentSession();
		$commanderManager->changeSession($movingCommandersSession);
		for ($j = 0; $j < $commanderManager->size(); $j++) { 
			if ($commanderManager->get($j)->getTypeOfMove() == Commander::COLO) {
				$coloQuantity++;
			}
		}
		$commanderManager->changeSession($S_COM3);

		$totalBases = $obQuantity + $msQuantity + $coloQuantity;

		echo '<div class="box" data-id="2">';
			echo '<h2>Lancer une ' . ($place->rPlayer == 0 ? 'colonisation' : 'conquête') . '</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer == 0 && $place->typeOfPlace != 1) {
					echo 'Vous ne pouvez pas coloniser une planète non-habitable.';
				} elseif ($place->typeOfPlace == 1 && $place->playerColor == $session->get('playerInfo')->get('color')) {
					echo 'Vous ne pouvez pas conquérir un joueur de votre faction.';
				} elseif ($place->typeOfPlace == 1 && $place->playerLevel <= 3 && $place->playerLevel != 0 && !$place->playerColor == 0) {
					echo 'Vous ne pouvez pas conquérir un joueur de niveau 3 ou inférieur.';
				} elseif ($place->rPlayer == 0 && $technologies->getTechnology(Technology::COLONIZATION) == 0) {
					echo 'Vous devez développer la technologie colonisation.';
				} elseif ($place->rPlayer != 0 && $technologies->getTechnology(Technology::CONQUEST) == 0) {
					echo 'Vous devez développer la technologie conquête.';
				} elseif ($totalBases >= $maxBasesQuantity) {
					echo 'Vous devez améliorer le niveau de la technologie administration étendue pour disposer de planète supplémentaire.';
				} else {
					echo '<div class="commander-tile">';
						echo '<div class="item no-commander">';
							echo 'Aucun commandant selectionné. Sélectionnez-en un sur la barre latérale gauche.<br/><br />Si aucun commandant n\'est visible, vous pouvez en affecter un depuis l\'école de commandement.';
						echo '</div>';
						echo '<div class="item too-far">';
							echo 'Ce commandant est trop éloigné pour coloniser cette planète.';
						echo '</div>';
						echo '<div class="item move">';
							echo '<strong class="name"></strong><br />';
							echo 'Temps de l\'attaque : ' . Chronos::secondToFormat(Game::getTimeTravel($defaultBase->system, $defaultBase->position, $defaultBase->xSystem, $defaultBase->ySystem, $place->rSystem, $place->position, $place->xSystem, $place->ySystem, $session->get('playerBonus')), 'lite') . ' <img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="" /><br />';

							if ($place->rPlayer == 0) {
								$price = $totalBases * CREDITCOEFFTOCOLONIZE;
								if ($session->get('playerInfo')->get('color') == ColorResource::CARDAN) {
									# bonus if the player is from Cardan
									$price -= round($price * ColorResource::BONUS_CARDAN_COLO / 100);
								}
								echo 'Coût : <span class="price">' . Format::numberFormat($price) . '</span> <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="" /><br />';
								echo '<a class="button" href="#" data-url="' . Format::actionBuilder('colonize', $sessionToken, ['commanderid' => '{id}', 'placeid' => $place->id]) . '">Lancer la colonisation</a>';
							} else {
								$price = $totalBases * CREDITCOEFFTOCONQUER;
								if ($session->get('playerInfo')->get('color') == ColorResource::CARDAN) {
									# bonus if the player is from Cardan
									$price -= round($price * ColorResource::BONUS_CARDAN_COLO / 100);
								}
								echo 'Coût : <span class="price">' . Format::numberFormat($price) . '</span> <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="" /><br />';
								echo '<a class="button" href="#" data-url="' . Format::actionBuilder('conquer', $sessionToken, ['commanderid' => '{id}', 'placeid' => $place->id]) . '">Lancer la conquête</a>';
							}
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="box" data-id="3">';
			echo $place->rPlayer == $defaultBase->rPlayer
				? '<h2>Déplacer une flotte</h2>'
				: '<h2>Donner votre flotte</h2>';

			echo '<div class="box-content">';
				if ($place->getId() == $defaultBase->getId()) {
					echo 'Vous ne pouvez pas déplacer une flotte sur votre planète de départ';
				} elseif ($place->playerColor != $session->get('playerInfo')->get('color')) {
					echo 'Vous ne pouvez donner une de vos flotte qu\'a un membre de votre faction';
				} else {
					echo '<div class="commander-tile">';
						echo '<div class="item no-commander">';
							echo 'Aucun commandant selectionné. Sélectionnez-en un sur la barre latérale gauche.<br/><br />Si aucun commandant n\'est visible, vous pouvez en affecter un depuis l\'école de commandement.';
						echo '</div>';
						echo '<div class="item too-far">';
							echo 'Ce commandant est trop éloigné pour se déplacer jusqu\'ici.';
						echo '</div>';
						echo '<div class="item move">';
							echo '<strong class="name"></strong><br />';
							echo 'Temps du déplacement : ' . Chronos::secondToFormat(Game::getTimeTravel($defaultBase->system, $defaultBase->position, $defaultBase->xSystem, $defaultBase->ySystem, $place->rSystem, $place->position, $place->xSystem, $place->ySystem, $session->get('playerBonus')), 'lite') . ' <img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="" /><br />';
							if ($place->rPlayer != $defaultBase->rPlayer) {
								echo 'Attention, vous perdrez votre flotte !<br />';
							}
							echo '<a class="button" href="#" data-url="' . Format::actionBuilder('movefleet', $sessionToken, ['commanderid' => '{id}', 'placeid' => $place->id]) . '">Lancer la mission</a>';
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="box" data-id="4">';
			echo '<h2>Proposer une route commerciale</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer == 0) {
					echo 'Vous ne pouvez proposer une route commerciale qu\'à des joueurs.';
				} elseif ($place->getId() == $defaultBase->getId()) {
					echo 'Vous ne pouvez pas proposer une route commerciale sur votre propre base.';
				} elseif ($defaultBase->levelSpatioport == 0) {
					echo 'Il vous faut un spatioport pour proposer une route commerciale.';
				} elseif ($place->levelSpatioport == 0) {
					echo 'Le joueur ne dispose pas d\'un spatioport.';
				} else {
					$proposed 	 = FALSE;
					$notAccepted = FALSE;
					$standby 	 = FALSE;

					$S_CRM3 = $commercialRouteManager->getCurrentSession();
					$commercialRouteManager->changeSession($defaultBase->routeManager);
					for ($j = 0; $j < $commercialRouteManager->size(); $j++) { 
						if ($commercialRouteManager->get($j)->getROrbitalBaseLinked() == $defaultBase->getRPlace()) {
							if ($commercialRouteManager->get($j)->getROrbitalBase() == $place->getId()) {
								switch($commercialRouteManager->get($j)->getStatement()) {
									case CommercialRoute::PROPOSED: $notAccepted = TRUE; break;
									case CommercialRoute::ACTIVE: $sendResources = TRUE; break;
									case CommercialRoute::STANDBY: $standby = TRUE; break;
								}
							}
						}
						if ($commercialRouteManager->get($j)->getROrbitalBase() == $defaultBase->getRPlace()) {
							if ($commercialRouteManager->get($j)->getROrbitalBaseLinked() == $place->getId()) {
								switch($commercialRouteManager->get($j)->getStatement()) {
									case CommercialRoute::PROPOSED: $proposed = TRUE; break;
									case CommercialRoute::ACTIVE: $sendResources = TRUE; break;
									case CommercialRoute::STANDBY: $standby = TRUE; break;
								}
							}
						}
					}
					$commercialRouteManager->changeSession($S_CRM3);

					$distance = Game::getDistance($defaultBase->xSystem, $place->xSystem, $defaultBase->ySystem, $place->ySystem);

					$bonusA = ($defaultBase->sector != $place->rSector) ? CRM_ROUTEBONUSSECTOR : 1;
					$bonusB = ($session->get('playerInfo')->get('color')) != $place->playerColor ? CRM_ROUTEBONUSCOLOR : 1;

					$price = Game::getRCPrice($distance);
					$income = Game::getRCIncome($distance, $bonusA, $bonusB);

					echo '<div class="rc">';
						echo '<img src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '-' . Game::getSizeOfPlanet($place->population) . '.png" alt="" class="planet" />';

						echo '<span class="label-box">';
							echo '<span class="key">Revenu par relève</span>';
							echo '<span class="val">' . Format::numberFormat($income) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" /></span>';
						echo '</span>';

						if ($session->get('playerInfo')->get('color') == ColorResource::NEGORA) {
							# bonus if the player is from Negore
							$price -= round($price * ColorResource::BONUS_NEGORA_ROUTE / 100);
						}
						echo '<span class="label-box">';
							echo '<span class="key">Coût de construction</span>';
							echo '<span class="val">' . Format::numberFormat($price) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" /></span>';
						echo '</span>';

						if ($proposed) {
							echo '<a href="' . APP_ROOT . 'bases/view-spatioport" class="button">Annuler la proposition</a>';
						} elseif ($notAccepted) {
							echo '<a href="' . APP_ROOT . 'bases/view-spatioport" class="button">Accepter la proposition</a>';
						} elseif ($standby) {
							echo '<span class="button">C\'est la guerre.</span>';
						} else {
							$S_CRM2 = $commercialRouteManager->getCurrentSession();
							$commercialRouteManager->changeSession($defaultBase->routeManager);
							$ur = $commercialRouteManager->size();
							for ($j = 0; $j < $commercialRouteManager->size(); $j++) {
								if ($commercialRouteManager->get($j)->getROrbitalBaseLinked() == $defaultBase->rPlace && $commercialRouteManager->get($j)->statement == CommercialRoute::PROPOSED) {
									$ur--;
								}
							}

							if ($price > $session->get('playerInfo')->get('credit')) {
								echo '<span class="button">Vous n\'avez pas assez de crédits.</span>';
							} elseif ($ur < $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $defaultBase->levelSpatioport, 'nbRoutesMax')) {
								echo '<a href="' . Format::actionBuilder('proposeroute', $sessionToken, ['basefrom' => $defaultBase->getId(), 'baseto' => $place->getId()]) . '" class="button">Proposer une route commerciale</a>';
							} else {
								echo '<span class="button">Pas assez de slots.</span>';
							}

							$commercialRouteManager->changeSession($S_CRM2);
						}
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="box" data-id="5">';
			echo '<h2>Lancer un espionnage</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer != 0 && $place->playerColor == $session->get('playerInfo')->get('color')) {
					echo 'Vous ne pouvez pas espionner un joueur de votre faction';
				} elseif ($place->rPlayer == 0 && $place->typeOfPlace != 1) {
					echo 'Vous ne pouvez pas espionner une planète non-habitable';
				} else {
					$prices = array(
						'Impact faible' => 1000,
						'Impact moyen' => 2500,
						'Grand impact' => 5000
					);

					foreach ($prices as $label => $price) { 
						echo '<a href="' . Format::actionBuilder('spy', $sessionToken, ['rplace' => $place->getId(), 'price' => $price]) . '" class="spy-button">';
							echo '<span class="label">' . $label . '</span>';
							echo '<span class="price">' . Format::numberFormat($price) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="" /></span>';
						echo '</a>';
					}

					echo '<form class="spy-form" method="post" action="' . Format::actionBuilder('spy', $sessionToken, ['rplace' => $place->getId()]) . '">';
						echo '<input type="text" value="10000" name="price" />';
						echo '<button type="submit">Espionner</button>';
					echo '</form>';
				}
			echo '</div>';
		echo '</div>';
	} else {
		echo '<div class="box" data-id="1">';
			echo '<h2>Envoyer des recycleurs</h2>';
			echo '<div class="box-content">';
				if (!($place->sectorColor == $session->get('playerInfo')->get('color') || $place->sectorColor == ColorResource::NO_FACTION)) {
					echo 'Vous ne pouvez envoyer des recycleurs que dans des secteurs non-revendiqués ou contrôlés par votre faction.';
				} elseif ($place->typeOfPlace == Place::EMPTYZONE) {
					echo 'Cette endroit regorgait autrefois de ressources ou de gaz mais de nombreux recycleurs sont déjà passés par là et n\'ont laissé que le vide de l\'espace.';
				} elseif ($defaultBase->getLevelRecycling() == 0) {
					echo 'Vous devez disposer d\'un centre de recyclage.';
				} else {
					$totalShip  = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::RECYCLING, 'level', $defaultBase->levelRecycling, 'nbRecyclers');
					$activeShip = 0;
					$travelTime = Game::getTimeTravel($defaultBase->system, $defaultBase->position, $defaultBase->xSystem, $defaultBase->ySystem, $place->rSystem, $place->position, $place->xSystem, $place->ySystem, $session->get('playerBonus'));

					for ($j = 0; $j < $recyclingMissionManager->size(); $j++) { 
						$activeShip += $recyclingMissionManager->get($j)->recyclerQuantity;
						$activeShip += $recyclingMissionManager->get($j)->addToNextMission;
					}

					echo '<span class="label-box">';
						echo '<span class="key">Recycleurs libres</span>';
						echo '<span class="val">' . Format::number($totalShip - $activeShip) . '</span>';
					echo '</span>';

					echo '<span class="label-box">';
						echo '<span class="key">Temps de cycle</span>';
						echo '<span class="val">' . Chronos::secondToFormat((2 * $travelTime) + RecyclingMission::RECYCLING_TIME, 'lite') . '</span>';
					echo '</span>';

					echo '<form class="spy-form" method="post" action="' . Format::actionBuilder('createmission', $sessionToken, ['rplace' => $defaultBase->getId(), 'rtarget' => $place->getId()]) . '">';
						echo '<input type="text" name="quantity" value="' . ($totalShip - $activeShip) . '" />';
						echo '<button type="submit">Envoyer</button>';
					echo '</form>';
				}
			echo '</div>';
		echo '</div>';
	}
	echo '</div>';
echo '</div>';
