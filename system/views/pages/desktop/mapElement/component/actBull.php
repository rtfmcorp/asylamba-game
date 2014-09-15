<?php
# display part
echo '<div class="column act">';
	echo '<div class="top">';
		$available = (($place->rPlayer != 0 && $place->playerColor != CTR::$data->get('playerInfo')->get('color')) || ($place->rPlayer == 0 && $place->typeOfPlace == 1)) ? NULL : 'grey';
		echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="1"><img src="' . MEDIA . 'map/action/loot.png" alt="" /></a>';
		echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="2"><img src="' . MEDIA . 'map/action/colo.png" alt="" /></a>';

		$available = ($place->rPlayer == CTR::$data->get('playerId') && $place->getId() != $defaultBase->getId()) ? NULL : 'grey';
		echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="3"><img src="' . MEDIA . 'map/action/move.png" alt="" /></a>';

		$available = ($place->rPlayer != 0 && $place->getId() != $defaultBase->getId()) ? NULL : 'grey';
		echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="4"><img src="' . MEDIA . 'map/action/rc.png" alt="" /></a>';

		$available = (($place->rPlayer != 0 && $place->playerColor != CTR::$data->get('playerInfo')->get('color')) || ($place->rPlayer == 0 && $place->typeOfPlace == 1)) ? NULL : 'grey';
		echo '<a href="#" class="actionbox-sh ' . $available . '" data-target="5"><img src="' . MEDIA . 'map/action/spy.png" alt="" /></a>';
	echo '</div>';
	
	echo '<div class="bottom">';
		echo '<div class="box" data-id="1">';
			echo '<h2>Lancer un pillage</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer == 0 && $place->typeOfPlace != 1) {
					echo 'Vous ne pouvez pas attaquer une planète non-habitable';
				} elseif ($place->typeOfPlace == 1 && $place->playerColor == CTR::$data->get('playerInfo')->get('color')) {
					echo 'Vous ne pouvez pas attaquer un joueur de votre faction';
				} elseif ($place->typeOfPlace == 1 && $place->playerLevel == 1) {
					echo 'Ce joueur est sous protection débutant';
				} else {
					echo '<div class="commander-tile">';
						echo '<div class="item no-commander">';
							echo 'Aucun commandant selectionné.<br/>Sélectionnez-en un sur la barre latérale gauche.<br/><br />Si aucun commandant n\'est visible, vous pouvez en affecter un depuis l\'école de commandement.';
						echo '</div>';
						echo '<div class="item too-far">';
							echo 'Ce commandant est trop éloigné pour coloniser cette planète';
						echo '</div>';
						echo '<div class="item move">';
							echo '<strong class="name"></strong><br />';
							echo 'Temps de l\'attaque : ' . Chronos::secondToFormat(Game::getTimeTravel($defaultBase->system, $defaultBase->position, $defaultBase->xSystem, $defaultBase->ySystem, $place->rSystem, $place->position, $place->xSystem, $place->ySystem, CTR::$data->get('playerBonus')), 'lite') . ' <img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="" /><br />';
							echo 'Capacité de la soute : <span class="wedge"></span> <img src="' . MEDIA . 'resources/resource.png" class="icon-color" alt="" /><br />';
							echo '<a class="button" href="#" data-url="' . APP_ROOT . 'action/a-loot/commanderid-{id}/placeid-' . $place->id . '">Lancer l\'attaque</a>';
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		$maxBasesQuantity = $technologies->getTechnology(Technology::BASE_QUANTITY) + 1;
		$obQuantity = CTR::$data->get('playerBase')->get('ob')->size();
		$msQuantity = CTR::$data->get('playerBase')->get('ms')->size();
		$coloQuantity = 0;

		$S_COM3 = ASM::$com->getCurrentSession();
		ASM::$com->changeSession($movingCommandersSession);
		for ($j = 0; $j < ASM::$com->size(); $j++) { 
			if (ASM::$com->get($j)->getTypeOfMove() == COM_COLO) {
				$coloQuantity++;
			}
		}
		ASM::$com->changeSession($S_COM3);

		$totalBases = $obQuantity + $msQuantity + $coloQuantity;

		echo '<div class="box" data-id="2">';
			echo '<h2>Lancer une ' . ($place->rPlayer == 0 ? 'colonisation' : 'conquête') . '</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer == 0 && $place->typeOfPlace != 1) {
					echo 'Vous ne pouvez pas coloniser une planète non-habitable';
				} elseif ($place->typeOfPlace == 1 && $place->playerColor == CTR::$data->get('playerInfo')->get('color')) {
					echo 'Vous ne pouvez pas conquérir un joueur de votre faction';
				} elseif ($place->typeOfPlace == 1 && $place->playerLevel <= 3 && $place->playerLevel != 0) {
					echo 'Vous ne pouvez pas conquérir un joueur de niveau 3 ou inférieur';
				} elseif ($place->rPlayer == 0 && $technologies->getTechnology(Technology::COLONIZATION) == 0) {
					echo 'Vous devez développer la technologie colonisation';
				} elseif ($place->rPlayer != 0 && $technologies->getTechnology(Technology::CONQUEST) == 0) {
					echo 'Vous devez développer la technologie conquête';
				} elseif ($totalBases >= $maxBasesQuantity) {
					echo 'Vous devez améliorer le niveau de la technologie administration étendue pour disposer de planète supplémentaire';
				} else {
					echo '<div class="commander-tile">';
						echo '<div class="item no-commander">';
							echo 'Aucun commandant selectionné<br/>Sélectionnez-en un sur la bar latérale gauche.<br/>Si aucun commandant n\'est visible, vous pouvez en affecter un dans l\'amirauté.';
						echo '</div>';
						echo '<div class="item too-far">';
							echo 'Ce commandant est trop éloigné pour coloniser cette planète';
						echo '</div>';
						echo '<div class="item move">';
							echo '<strong class="name"></strong><br />';
							echo 'Temps de l\'attaque : ' . Chronos::secondToFormat(Game::getTimeTravel($defaultBase->system, $defaultBase->position, $defaultBase->xSystem, $defaultBase->ySystem, $place->rSystem, $place->position, $place->xSystem, $place->ySystem, CTR::$data->get('playerBonus')), 'lite') . ' <img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="" /><br />';

							if ($place->rPlayer == 0) {
								$price = $totalBases * CREDITCOEFFTOCOLONIZE;
								if (CTR::$data->get('playerInfo')->get('color') == ColorResource::CARDAN) {
									# bonus if the player is from Cardan
									$price -= round($price * ColorResource::BONUS_CARDAN_COLO / 100);
								}
								echo 'Coût : <span class="price">' . Format::numberFormat($price) . '</span> <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="" /><br />';
								echo '<a class="button" href="#" data-url="' . APP_ROOT . 'action/a-colonize/commanderid-{id}/placeid-' . $place->id . '">Lancer la colonisation</a>';
							} else {
								$price = $totalBases * CREDITCOEFFTOCONQUER;
								if (CTR::$data->get('playerInfo')->get('color') == ColorResource::CARDAN) {
									# bonus if the player is from Cardan
									$price -= round($price * ColorResource::BONUS_CARDAN_COLO / 100);
								}
								echo 'Coût : <span class="price">' . Format::numberFormat($price) . '</span> <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="" /><br />';
								echo '<a class="button" href="#" data-url="' . APP_ROOT . 'action/a-conquer/commanderid-{id}/placeid-' . $place->id . '">Lancer la conquête</a>';
							}
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="box" data-id="3">';
			echo '<h2>Déplacer une flotte</h2>';
			echo '<div class="box-content">';
				if ($place->getId() == $defaultBase->getId()) {
					echo 'Vous ne pouvez pas déplacer une flotte sur votre planète de départ';
				} elseif ($place->rPlayer != CTR::$data->get('playerId')) {
					echo 'Vous ne pouvez déplacer une flotte que vers une de vos bases';
				} else {
					echo '<div class="commander-tile">';
						echo '<div class="item no-commander">';
							echo 'Aucun commandant selectionné<br/>Sélectionnez-en un sur la bar latérale gauche.<br/>Si aucun commandant n\'est visible, vous pouvez en affecter un dans l\'amirauté.';
						echo '</div>';
						echo '<div class="item too-far">';
							echo 'Ce commandant est trop éloigné pour se déplacer jusqu\'ici';
						echo '</div>';
						echo '<div class="item move">';
							echo '<strong class="name"></strong><br />';
							echo 'Temps du déplacement : ' . Chronos::secondToFormat(Game::getTimeTravel($defaultBase->system, $defaultBase->position, $defaultBase->xSystem, $defaultBase->ySystem, $place->rSystem, $place->position, $place->xSystem, $place->ySystem, CTR::$data->get('playerBonus')), 'lite') . ' <img src="' . MEDIA . 'resources/time.png" class="icon-color" alt="" /><br />';
							echo '<a class="button" href="#" data-url="' . APP_ROOT . 'action/a-movefleet/commanderid-{id}/placeid-' . $place->id . '">Lancer la mission</a>';
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="box" data-id="4">';
			echo '<h2>Proposer une route commerciale</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer == 0) {
					echo 'Vous ne pouvez proposer une route commerciale qu\'à des joueurs';
				} elseif ($place->getId() == $defaultBase->getId()) {
					echo 'Vous ne pouvez pas proposer une route commerciale sur votre propre base';
				} elseif ($defaultBase->levelCommercialPlateforme == 0) {
					echo 'Il vous faut une plateforme commerciale pour proposer une route commericale';
				} elseif ($place->levelCommercialPlateforme == 0) {
					echo 'Le joueur ne dispose pas d\'une plateforme commerciale';
				} else {
					$proposed 	 = FALSE;
					$notAccepted = FALSE;
					$standby 	 = FALSE;

					$S_CRM3 = ASM::$crm->getCurrentSession();
					ASM::$crm->changeSession($defaultBase->routeManager);
					for ($j = 0; $j < ASM::$crm->size(); $j++) { 
						if (ASM::$crm->get($j)->getROrbitalBaseLinked() == $defaultBase->getRPlace()) {
							if (ASM::$crm->get($j)->getROrbitalBase() == $place->getId()) {
								switch(ASM::$crm->get($j)->getStatement()) {
									case CRM_PROPOSED: $notAccepted = TRUE; break;
									case CRM_ACTIVE: $sendResources = TRUE; break;
									case CRM_STANDBY: $standby = TRUE; break;
								}
							}
						}
						if (ASM::$crm->get($j)->getROrbitalBase() == $defaultBase->getRPlace()) {
							if (ASM::$crm->get($j)->getROrbitalBaseLinked() == $place->getId()) {
								switch(ASM::$crm->get($j)->getStatement()) {
									case CRM_PROPOSED: $proposed = TRUE; break;
									case CRM_ACTIVE: $sendResources = TRUE; break;
									case CRM_STANDBY: $standby = TRUE; break;
								}
							}
						}
					}
					ASM::$crm->changeSession($S_CRM3);

					$distance = Game::getDistance($defaultBase->xSystem, $place->xSystem, $defaultBase->ySystem, $place->ySystem);
					$bonusA = ($defaultBase->sector != $place->rSector) ? CRM_ROUTEBONUSSECTOR : 1;
					$bonusB = (CTR::$data->get('playerInfo')->get('color')) != $place->playerColor ? CRM_ROUTEBONUSCOLOR : 1;
					$price = Game::getRCPrice($distance, $defaultBase->planetPopulation, $place->population, CRM_COEFROUTEPRICE);
					$income = Game::getRCIncome($distance, $defaultBase->planetPopulation, $place->population, CRM_COEFROUTEINCOME, $bonusA, $bonusB);

					echo '<div class="rc">';
						echo '<img src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '-' . Game::getSizeOfPlanet($place->population) . '.png" alt="" class="planet" />';
						echo 'Revenu par relève : ' . Format::numberFormat($income) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" /><br />';
						echo 'Bassin de population : ' . Format::numberFormat($place->population + $defaultBase->planetPopulation) . ' millions<br />';
						if (CTR::$data->get('playerInfo')->get('color') == ColorResource::NEGORA) {
							# bonus if the player is from Negore
							$price -= round($price * ColorResource::BONUS_NEGORA_ROUTE / 100);
						}
						echo 'Coûts de construction : ' . Format::numberFormat($price) . ' <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" /><br />';
						if ($proposed) {
							echo '<a href="' . APP_ROOT . 'bases/view-commercialplateforme/mode-route" class="button">Annuler la proposition</a>';
						} elseif ($notAccepted) {
							echo '<a href="' . APP_ROOT . 'bases/view-commercialplateforme/mode-route" class="button">Accepter la proposition</a>';
						} elseif ($standby) {
							echo '<span class="button">C\'est la guerre</span>';
						} else {
							$S_CRM2 = ASM::$crm->getCurrentSession();
							ASM::$crm->changeSession($defaultBase->routeManager);
							$ur = ASM::$crm->size();
							for ($j = 0; $j < ASM::$crm->size(); $j++) {
								if (ASM::$crm->get($j)->getROrbitalBaseLinked() == $defaultBase->rPlace && ASM::$crm->get($j)->statement == CRM_PROPOSED) {
									$ur--;
								}
							}

							if ($ur < OrbitalBaseResource::getBuildingInfo(6, 'level', $defaultBase->levelCommercialPlateforme, 'nbRoutesMax')) {
								echo '<a href="' . APP_ROOT . 'action/a-proposeroute/basefrom-' . $defaultBase->getId() . '/baseto-' . $place->getId() . '" class="button">Proposer une route</a>';
							} else {
								echo '<span class="button">Pas assez de slot</span>';
							}

							ASM::$crm->changeSession($S_CRM2);
						}
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="box" data-id="5">';
			echo '<h2>Lancer un espionnage</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer != 0 && $place->playerColor == CTR::$data->get('playerInfo')->get('color')) {
					echo 'Vous ne pouvez pas espionner un joueur de votre faction';
				} elseif ($place->rPlayer == 0 && $place->typeOfPlace != 1) {
					echo 'Vous ne pouvez pas espionner une planète non-habitable';
				} else {
					$prices = array(
						'petit' => 1000,
						'moyen' => 2500,
						'grand' => 5000,
						'très grande' => 10000
					);

					foreach ($prices as $label => $price) { 
						echo '<a href="' . APP_ROOT . 'action/a-spy/rplace-' . $place->getId() . '/price-' . $price . '" class="spy-button">';
							echo '<img src="' . MEDIA . 'resources/credit.png" alt="" class="picto" />';
							echo '<span class="label">' . $label . '</span>';
							echo '<span class="price">' . Format::numberFormat($price) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="" /></span>';
						echo '</a>';
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>