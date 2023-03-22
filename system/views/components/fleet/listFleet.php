<?php
# listFleetIncoming component
# in ares package

# affichage de la liste des flottes qui attaquent le joueurs

# require
	# [{commander}]		commander_listFleetIncoming

use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Classes\Library\Game;
use Asylamba\Classes\Library\Chronos;

$container = $this->getContainer();
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');
$mediaPath = $container->getParameter('media');
$appRoot = $container->getParameter('app_root');

echo '<div class="component size3 list-fleet">';
	echo '<div class="head skin-1">';
		echo '<h1>Centre des opérations</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a class="top-right-button" href="' . Format::actionBuilder('switchparams', $sessionToken, ['params' => Params::LIST_ALL_FLEET]) . '">' . ($request->cookies->get('p' . Params::LIST_ALL_FLEET, Params::$params[Params::LIST_ALL_FLEET]) ? 'Afficher uniquement la base courante' : 'Afficher toutes les bases') . '</a>';

			foreach ($obsets as $base) {
				echo '<div class="set-fleet">';
					echo '<img src="' . $mediaPath . 'map/place/place' . $base['info']['img'] . '.png" alt="' . $base['info']['name'] . '" class="place" />';

					echo '<h2>';
						echo PlaceResource::get($base['info']['type'], 'name') . ' ';
						echo $base['info']['name'];
						echo ' <a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $base['info']['id'], 'page' => 'school']) . '">(affecter un officier)</a>';
					echo '</h2>';
                    
					foreach ($base['fleets'] as $commander) {

						$reversed = $commander->rPlayer != $session->get('playerId') || $commander->travelType == Commander::BACK;

						
						echo '<div class="item color' . $commander->playerColor . '">';
							echo '<div class="left">';
								if ($commander->rPlayer != $session->get('playerId')) {
									echo '<img src="' . $mediaPath . 'map/action/shield.png" alt="" class="status" />';
								} elseif ($commander->statement == Commander::AFFECTED) {
									echo '<img src="' . $mediaPath . 'map/action/anchor.png" alt="" class="status" />';
								} elseif ($commander->statement == Commander::MOVING) {
									switch ($commander->travelType) {
										case Commander::MOVE: echo '<img src="' . $mediaPath . 'map/action/move.png" alt="" class="status" />'; break;
										case Commander::LOOT: echo '<img src="' . $mediaPath . 'map/action/loot.png" alt="" class="status" />'; break;
										case Commander::COLO: echo '<img src="' . $mediaPath . 'map/action/colo.png" alt="" class="status" />'; break;
										case Commander::BACK: echo '<img src="' . $mediaPath . 'map/action/back.png" alt="" class="status" />'; break;
										default: break;
									}
								}
								echo '<span class="top">';
									echo  CommanderResources::getInfo($commander->level, 'grade') . ' <strong>' . $commander->name . '</strong>, ' ;										

									if ($commander->rPlayer != $session->get('playerId')) {
										
											switch ($commander->travelType) {
												case Commander::LOOT: $type = 'tente de vous piller'; break;
												case Commander::COLO: $type = 'tente de vous conquérir'; break;
												default: $type = 'erreur'; break;
											}
											echo $type;
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

									echo (($commander->rPlayer == $session->get('playerId')) )
										? '&#8194;|&#8194;' . Format::number($commander->getPev()) . ' pev'
										: '&#8194;';

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
												? '<img src="' . $mediaPath . 'map/fleet/ship-reversed.png" alt="" class="ship" />'
												: '<img src="' . $mediaPath . 'map/fleet/ship.png" alt="" class="ship" />';
											echo '<span class="time">' . Chronos::secondToFormat($restTime, 'lite') . '</span>';
										echo '</div>';
									echo '</div>';
								} else {
									echo '<img src="' . $mediaPath . 'map/fleet/ship.png" alt="" class="ship" />';
								}
							echo '</div>';

							if ($commander->statement == Commander::MOVING) {
								echo '<div class="right">';
									echo $commander->travelType == Commander::BACK
										? '<img src="' . $mediaPath . 'map/place/place1-' . Game::getSizeOfPlanet($commander->startPlacePop) . '.png" alt="" class="cover" />'
										: '<img src="' . $mediaPath . 'map/place/place1-' . Game::getSizeOfPlanet($commander->destinationPlacePop) . '.png" alt="" class="cover" />';
									echo '<span class="top">';
										echo $reversed
											? '<a href="' . $appRoot . 'map/place-' . $commander->rStartPlace . '">' . $commander->startPlaceName . '</a>'
											: '<a href="' . $appRoot . 'map/place-' . $commander->rDestinationPlace . '">' . $commander->destinationPlaceName . '</a>';

										if ($commander->rPlayer != $session->get('playerId')) {
											echo ' (<a href="' . $appRoot . 'embassy/player-' . $commander->rPlayer . '">' . $commander->playerName . '</a>)';
										}
									echo '</span>';
								echo '</div>';
							}

							if ($commander->rPlayer == $session->get('playerId')) {
								echo '<a href="' . $appRoot . 'fleet/commander-' . $commander->id . '/sftr-2" class="show-commander ' . ($request->query->has('commander') && $request->query->get('commander') == $commander->id ? 'active' : NULL) . '"></a>';
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
