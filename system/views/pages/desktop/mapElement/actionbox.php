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
ASM::$com->load(array('c.rBase' => $defaultBase->getRPlace(), 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));
$localCommandersSession = ASM::$com->getCurrentSession();

# load all the commanders moving in a session
ASM::$com->newSession();
ASM::$com->load(array('c.rPlayer' => CTR::$data->get('playerId'), 'c.statement' => Commander::MOVING));
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
			echo '<li class="place color' . $place->playerColor . '">';
				echo '<a href="#" class="openplace" data-target="' . $i . '">';
					echo '<strong>' . $place->position . '</strong>';
					if ($place->typeOfPlace == 1) {
						echo '<img class="land" src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '-' . Game::getSizeOfPlanet($place->population) . '.png" />';
					} else {
						echo '<img class="land" src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '.png" />';
					}
					if ($place->rPlayer != 0) {
						echo '<img class="avatar" src="' . MEDIA . '/avatar/small/' . $place->playerAvatar . '.png" />';
					}
				echo '</a>';
			echo '</li>';

			echo '<li class="action color' . $place->playerColor . '" id="place-' . $i . '">';
				echo '<div class="content">';
					echo '<div class="column info">';
						if ($place->typeOfBase != 0) {
							echo '<p><strong>' . $place->baseName . '</strong></p>';
							echo '<p>' . Format::numberFormat($place->points) . ' points</p>';
							echo '<hr />';
							echo '<p>propriété du</p>';
							echo '<p>';
								if ($place->playerColor != 0) {
									$status = ColorResource::getInfo($place->playerColor, 'status');
									echo $status[$place->playerStatus - 1] . ' ';
									echo '<span class="player-name">';
										echo '<a href="' . APP_ROOT . 'diary/player-' . $place->rPlayer . '" class="color' . $place->playerColor . '">' . $place->playerName . '</a>';
									echo '</span>';
								} else {
									echo 'rebelle <span class="player-name">' . $place->playerName . '</span>';
								}
							echo '</p>';
						} elseif (1 == 2) {
							# gérer les vaisseaux mères
						} else {
							switch ($place->typeOfPlace) {
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
							echo '<span class="value">' . $place->coefHistory . ' %</span>';
						echo '</p>';
						echo '<p>';
							echo '<span class="label">Coeff. ressource</span>';
							echo '<span class="value">' . $place->coefResources . ' %</span>';
						echo '</p>';
						echo '<p>';
							echo '<span class="label">Distance</span>';
							echo '<span class="value">' . Format::numberFormat(Game::getDistance($defaultBase->xSystem, $place->xSystem, $defaultBase->ySystem, $place->ySystem)) . ' Al.</span>';
						echo '</p>';
						if ($place->typeOfPlace == 1) {
							echo '<p>';
								echo '<span class="label">Population [million]</span>';
								echo '<span class="value">' . Format::numberFormat($place->population) . '</span>';
							echo '</p>';
						}
					echo '</div>';

					include PAGES . 'desktop/mapElement/component/actBull.php';
				echo '</div>';
			echo '</li>';
			}
		echo '</ul>';
	echo '</div>';
echo '</div>';
?>