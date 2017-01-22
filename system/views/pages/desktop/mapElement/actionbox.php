<?php

use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Gaia\Resource\SystemResource;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Athena\Model\RecyclingMission;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Modules\Ares\Model\Commander;

$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$session = $this->getContainer()->get('app.session');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$commanderManager = $this->getContainer()->get('ares.commander_manager');
$littleReportManager = $this->getContainer()->get('ares.little_report_manager');
$spyReportManager = $this->getContainer()->get('artemis.spy_report_manager');
$recyclingMissionManager = $this->getContainer()->get('athena.recycling_mission_manager');
$technologyManager = $this->getContainer()->get('promethee.technology_manager');

if ($request->query->has('relatedplace')) {
	if (($defaultBase = $orbitalBaseManager->get($request->query->get('relatedplace'))) === null) {
		$response->redirect('404');
	}
}

if (isset($defaultBase)) {
	# load the commanders of the default base in a session
	$S_COM1 = $commanderManager->getCurrentSession();
	$commanderManager->newSession();
	$commanderManager->load(array('c.rBase' => $defaultBase->getRPlace(), 'c.statement' => array(Commander::AFFECTED, Commander::MOVING)));
	$localCommandersSession = $commanderManager->getCurrentSession();

	# load all the commanders moving in a session
	$commanderManager->newSession();
	$commanderManager->load(array('c.rPlayer' => $session->get('playerId'), 'c.statement' => Commander::MOVING));
	$movingCommandersSession = $commanderManager->getCurrentSession();
	$commanderManager->changeSession($S_COM1);

	# load last report
	$placesId = array();
	foreach ($places as $place) {
		$placesId[] = $place->id;
	}

	$S_LRM_MAP = $littleReportManager->getCurrentSession();
	$littleReportManager->newSession();
	$littleReportManager->load(array('rPlayerAttacker' => $session->get('playerId'), 'r.rPlace' => $placesId), array('r.dFight', 'DESC'), array(0, 30));

	$S_SRM_MAP = $spyReportManager->getCurrentSession();
	$spyReportManager->newSession();
	$spyReportManager->load(array('rPlayer' => $session->get('playerId'), 'rPlace' => $placesId), array('dSpying', 'DESC'), array(0, 30));

	# load the technologies
	$technologies = $technologyManager->getPlayerTechnology($session->get('playerId'));

	# load recycling missions
	$S_REM1 = $recyclingMissionManager->getCurrentSession();
	$recyclingMissionManager->newSession();
	$recyclingMissionManager->load(array('rBase' => $defaultBase->rPlace, 'statement' => array(RecyclingMission::ST_BEING_DELETED, RecyclingMission::ST_ACTIVE)));

	# header part
	echo '<div class="header" data-sector-color="' . $places[0]->sectorColor . '" data-distance="' . Format::numberFormat(Game::getDistance($defaultBase->xSystem, $places[0]->xSystem, $defaultBase->ySystem, $places[0]->ySystem)) . '">';
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
						if ($place->typeOfPlace == 1) {
							echo '<img class="land" src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '-' . Game::getSizeOfPlanet($place->population) . '.png" />';
						} else {
							if ($place->resources > 10000000) {
								$size = 3;
							} elseif ($place->resources > 5000000) {
								$size = 2;
							} else {
								$size = 1;
							}

							echo '<img class="land" src="' . MEDIA . 'map/place/place' . $place->typeOfPlace . '-' . $size . '.png" />';
						}
						if ($place->rPlayer != 0) {
							echo '<img class="avatar" src="' . MEDIA . '/avatar/small/' . $place->playerAvatar . '.png" />';
						}
					echo '</a>';
				echo '</li>';

				// noAJAX
				echo '<li class="action color' . $place->playerColor . '" id="place-' . $i . '" ' . (isset($noAJAX) && $noAJAX && (int) $request->query->get('place') === $place->id ? 'style="width: 565px;"' : NULL) . '>';
					echo '<div class="content">';
						echo '<div class="column info">';
							for ($j = 0; $j < $spyReportManager->size(); $j++) { 
								if ($spyReportManager->get($j)->rPlace == $place->id) {
									echo '<a href="' . APP_ROOT . 'fleet/view-spyreport/report-' . $spyReportManager->get($j)->id . '" class="last-spy-link hb" title="voir le rapport d\'espionnage le plus récent"><img src="' . MEDIA . 'map/spy/last-spy.png" alt="" /></a>';
									break;
								}
							}

							if ($place->typeOfPlace == 1) {
								if ($place->typeOfBase != 0) {
									echo '<p><strong>' . $place->baseName . '</strong></p>';

									echo '<p>propriété du</p>';
									echo '<p>';
										if ($place->playerColor != 0) {
											$status = ColorResource::getInfo($place->playerColor, 'status');
											echo $status[$place->playerStatus - 1] . ' ';
											echo '<span class="player-name">';
												echo '<a href="' . APP_ROOT . 'embassy/player-' . $place->rPlayer . '" class="color' . $place->playerColor . '">' . $place->playerName . '</a>';
											echo '</span>';
										} else {
											echo 'rebelle <span class="player-name">' . $place->playerName . '</span>';
										}
									echo '</p>';
								} else {
									echo '<p><strong>Planète rebelle</strong></p>';
									
									echo '<hr />';

									echo '<p>';
										echo '<span class="label">Défense</span>';
										echo '<span class="value">';
											$danger = [
												0 => 'défense pratiquement inexistante',
												Place::DNG_CASUAL => 'défense faible',
												Place::DNG_EASY => 'défense moyenne',
												Place::DNG_MEDIUM => 'défense forte',
												Place::DNG_HARD => 'défense extrêmement forte'
											];
											foreach ($danger as $value => $label) {
												if ($place->maxDanger >= $value) {
													echo '<img src="' . MEDIA . 'resources/defense.png" class="icon-color" alt="" />';
												}
											}
										echo '</span>';
									echo '</p>';
								}

								echo '<hr />';

								echo '<p>';
									echo '<span class="label">Population</span>';
									echo '<span class="value">';
										$population = [
											0 => '',
											50 => '',
											100 => '',
											150 => '',
											200 => ''
										];
										foreach ($population as $value => $label) {
											if ($place->population >= $value) {
												echo '<img src="' . MEDIA . 'resources/population.png" class="icon-color" alt="" />';
											}
										}
									echo '</span>';
								echo '</p>';
								echo '<p>';
									echo '<span class="label">Ressource</span>';
									echo '<span class="value ">';
										$resources = [
											0 => '',
											20 => '',
											40 => '',
											60 => '',
											80 => ''
										];
										foreach ($resources as $value => $label) {
											if ($place->coefResources >= $value) {
												echo '<img src="' . MEDIA . 'resources/resource.png" class="icon-color" alt="" />';
											}
										}
									echo '</span>';
								echo '</p>';
								echo '<p>';
									echo '<span class="label">Science</span>';
									echo '<span class="value">';
									$coefHistory = Game::getImprovementFromScientificCoef($place->coefHistory);
										$science = [
											0 => '',
											8 => '',
											16 => '',
											24 => '',
											32 => ''
										];
										foreach ($science as $value => $label) {
											if ($coefHistory >= $value) {
												echo '<img src="' . MEDIA . 'resources/science.png" class="icon-color" alt="" />';
											}
										}
									echo '</span>';
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

							echo '<p>';
								echo '<span class="label">Distance</span>';
								echo '<span class="value">' . Format::numberFormat(Game::getDistance($defaultBase->xSystem, $place->xSystem, $defaultBase->ySystem, $place->ySystem)) . ' Al.</span>';
							echo '</p>';

							if ($place->typeOfPlace == 1) {
								for ($j = 0; $j < $littleReportManager->size(); $j++) { 
									if ($littleReportManager->get($j)->rPlace == $place->id) {
										echo '<hr />';
										echo '<p><em>Dernier pillage ' . Chronos::transform($littleReportManager->get($j)->dFight) . '.</em></p>';
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
}

$spyReportManager->changeSession($S_SRM_MAP);
$littleReportManager->changeSession($S_LRM_MAP);
$recyclingMissionManager->changeSession($S_REM1);