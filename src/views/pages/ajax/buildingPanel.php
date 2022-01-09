<?php

use App\Classes\Library\Format;
use App\Classes\Library\Chronos;
use App\Classes\Library\Game;
use App\Modules\Athena\Model\OrbitalBase;
use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Modules\Gaia\Resource\PlaceResource;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');
$request = $this->getContainer()->get('app.request');
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);

$building 		= $request->query->get('building');
$currentLevel 	= $request->query->get('lvl');

echo '<div class="component panel-info size2">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>' . $orbitalBaseHelper->getBuildingInfo($building, 'frenchName') . '</h4>';
			echo '<a href="#" class="removeInfoPanel remove-info hb lt" title="fermer le panneau">x</a>';

			echo '<div class="table"><table>';
				echo '<tr>';
					echo '<td class="hb lt" title="niveau du bâtiment">niv.</td>';
					echo '<td class="hb lt" title="prix en ressources du bâtiment">prix</td>';
					echo '<td class="hb lt" title="temps de construction du bâtiment (heures:minutes:secondes) sans bonus">temps</td>';
					switch ($building) {
						case OrbitalBaseResource::GENERATOR:
							echo '<td class="hb lt" title="nombre d\'éléments dans la file d\'attente de construction">queues</td>';
							break;
						case OrbitalBaseResource::REFINERY:
							echo '<td class="hb lt" title="production de ressources par relève sans bonus et au coeff. ressource moyen de 50 %">prod.</td>';
							break;
						case OrbitalBaseResource::STORAGE:
							echo '<td class="hb lt" title="stockage maximum de ressources sans bonus">stockage</td>';
							break;
						case OrbitalBaseResource::DOCK1:
							echo '<td class="hb lt" title="nombre d\'éléments dans la file d\'attente de construction">queues</td>';
							echo '<td class="hb lt" title="nombre de PEV que le chantier peut stocker">stockage</td>';
							break;
						case OrbitalBaseResource::DOCK2:
							echo '<td class="hb lt" title="nombre d\'éléments dans la file d\'attente de construction">queues</td>';
							echo '<td class="hb lt" title="nombre de PEV que le chantier peut stocker">stockage</td>';
							break;
						case OrbitalBaseResource::TECHNOSPHERE:
							echo '<td class="hb lt" title="nombre d\'éléments dans la file d\'attente de construction">queues</td>';
							break;
						case OrbitalBaseResource::COMMERCIAL_PLATEFORME:
							echo '<td class="hb lt" title="nombre de vaisseaux de transports">vaisseaux</td>';
							break;
						case OrbitalBaseResource::RECYCLING:
							echo '<td class="hb lt" title="nombre de collecteurs">collecteurs</td>';
							break;
						case OrbitalBaseResource::SPATIOPORT:
							echo '<td class="hb lt" title="nombre de routes commerciales disponibles">routes</td>';
							break;
						default:
							break;
					}
					echo '<td class="hb lt" title="points gagné par le joueur lors de la construction du niveau de bâtiment">points</td>';
				echo '</tr>';

				$max = $orbitalBaseHelper->getBuildingInfo($building, 'maxLevel', OrbitalBase::TYP_CAPITAL);
				
				$noteQuantity = 0;
				$footnoteArray = array();
				for ($i = 0; $i < $max; $i++) {
					$level = $i + 1;

					$state = NULL;
					if ($currentLevel !== FALSE) {
						if ($currentLevel > $level) {
							$state = 'class="small-grey"';
						} elseif ($currentLevel == $level) {
							$state = 'class="active"';
						} else {
							$state = NULL;
						}
					}
					echo '<tr ' . $state . '>';
						# generate the exponents for the footnotes
						$alreadyANote = FALSE;
						$note = '';
						for ($j = 0; $j < 4; $j++) { 
							if ($i == $orbitalBaseHelper->getInfo($building, 'maxLevel', $j) - 1) {
								if (!$alreadyANote) {
									$alreadyANote = TRUE;
									$noteQuantity++;
									$note .= '<sup>' . $noteQuantity . '</sup>';
								}
								$footnoteArray[$j] = $noteQuantity;
							}
						}
						echo '<td>' . $level . $note . '</td>';
						echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'resourcePrice')) . ' <img src="' .  $mediaPath. 'resources/resource.png" alt="ressources" class="icon-color" /></td>';
						echo '<td>' . Chronos::secondToFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'time'), 'lite') . ' <img src="' .  $mediaPath. 'resources/time.png" alt="relève" class="icon-color" /></td>';
						switch ($building) {
							case OrbitalBaseResource::GENERATOR:
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'nbQueues')) . '</td>';
								break;
							case OrbitalBaseResource::REFINERY:
								echo '<td>' . Format::numberFormat(Game::resourceProduction($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'refiningCoefficient'), 50)) . ' <img src="' .  $mediaPath. 'resources/resource.png" alt="ressources" class="icon-color" /></td>';
								break;
							case OrbitalBaseResource::STORAGE:
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'storageSpace')) . ' <img src="' .  $mediaPath. 'resources/resource.png" alt="ressources" class="icon-color" /></td>';
								break;
							case OrbitalBaseResource::DOCK1:
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'nbQueues')) . '</td>';
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'storageSpace')) . ' <img src="' .  $mediaPath. 'resources/pev.png" alt="pev" class="icon-color" /></td>';
								break;
							case OrbitalBaseResource::DOCK2:
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'nbQueues')) . '</td>';
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'storageSpace')) . ' <img src="' .  $mediaPath. 'resources/pev.png" alt="pev" class="icon-color" /></td>';
								break;
							case OrbitalBaseResource::TECHNOSPHERE:
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'nbQueues')) . '</td>';
								break;
							case OrbitalBaseResource::COMMERCIAL_PLATEFORME:
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'nbCommercialShip')) . '</td>';
								break;
							case OrbitalBaseResource::RECYCLING:
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'nbRecyclers')) . '</td>';
								break;
							case OrbitalBaseResource::SPATIOPORT:
								echo '<td>' . Format::numberFormat($orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'nbRoutesMax')) . '</td>';
								break;
							default:
								break;
						}
						echo '<td>' . $orbitalBaseHelper->getBuildingInfo($building, 'level', $level, 'points') . '</td>';
					echo '</tr>';
				}
			echo '</table></div>';

			# generate the footnotes
			$quantityArray = array_count_values($footnoteArray);
			echo '<p class="info">';
			foreach ($quantityArray as $footnote => $quantity) {
				echo '<sup>' . $footnote . '</sup> Niveau maximal pour une base orbitale de type ';
				$qty = 0;
				foreach ($footnoteArray as $type => $footnoteId) {
					if ($footnoteId == $footnote) {
						$qty++;
						if ($qty > 1) {
							echo ($qty == $quantity) ? ' et ' : ', ';
						}
						echo PlaceResource::get($type, 'name');
					}
				}
				echo '.<br />';
			}
			echo '</p>';

			echo '<h4>A propos</h4>';
			echo '<p class="info">' . $orbitalBaseHelper->getBuildingInfo($building, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
