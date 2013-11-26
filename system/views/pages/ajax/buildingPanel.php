<?php
include_once ATHENA;

$building = CTR::$get->get('building');

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

				if (in_array($building, array(3, 6))) {
					$max = 15;
				} elseif (in_array($building, array(4, 7))) {
					$max = 5;
				} else {
					$max = 20;
				}
				
				for ($i = 0; $i < $max; $i++) {
					$level = $i + 1;
					echo '<tr>';
						echo '<td>' . ((in_array($building, array(3, 6)) && $i >= 10) ? '* ' : '') . $level . '</td>';
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

			if (in_array($building, array(3, 6))) {
				echo '<p class="info">* ces niveaux ne sont pas disponible si vous avez fait le choix de constuire en ';
				echo 'premier ' . (($building == 3) ? 'la plateforme commerciale' : 'le chantier de ligne') . '</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>