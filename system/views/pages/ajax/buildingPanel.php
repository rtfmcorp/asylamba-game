<?php
include_once ATHENA;


$building 		= Utils::getHTTPData('building');
$currentLevel 	= Utils::getHTTPData('lvl');

echo '<div class="component panel-info size2">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>' . OrbitalBaseResource::getBuildingInfo($building, 'frenchName') . '</h4>';
			echo '<a href="#" class="removeInfoPanel remove-info hb lt" title="fermer le panneau">x</a>';

			echo '<div class="table"><table>';
				echo '<tr>';
					echo '<td class="hb lt" title="niveau du bâtiment">niv.</td>';
					echo '<td class="hb lt" title="prix en ressources du bâtiment">prix</td>';
					echo '<td class="hb lt" title="temps de construction du bâtiment (heures:minutes:secondes) sans bonus">temps</td>';
					if ($building == 1) {
						echo '<td class="hb lt" title="production de ressources par relève sans bonus et au coeff. ressource moyen de 50 %">prod.</td>';
						echo '<td class="hb lt" title="stockage maximum de ressources sans bonus">stockage</td>';
					} elseif (in_array($building, array(2, 3))) {
						echo '<td class="hb lt" title="nombre de pev que le chantier peut stocker">stockage</td>';
					} elseif ($building == 6) {
						echo '<td class="hb lt" title="nombre de routes commerciales que peut avoir le bâtiment">route</td>';
					}
					echo '<td class="hb lt" title="points gagné par le joueur lors de la construction du niveau de bâtiment">points</td>';
				echo '</tr>';

				switch ($building) {
					case 0: $max = 20; break;
					case 1: $max = 20; break;
					case 2: $max = 20; break;
					case 3: $max = 15; break;
					case 4: $max = 5;  break;
					case 5: $max = 20; break;
					case 6: $max = 15; break;
					case 7: $max = 5;  break;
					default:$max = 0;  break;
				}
				
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
							if ($i == OrbitalBaseResource::getInfo($building, 'maxLevel', $j) - 1) {
								if (!$alreadyANote) {
									$alreadyANote = TRUE;
									$noteQuantity++;
									$note .= '<sup>' . $noteQuantity . '</sup>';
								}
								$footnoteArray[$j] = $noteQuantity;
							}
						}
						echo '<td>' . $level . $note . '</td>';
						echo '<td>' . Format::numberFormat(OrbitalBaseResource::getBuildingInfo($building, 'level', $level, 'resourcePrice')) . ' <img src="' .  MEDIA. 'resources/resource.png" alt="ressources" class="icon-color" /></td>';
						echo '<td>' . Chronos::secondToFormat(OrbitalBaseResource::getBuildingInfo($building, 'level', $level, 'time'), 'lite') . ' <img src="' .  MEDIA. 'resources/time.png" alt="relève" class="icon-color" /></td>';
						if ($building == 1) {
							echo '<td>' . Format::numberFormat(Game::resourceProduction(OrbitalBaseResource::getBuildingInfo($building, 'level', $level, 'refiningCoefficient'), 50)) . ' <img src="' .  MEDIA. 'resources/resource.png" alt="ressources" class="icon-color" /></td>';
							echo '<td>' . Format::numberFormat(OrbitalBaseResource::getBuildingInfo($building, 'level', $level, 'storageSpace')) . ' <img src="' .  MEDIA. 'resources/resource.png" alt="ressources" class="icon-color" /></td>';
						} elseif (in_array($building, array(2, 3))) {
							echo '<td>' . Format::numberFormat(OrbitalBaseResource::getBuildingInfo($building, 'level', $level, 'storageSpace')) . ' <img src="' .  MEDIA. 'resources/pev.png" alt="pev" class="icon-color" /></td>';
						} elseif ($building == 6) {
							echo '<td>' . Format::numberFormat(OrbitalBaseResource::getBuildingInfo($building, 'level', $level, 'nbRoutesMax')) . '</td>';
						}
						echo '<td>' . OrbitalBaseResource::getBuildingInfo($building, 'level', $level, 'points') . '</td>';
					echo '</tr>';
				}
			echo '</table></div>';

			# generate the footnotes
			include_once GAIA;
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
		echo '</div>';
	echo '</div>';
echo '</div>';
?>