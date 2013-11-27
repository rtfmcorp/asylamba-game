<?php
include_once GAIA;
include_once ATHENA;
include_once ZEUS;

if (CTR::$get->exist('relatedplace')) {
	$S_OBM2 = ASM::$obm->getCurrentSession();
	ASM::$obm->newSession();
	ASM::$obm->load(array('rPlace' => CTR::$get->get('relatedplace')));
	if (ASM::$obm->size() == 1) {
		$defaultBase = ASM::$obm->get(0);
	} else {
		CTR::redirect('404');
	}
	ASM::$obm->changeSession($S_OBM2);
}

# load the commanders of the default base in a session
$S_COM1 = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('rBase' => $defaultBase->getRPlace(), 'statement' => array(COM_AFFECTED, COM_MOVING)));
$localCommandersSession = ASM::$com->getCurrentSession();

# load all the commanders moving in a session
ASM::$com->newSession();
ASM::$com->load(array('rPlayer' => CTR::$data->get('playerId'), 'statement' => COM_MOVING));
$movingCommandersSession = ASM::$com->getCurrentSession();
ASM::$com->changeSession($S_COM1);

# load the technologies
$technologies = new Technology(CTR::$data->get('playerId'));

# header part
echo '<div class="header">';
	echo '<ul>';
		echo '<li>Système #' . $system->id . '</li>';
		echo '<li>Cordonnées ' . Game::formatCoord($system->xPosition, $system->yPosition) . '</li>';
		echo '<li>';
			if ($system->rColor == 0) {
				echo 'Non revendiqué';
			} else {
				echo '<img src="' . MEDIA . 'ally/big/color' . $system->rColor . '.png" alt="" /> ';
				echo 'Revendiqué par ' . ColorResource::getInfo($system->rColor, 'popularName');
			}
		echo '</li>';
		echo '<li><img src="' . MEDIA . 'map/systems/t' . $system->typeOfSystem . 'c0.png" alt="" /> ' . SystemResource::getInfo($system->typeOfSystem, 'frenchName') . '</li>';
	echo '</ul>';
	echo '<a href="#" class="button hb tl closeactionbox" title="fermer">×</a>';
echo '</div>';

# body part
echo '<div class="body">';
	echo '<a class="actbox-movers" id="actboxToLeft" href="#"></a>';
	echo '<a class="actbox-movers" id="actboxToRight" href="#"></a>';
	echo '<div class="system">';
		echo '<ul>';
		echo '<li class="star"></li>';
		for ($i = 0; $i < count($places); $i++) {
			$place = $places[$i];
			echo '<li class="place color' . $place->getPlayerColor() . '">';
				echo '<a href="#" class="openplace" data-target="' . $i . '">';
					echo '<strong>' . $place->getPosition() . '</strong>';
					if ($place->getTypeOfPlace() == 1) {
						echo '<img class="land" src="' . MEDIA . 'map/place/place' . $place->getTypeOfPlace() . '-' . Game::getSizeOfPlanet($place->getPopulation()) . '.png" />';
					} else {
						echo '<img class="land" src="' . MEDIA . 'map/place/place' . $place->getTypeOfPlace() . '.png" />';
					}
					if ($place->getRPlayer() != 0) {
						echo '<img class="avatar" src="' . MEDIA . '/avatar/small/' . $place->getPlayerAvatar() . '.png" />';
					}
				echo '</a>';
			echo '</li>';

			echo '<li class="action color' . $place->getPlayerColor() . '" id="place-' . $i . '">';
				echo '<div class="content">';
					echo '<div class="column info">';
						if ($place->getTypeOfBase() != 0) {
							echo '<p><strong>' . $place->getBaseName() . '</strong></p>';
							echo '<p>' . Format::numberFormat($place->getPoints()) . ' points</p>';
							echo '<hr />';
							echo '<p>propriété de</p>';
							echo '<p>';
								echo '<a class="color-name color' . $place->getPlayerColor() . '" href="' . APP_ROOT . 'diary/player-' . $place->getRPlayer() . '" class="color1">' . $place->getPlayerName() . '</a>';
							echo ($place->getPlayerColor() == 0 ? '(inactif)' : '') . '</p>';
						} elseif (1 == 2) {
							# réger les vaisseaux mères
						} else {
							switch ($place->getTypeOfPlace()) {
								case 1: echo '<p><strong>Planète rebelle</strong></p>'; break;
								case 2: echo '<p><strong>Géante gazeuse</strong></p>'; break;
								case 3: echo '<p><strong>Ruines anciennes</strong></p>'; break;
								case 4: echo '<p><strong>Poche de gaz</strong></p>'; break;
								case 5: echo '<p><strong>Ceinture d\'astéroide</strong></p>'; break;
								case 6: echo '<p><strong>Zone vide</strong></p>'; break;
								default: break;
							}
							echo '<hr />';
							# gérer les vaisseaux mères
							echo '<p>Non-revendiquée</p>';
						}
						echo '<hr />';
						echo '<p>';
							echo '<span class="label">Coeff. histoire</span>';
							echo '<span class="value">' . $place->getCoefHistory() . ' %</span>';
						echo '</p>';
						echo '<p>';
							echo '<span class="label">Coeff. ressource</span>';
							echo '<span class="value">' . $place->getCoefResources() . ' %</span>';
						echo '</p>';
						echo '<p>';
							echo '<span class="label">Distance [parsec]</span>';
							echo '<span class="value">---</span>';
						echo '</p>';
						if ($place->getTypeOfPlace() == 1) {		
							echo '<p>';
								echo '<span class="label">Population [million]</span>';
								echo '<span class="value">' . Format::numberFormat($place->getPopulation()) . '</span>';
							echo '</p>';
						}
					echo '</div>';
					echo '<div class="column fleet">';
						echo '<p><strong>Défense</strong></p>';
						echo '<p>';
							if ($place->getTypeOfBase() != 0) {
								for ($j = 0; $j < 3; $j++) { 
									if (isset($place->commanders[$j])) {
										echo '<img src="' . MEDIA . 'map/fleet/away.jpg" alt="flotte" class="hb lt" title="commandant en fonction, taille de la flotte inconnue" />';	
									}
								}
							} elseif ($place->getTypeOfPlace() == 1) {
								if ($place->getPopulation() < 80) {
									$title = 'petite flotte de défense';
									$img = 'fleet1';
								} elseif ($place->getPopulation() >= 80 AND $place->getPopulation() < 150) {
									$title = 'moyenne flotte de défense';
									$img = 'fleet2';
								} else {
									$title = 'grande flotte de défense';
									$img = 'fleet3';
								}
								echo '<img src="' . MEDIA . 'map/fleet/' . $img . '.png" alt="flotte" class="hb lt" title="Commandant rebelle à la tête d\'une ' . $title . '" />';
							} else {
								echo '<p>---</p>';
							}
						echo '</p>';
					echo '</div>';

					# work part
					$link = ''; $box = '';
					if ($place->getTypeOfPlace() == 1) {
						# planète habitable
						if ($place->getTypeOfBase() == 0) {
							# planète rebelle
							ActionHelper::loot($defaultBase, $link, $box, 1, $place, $localCommandersSession);
							ActionHelper::colonize($defaultBase, $link, $box, 2, $place, $localCommandersSession, $movingCommandersSession, $technologies);
							ActionHelper::motherShip($defaultBase, $link, $box, 3);
						} else {
							# planète avec joueur
							if ($place->getId() == $defaultBase->getId()) {
								# planète courante
							} elseif ($place->getRPlayer() == CTR::$data->get('playerId')) {
								# une de mes planètes
								ActionHelper::move($defaultBase, $link, $box, 1, $place, $localCommandersSession);
								ActionHelper::proposeRC($defaultBase, $link, $box, 2, $place);
							} elseif ($place->getPlayerColor() == CTR::$data->get('playerInfo')->get('color')) {
								# planète alliée
								ActionHelper::proposeRC($defaultBase, $link, $box, 1, $place);
							} else {
								# planète ennemie
								ActionHelper::loot($defaultBase, $link, $box, 1, $place, $localCommandersSession);
								ActionHelper::conquest($defaultBase, $link, $box, 2, $place, $localCommandersSession, $movingCommandersSession, $technologies);
								ActionHelper::proposeRC($defaultBase, $link, $box, 3, $place);
							}
						}
					} else {
						# autre type de place
						if ($place->getTypeOfBase() != 0) {
							# vaisseau mère -> piller / détruire
							# [TODO|loot, break]
						} else {
							# place vide
							ActionHelper::motherShip($defaultBase, $link, $box, 1);
						}
					}

					# display part
					if ($place->getId() != $defaultBase->getId()) {	
						echo '<div class="column act">';
							echo '<p><strong>Action<span class="subcontext"></span></strong></p>';
							echo '<div class="left">';
								echo $link;
							echo '</div>';
							echo '<div class="right">';
								echo $box;
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
			echo '</li>';
			}
		echo '</ul>';
	echo '</div>';
echo '</div>';
?>