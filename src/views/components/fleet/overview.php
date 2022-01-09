<?php
# listFleetIncoming component
# in ares package

# affichage de la liste des flottes qui attaquent le joueurs

# require
	# [{commander}]		commander_listFleetIncoming

use App\Modules\Athena\Resource\ShipResource;
use App\Modules\Gaia\Resource\PlaceResource;
use App\Classes\Library\Format;
use App\Modules\Ares\Resource\CommanderResources;

$container = $this->getContainer();
$sessionToken = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class)->get('token');
$appRoot = $container->getParameter('app_root');

echo '<div class="component size3 table-fleet">';
	echo '<div class="head skin-1">';
		echo '<h1>Aperçu des armées</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<table>';
				echo '<tr>';
					echo '<th class="base"></th>';
					echo '<th class="large"></th>';
					for ($i = 0; $i < 12; $i++) {
						echo '<th><span>' . ShipResource::getInfo($i, 'codeName') . '</span></th>';
					}
					echo '<th><span>PEV</span></th>';
				echo '</tr>';

				$totalShips = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

				foreach ($obsets as $base) {
					$totalShipsBase = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

					echo '<tr>';
						echo '<td rowspan="' . (count($base['fleets']) + 2) . '" class="base">';
							echo '<a href="' . $appRoot . 'map/place-' . $base['info']['id'] . '">';
								echo PlaceResource::get($base['info']['type'], 'name') . '<br />';
								echo '<strong>' . $base['info']['name'] . '</strong>';
							echo '</a>';
						echo '</td>';

						echo '<td class="large">';
							echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $base['info']['id'], 'page' => 'dock1']) . '">';
								echo 'Vaisseaux dans les hangars';
							echo '</a>';
						echo '</td>';
						$linePEV = 0;

						for ($i = 0; $i < count($totalShipsBase); $i++) {
							echo '<td ' . ($i == 6 ? 'class="sep"' : NULL) . '>';
								echo $base['dock'][$i] == 0 ? '<span class="zero-value">' : '<span>';
									echo $base['dock'][$i];
								echo '<span>';
							echo '</td>';

							$linePEV += ShipResource::getInfo($i, 'pev') * $base['dock'][$i];
							$totalShips[$i] += $base['dock'][$i];
							$totalShipsBase[$i] += $base['dock'][$i];
						}
						echo '<td>' . $linePEV . '</td>';
					echo '</tr>';

					foreach ($base['fleets'] as $commander) {
						$commanderShips = $commander->getNbrShipByType();
						$linePEV = 0;

						echo '<tr>';
							echo '<td class="large">';
								echo '<a href="' . $appRoot . 'fleet/commander-' . $commander->id . '/sftr-4">';
									echo CommanderResources::getInfo($commander->level, 'grade') . ' <strong>' . $commander->name . '</strong>';
								echo '</a>';
							echo '</td>';
							for ($i = 0; $i < count($totalShipsBase); $i++) { 
								echo '<td ' . ($i == 6 ? 'class="sep"' : NULL) . '>';
									echo $commanderShips[$i] == 0 ? '<span class="zero-value">' : '<span>';
										echo $commanderShips[$i];
									echo '<span>';
								echo '</td>';

								$linePEV += ShipResource::getInfo($i, 'pev') * $commanderShips[$i];
								$totalShips[$i] += $commanderShips[$i];
								$totalShipsBase[$i] += $commanderShips[$i];
							}
							echo '<td>' . $linePEV . '</td>';
						echo '</tr>';
					}

					echo '<tr class="total">';
						echo '<td class="large">Total sur la planète</td>';
						$linePEV = 0;

						for ($i = 0; $i < count($totalShipsBase); $i++) { 
							echo '<td ' . ($i == 6 ? 'class="sep"' : NULL) . '>';
								echo $totalShipsBase[$i] == 0 ? '<span class="zero-value">' : '<span>';
									echo $totalShipsBase[$i];
								echo '<span>';
							echo '</td>';

							$linePEV += ShipResource::getInfo($i, 'pev') * $totalShipsBase[$i];
						}
						echo '<td>' . $linePEV . '</td>';
					echo '</tr>';
				}

				echo '<tr>';
					echo '<td class="base"></td>';
					echo '<td class="large">Total général</td>';
					$linePEV = 0;

					for ($i = 0; $i < count($totalShips); $i++) { 
						echo '<td ' . ($i == 6 ? 'class="sep"' : NULL) . '>';
							echo $totalShips[$i] == 0 ? '<span class="zero-value">' : '<span>';
								echo $totalShips[$i];
							echo '<span>';
						echo '</td>';

						$linePEV += ShipResource::getInfo($i, 'pev') * $totalShips[$i];
					}
					echo '<td>' . $linePEV . '</td>';
				echo '</tr>';
			echo '</table>';
		echo '</div>';
	echo '</div>';
echo '</div>';
