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
							echo 'Aucun commandant selectionné';
						echo '</div>';
						echo '<div class="item too-far">';
							echo 'Ce commandant est trop éloigné';
						echo '</div>';
						echo '<div class="item move">';
							echo 'On peut attaquer<br />';
							echo '<a href="' . APP_ROOT . 'action/a-loot/commanderid-{id}/placeid-' . $place->id . '">Par ici</a>';
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';

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
				} elseif (FALSE) {
					#### niveau d'administration
				} else {
					echo 'Aucun commandant selectionné';
					
					echo '-- commandant trop loin <br />';
					echo '-- on peut attaquer';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="box" data-id="3">';
			echo '<h2>Déplacer une flotte</h2>';
			echo '<div class="box-content">';
				if ($place->getId() == $defaultBase->getId()) {
					echo 'Vous ne pouvez pas déplacer une flotte sur votre planète de départ';
				} elseif ($place->rPlayer != CTR::$data->get('playerId')) {
					echo 'Vous ne pouvez déplacer une flotte que vers vos base';
				} else {
					echo 'Aucun commandant selectionné';

					echo '-- commandant trop loin <br />';
					echo '-- on peut déplacer';
				}
			echo '</div>';
		echo '</div>';

		echo '<div class="box" data-id="4">';
			echo '<h2>Proposer une route commerciale</h2>';
			echo '<div class="box-content">';
				if ($place->rPlayer == 0) {
					echo 'Vous ne pouvez proposer une route commerciale qu\'a des joueurs';
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

					echo '<div class="rc">';
						echo '<img src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '-' . Game::getSizeOfPlanet($place->population) . '.png" alt="" class="planet" />';
						echo 'Revenu par relève : 1 000 <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" /><br />';
						echo 'Bassin de population : ' . Format::numberFormat($place->population + $defaultBase->planetPopulation) . ' millions<br />';
						echo 'Coûts de construction : 1 000 000 <img src="' . MEDIA . 'resources/credit.png" alt="" class="icon-color" /><br />';

						if ($proposed) {
							echo '<a href="#" class="button">en attente d\'acceptation<br />Annuler la proposition</a>';
						} elseif ($notAccepted) {
							echo '<a href="#" class="button">en attente d\'acceptation<br />Accepter la proposition</a>';
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
								echo '<a href="#" class="button">Proposer une route</a>';
							} else {
								echo '<span class="button">pas assez de slot</span>';
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