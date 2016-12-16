<?php
# listFleetIncoming component
# in ares package

# affichage de la liste des flottes qui attaquent le joueurs

# require
	# [{commander}]		commander_listFleetIncoming

use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Chronos;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$sessionToken = $session->get('token');

echo '<div class="component size3 list-fleet">';
	echo '<div class="head skin-1">';
		echo '<h1>Centre des opérations</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a class="top-right-button" href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::LIST_ALL_FLEET]) . '">' . ($request->cookies->get('p' . Params::LIST_ALL_FLEET, Params::LIST_ALL_FLEET) ? 'Afficher uniquement la base courante' : 'Afficher toutes les bases') . '</a>';

			foreach ($obsets as $base) {
				echo '<div class="set-fleet">';
					echo '<img src="' . MEDIA . 'map/place/place' . $base['info']['img'] . '.png" alt="' . $base['info']['name'] . '" class="place" />';

					echo '<h2>';
						echo PlaceResource::get($base['info']['type'], 'name') . ' ';
						echo $base['info']['name'];
						echo ' <a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $base['info']['id'], 'page' => 'school']) . '">(affecter un officier)</a>';
					echo '</h2>';

					foreach ($base['fleets'] as $commander) {
						$step = 0;
						$reversed = $commander->rPlayer != $session->get('playerId') || $commander->travelType == Commander::BACK;

						if ($commander->rPlayer != $session->get('playerId')) {
							for ($i = 0; $i < $session->get('playerEvent')->size(); $i++) {
								$event = $session->get('playerEvent')->get($i);
								if ($event->get('eventId') == $commander->getId() && $event->exist('eventInfo')) {
									foreach ($event->get('eventInfo')->get('inCircle') as $date) {
										if (strtotime(Utils::now()) >= strtotime($date)) { $step++; } else { break; }
									}
								}
							}
						}

						echo '<div class="item color' . $commander->playerColor . '">';
							echo '<div class="left">';
								if ($commander->rPlayer != $session->get('playerId')) {
									echo '<img src="' . MEDIA . 'map/action/shield.png" alt="" class="status" />';
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
									echo (($commander->rPlayer == $session->get('playerId')) || ($commander->rPlayer != $session->get('playerId') && $step >= 2))
										? CommanderResources::getInfo($commander->level, 'grade') . ' <strong>' . $commander->name . '</strong>, '
										: 'Officier inconnu, ';

									if ($commander->rPlayer != $session->get('playerId')) {
										if ($step >= 2) {
											switch ($commander->getTypeOfMove()) {
												case Commander::LOOT: $type = 'tente de vous piller'; break;
												case Commander::COLO: $type = 'tente de vous conquérir'; break;
												default: $type = 'erreur'; break;
											}
										} else {
											echo 's\'approche de vous';
										}
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

									echo (($commander->rPlayer == $session->get('playerId')) || ($commander->rPlayer != $session->get('playerId') && $step >= 3))
										? '&#8194;|&#8194;' . Format::number($commander->getPev()) . ' pev'
										: '&#8194;|&#8194;??? pev';

									if ($commander->rPlayer == $session->get('playerId') && $commander->statement == Commander::MOVING && $commander->travelType != Commander::BACK) {
										echo '&#8195;<a class="confirm" href="' . Format::actionBuilder('cancelmove', $sessionToken, ['commanderid' => $commander->id]) . '">annuler la mission</a>';
									}
								echo '</span>';
							echo '</div>';

							echo '<div class="center ' . ($reversed ? 'reversed' : NULL) . '">';
								if ($commander->statement == Commander::MOVING) {
									$passeTime = $reversed
										? Utils::interval($commander->dArrival, Utils::now(), 's')
										: Utils::interval($commander->dStart, Utils::now(), 's');
									$restTime  = Utils::interval($commander->dArrival, Utils::now(), 's');
									$totalTime = Utils::interval($commander->dStart, $commander->dArrival, 's');

									echo '<div class="progress-ship" data-progress-current-time="' . $passeTime . '" data-progress-total-time="' . $totalTime . '" data-progress-reverse="' . ($reversed ? 'true' : 'false') . '">';
											echo '<div class="bar" style="width: ' . Format::percent($passeTime, $totalTime, FALSE) . '%;">';
											echo $reversed
												? '<img src="' . MEDIA . 'map/fleet/ship-reversed.png" alt="" class="ship" />'
												: '<img src="' . MEDIA . 'map/fleet/ship.png" alt="" class="ship" />';
											echo '<span class="time">' . Chronos::secondToFormat($restTime, 'lite') . '</span>';
										echo '</div>';
									echo '</div>';
								} else {
									echo '<img src="' . MEDIA . 'map/fleet/ship.png" alt="" class="ship" />';
								}
							echo '</div>';

							if ($commander->statement == Commander::MOVING) {
								echo '<div class="right">';
									echo $commander->travelType == Commander::BACK
										? '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($commander->startPlacePop) . '.png" alt="" class="cover" />'
										: '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($commander->destinationPlacePop) . '.png" alt="" class="cover" />';
									echo '<span class="top">';
										echo $reversed
											? '<a href="' . APP_ROOT . 'map/place-' . $commander->rStartPlace . '">' . $commander->startPlaceName . '</a>'
											: '<a href="' . APP_ROOT . 'map/place-' . $commander->rDestinationPlace . '">' . $commander->destinationPlaceName . '</a>';

										if ($commander->rPlayer != $session->get('playerId')) {
											echo ' (<a href="' . APP_ROOT . 'embassy/player-' . $commander->rPlayer . '">' . $commander->playerName . '</a>)';
										}
									echo '</span>';
								echo '</div>';
							}

							if ($commander->rPlayer == $session->get('playerId')) {
								echo '<a href="' . APP_ROOT . 'fleet/commander-' . $commander->id . '/sftr-2" class="show-commander ' . ($request->query->has('commander') && $request->query->get('commander') == $commander->id ? 'active' : NULL) . '"></a>';
							}
						echo '</div>';
					}

					if (count($base['fleets']) == 0) {
						echo '<em>Aucun commandant affecté autour de cette planète</em>';
					}
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';