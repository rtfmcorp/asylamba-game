<?php
# listFleetIncoming component
# in ares package

# affichage de la liste des flottes qui attaquent le joueurs

# require
	# [{commander}]		commander_listFleetIncoming

echo '<div class="component size3 list-fleet">';
	echo '<div class="head skin-1">';
		echo '<h1>Centre des opérations</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($obsets as $base) {
				echo '<div class="set-fleet">';
					echo '<img src="' . MEDIA . 'map/place/place' . $base['info']['img'] . '.png" alt="' . $base['info']['name'] . '" class="place" />';

					echo '<h2>';
						echo PlaceResource::get($base['info']['type'], 'name') . ' ';
						echo $base['info']['name'];
					echo '</h2>';

					foreach ($base['fleets'] as $commander) {
						echo '<div class="item">';
							echo '<div class="left">';
								if ($commander->rPlayer != CTR::$data->get('playerId')) {
									echo '<img src="' . MEDIA . 'map/action/shield.png" alt="" class="status color' . $commander->playerColor . '" />';
								} elseif ($commander->statement == Commander::AFFECTED) {
									echo '<img src="' . MEDIA . 'map/action/anchor.png" alt="" class="status" />';
								} elseif ($commander->statement == Commander::MOVING) {
									switch ($commander->travelType) {
										case Commander::MOVE: echo '<img src="' . MEDIA . 'map/action/move.png" alt="" class="status" />'; break;
										case Commander::LOOT: echo '<img src="' . MEDIA . 'map/action/loot.png" alt="" class="status" />'; break;
										case Commander::COLO: echo '<img src="' . MEDIA . 'map/action/colo.png" alt="" class="status" />'; break;
										case Commander::BACK: echo '<img src="' . MEDIA . 'map/action/back.png" alt="" class="status" />'; break;
										default: break;
									}
								}
								echo '<span class="top">';
									echo CommanderResources::getInfo($commander->level, 'grade') . ' <strong>' . $commander->name . '</strong>, ';
									if ($commander->rPlayer != CTR::$data->get('playerId')) {
										echo 'vous attaque';
									} elseif ($commander->statement == Commander::AFFECTED) {
										echo 'à quai';
									} elseif ($commander->statement == Commander::MOVING) {
										switch ($commander->travelType) {
											case Commander::MOVE: echo 'se déplace'; break;
											case Commander::LOOT: echo 'tente un pillage'; break;
											case Commander::COLO: echo 'tente une colonisation'; break;
											case Commander::BACK: echo 'revient avec ' . Format::number($commander->resources) . ' ressources'; break;
											default: break;
										}
									}
									echo '&#8194;|&#8194;' . Format::number($commander->getPev()) . ' pev';

									if ($commander->rPlayer == CTR::$data->get('playerId') && $commander->statement == Commander::MOVING && $commander->travelType != Commander::BACK) {
										echo '&#8194;&#8194;<a href="' . APP_ROOT . 'action/a-cancelmove/commanderid-' . $commander->id . '">annuler</a>';
									}
								echo '</span>';
							echo '</div>';

							echo '<div class="center ' . (($commander->rPlayer != CTR::$data->get('playerId') || $commander->travelType == Commander::BACK) ? 'reversed' : NULL) . '">';
								if ($commander->statement == Commander::MOVING) {
									echo '<div class="progress-ship color' . $commander->playerColor . '">';
										if ($commander->rPlayer != CTR::$data->get('playerId') || $commander->travelType == Commander::BACK) {
											echo '<div class="bar" style="width: ' . Format::percent(Utils::interval($commander->dArrival, Utils::now(), 's'), Utils::interval($commander->dArrival, $commander->dStart, 's')) . '%;">';
										} else {
											echo '<div class="bar" style="width: ' . Format::percent(Utils::interval($commander->dStart, Utils::now(), 's'), Utils::interval($commander->dStart, $commander->dArrival, 's')) . '%;">';
										}
											echo ($commander->rPlayer != CTR::$data->get('playerId') || $commander->travelType == Commander::BACK)
												? '<img src="' . MEDIA . 'map/fleet/ship-reversed.png" alt="" class="ship" />'
												: '<img src="' . MEDIA . 'map/fleet/ship.png" alt="" class="ship" />';
											echo '<span>' . Chronos::secondToFormat(Utils::interval(Utils::now(), $commander->dArrival, 's'), 'lite') . '</span>';
										echo '</div>';
									echo '</div>';
								} else {
									echo '<img src="' . MEDIA . 'map/fleet/ship.png" alt="" class="ship" />';

								}
							echo '</div>';

							if ($commander->statement == Commander::MOVING) {
								echo '<div class="right">';
									echo '<img src="' . MEDIA . 'map/place/place1-2.png" alt="" class="cover" />';
									echo '<span class="top"><a href="' . APP_ROOT . 'map/place-' . $commander->rDestinationPlace . '">' . ($commander->destinationPlaceName == NULL ? 'Planète Rebelle' : $commander->destinationPlaceName) . '</a></span>';
								echo '</div>';
							}
						echo '</div>';
					}

					if (count($base['fleets']) == 0) {
						echo '<em>Aucun commandant affecté autours de cette planète</em>';
					}
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';;