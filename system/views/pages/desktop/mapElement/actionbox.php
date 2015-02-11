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

# load last report
$placesId = array();
foreach ($places as $place) {
	$placesId[] = $place->id;
}

$S_LRM_MAP = ASM::$lrm->getCurrentSession();
ASM::$lrm->newSession();
ASM::$lrm->load(array('rPlayerAttacker' => CTR::$data->get('playerId'), 'r.rPlace' => $placesId), array('r.dFight', 'DESC'), array(0, 30));

include_once ARTEMIS;
$S_SRM_MAP = ASM::$srm->getCurrentSession();
ASM::$srm->newSession();
ASM::$srm->load(array('rPlayer' => CTR::$data->get('playerId'), 'rPlace' => $placesId), array('dSpying', 'DESC'), array(0, 30));

# load the technologies
$technologies = new Technology(CTR::$data->get('playerId'));

# header part
echo '<div class="header" data-distance="' . Format::numberFormat(Game::getDistance($defaultBase->xSystem, $places[0]->xSystem, $defaultBase->ySystem, $places[0]->ySystem)) . '">';
	echo '<ul>';
		echo '<li>Système #' . $system->id . '</li>';
		echo '<li>Cordonnées ' . Game::formatCoord($system->xPosition, $system->yPosition) . '</li>';
		echo '<li>';
			echo $system->rColor == 0
				? 'Non revendiqué'
				: '<img src="' . MEDIA . 'ally/big/color' . $system->rColor . '.png" alt="" /> Revendiqué par ' . ColorResource::getInfo($system->rColor, 'popularName');
		echo '</li>';
		echo '<li><img src="' . MEDIA . 'map/systems/t' . $system->typeOfSystem . 'c0.png" alt="" /> ' . SystemResource::getInfo($system->typeOfSystem, 'frenchName') . '</li>';
	echo '</ul>';
	echo '<a href="#" class="button closeactionbox">×</a>';
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
					echo $place->typeOfPlace == 1
						? '<img class="land" src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '-' . Game::getSizeOfPlanet($place->population) . '.png" />'
						: '<img class="land" src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '.png" />';
					if ($place->rPlayer != 0) {
						echo '<img class="avatar" src="' . MEDIA . '/avatar/small/' . $place->playerAvatar . '.png" />';
					}
				echo '</a>';
			echo '</li>';

			// noAJAX
			echo '<li class="action color' . $place->playerColor . '" id="place-' . $i . '" ' . (isset($noAJAX) && $noAJAX && CTR::$get->equal('place', $place->id) ? 'style="width: 565px;"' : NULL) . '>';
				echo '<div class="content">';
					echo '<div class="column info">';
						for ($j = 0; $j < ASM::$srm->size(); $j++) { 
							if (ASM::$srm->get($j)->rPlace == $place->id) {
								echo '<a href="' . APP_ROOT . 'fleet/view-spyreport/report-' . ASM::$srm->get($j)->id . '" class="last-spy-link hb" title="voir le rapport d\'espionnage le plus récent"><img src="' . MEDIA . 'map/spy/last-spy.png" alt="" /></a>';
								break;
							}
						}

						if ($place->typeOfPlace == 1) {
							if ($place->typeOfBase != 0) {
								echo '<p><strong>' . $place->baseName . '</strong></p>';
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
							} else {
								echo '<p><strong>Planète rebelle</strong></p>';
								echo '<hr />';
								echo '<p>Non-revendiquée</p>';
							}

							echo '<hr />';

							echo '<p>';
								echo '<span class="label">Population</span>';
								echo '<span class="value">' . Format::numberFormat($place->population) . ' [M]</span>';
							echo '</p>';
							echo '<p>';
								echo '<span class="label">Coeff. ressource</span>';
								echo '<span class="value">' . $place->coefResources . ' %</span>';
							echo '</p>';
							echo '<p>';
								echo '<span class="label">Coeff. scientifique</span>';
								echo '<span class="value">' . $place->coefHistory . ' %</span>';
							echo '</p>';
						} else {
							echo '<p><strong>' . ucfirst(Game::convertPlaceType($place->typeOfPlace)) . '</strong></p>';

							echo '<hr />';

							echo '<p>';
								echo '<span class="label">Ressources</span>';
								echo '<span class="value">' .Format::numberFormat($place->resources * $place->coefResources / 100) . '</span>';
							echo '</p>';
							echo '<p>';
								echo '<span class="label">Débris</span>';
								echo '<span class="value">' . Format::numberFormat($place->resources * $place->coefHistory / 100) . '</span>';
							echo '</p>';
							echo '<p>';
								echo '<span class="label">Gaz noble</span>';
								echo '<span class="value">' . Format::numberFormat($place->resources * $place->population / 100) . '</span>';
							echo '</p>';
						}

						echo '<hr />';

						echo '<p>';
							echo '<span class="label">Distance</span>';
							echo '<span class="value">' . Format::numberFormat(Game::getDistance($defaultBase->xSystem, $place->xSystem, $defaultBase->ySystem, $place->ySystem)) . ' Al.</span>';
						echo '</p>';

						if ($place->typeOfPlace == 1) {
							for ($j = 0; $j < ASM::$lrm->size(); $j++) { 
								if (ASM::$lrm->get($j)->rPlace == $place->id) {
									echo '<hr />';
									echo '<p><em>Dernier pillage ' . Chronos::transform(ASM::$lrm->get($j)->dFight) . '.</em></p>';
									break;
								}
							}
						}

					echo '</div>';

					include PAGES . 'desktop/mapElement/component/actBull.php';
				echo '</div>';
			echo '</li>';
			}
		echo '</ul>';
	echo '</div>';
echo '</div>';

ASM::$srm->changeSession($S_SRM_MAP);
ASM::$lrm->changeSession($S_LRM_MAP);
?>