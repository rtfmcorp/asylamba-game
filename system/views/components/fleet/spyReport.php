<?php
# report componant
# in poseidon package

# affiche un rapport d'espionnage

# require 
	# {report}		spyreport
	# {place}		place_spy

echo '<div class="component size3 space">';
	echo '<div class="head skin-1">';
		echo ($spyreport->rEnemy == 0)
			? '<img src="' . MEDIA . 'commander/big/t1-c0.png" alt="' . $spyreport->enemyName . '" />'
			: '<img src="' . MEDIA . 'avatar/medium/' . $spyreport->enemyAvatar . '.png" alt="' . $spyreport->enemyName . '" />';
		echo '<h2>' . $spyreport->placeName . '</h2>';
		echo '<em>' . $spyreport->enemyName . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="situation-content color' . $spyreport->placeColor . ' place1">';
				echo '<div class="toolbar">';
					echo '<span>Opération de ' . Format::number($spyreport->price) . ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits"></span>';
					echo '<span>';
						switch ($spyreport->type) {
							case SpyReport::TYP_NOT_CAUGHT: echo 'L\'ennemi ne sait rien de cet espionnage'; break;
							case SpyReport::TYP_ANONYMOUSLY_CAUGHT: echo 'L\'ennemi ne vous soupçonne pas'; break;
							case SpyReport::TYP_CAUGHT: echo 'L\'ennemi vous a vu'; break;
							default: break;
						}
					echo '</span>';
					echo '<span>' . $spyreport->success . ' % de réussite de l\'espionnage</span>';
					echo '<a href="' . Format::actionBuilder('deletespyreport', ['id' => $spyreport->id]) . '" class="hb" title="supprimer le rapport">&#215;</a>';
				echo '</div>';

				echo '<span class="line-help line-1">I</span>';
				echo '<span class="line-help line-2">II</span>';

				$lLine = 0; $rLine = 0;
				$llp = PlaceResource::get($spyreport->typeOfOrbitalBase, 'l-line-position');
				$rlp = PlaceResource::get($spyreport->typeOfOrbitalBase, 'r-line-position');
				$commanders = unserialize($spyreport->commanders);

				if ($spyreport->success > SpyReport::STEP_FLEET) {
					foreach ($commanders as $commander) {
						$data  = 'data-army="';
						$data .= $spyreport->success > SpyReport::STEP_ARMY
							? json_encode($commander['army'])
							: json_encode(array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1));
						$data .= '"';

						echo '<span class="commander full show-army position-' . $commander['line'] . '-' . ($commander['line'] == 1 ? $llp[$lLine] : $rlp[$rLine]) . '" ' . $data . '>';
							echo ($spyreport->success > SpyReport::STEP_MOVEMENT && $commander['statement'] != COM_AFFECTED)
								? '<img src="' . MEDIA . 'map/fleet/army-away.png" alt="plein" />'
								: '<img src="' . MEDIA . 'map/fleet/army.png" alt="plein" />';
							echo '<span class="info">';
								echo $spyreport->success > SpyReport::STEP_COMMANDER
									? CommanderResources::getInfo($commander['level'], 'grade') . ' <strong>' . $commander['name'] . '</strong><br />'
									: 'Commandant inconnu<br />';
								echo $spyreport->success > SpyReport::STEP_PEV
									? $commander['pev'] . ' Pev'
									: '??? Pev';
								echo ($spyreport->success > SpyReport::STEP_MOVEMENT && $commander['statement'] != COM_AFFECTED)
									? '<br />&#8594; déplacement'
									: NULL;
							echo '</span>';
						echo '</span>';

						if ($commander['line'] == 1) {
							$lLine++;
						} else {
							$rLine++;
						}
					}
				}

				for ($lLine; $lLine < PlaceResource::get($spyreport->typeOfOrbitalBase, 'l-line'); $lLine++) { 
					echo '<span class="commander empty position-1-' . $llp[$lLine] . '">';
						echo $spyreport->success > SpyReport::STEP_FLEET
							? '<img src="' . MEDIA . 'map/fleet/army-empty.png" alt="vide" />'
							: '<img src="' . MEDIA . 'map/fleet/army-unknow.png" alt="vide" />';
					echo '</span>';
				}

				for ($rLine; $rLine < PlaceResource::get($spyreport->typeOfOrbitalBase, 'r-line'); $rLine++) { 
					echo '<span class="commander empty position-2-' . $rlp[$rLine] . '">';
						echo $spyreport->success > SpyReport::STEP_FLEET
							? '<img src="' . MEDIA . 'map/fleet/army-empty.png" alt="vide" />'
							: '<img src="' . MEDIA . 'map/fleet/army-unknow.png" alt="vide" />';
					echo '</span>';
				}

				if ($spyreport->typeOfBase != Place::TYP_EMPTY) {
					$data  = 'data-army="';
					$data .= $spyreport->success > SpyReport::STEP_ARMY
						? json_encode(unserialize($spyreport->shipsInStorage))
						: json_encode(array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1));
					$data .= '"';

					echo '<span class="commander ' . ($spyreport->success > SpyReport::STEP_FLEET && array_sum(unserialize($spyreport->shipsInStorage)) > 0 ? 'full' : 'empty') . ' show-army position-3" ' . $data . '>';
						echo '<img src="' . MEDIA . 'orbitalbase/dock1.png" alt="chantier" />';
					echo '</span>';
				}

				echo '<div class="stellar">';
					echo '<div class="info top">';
						echo PlaceResource::get($spyreport->typeOfOrbitalBase, 'name') . '<br />';
						echo '<strong>' . $spyreport->placeName . '</strong><br />';
						echo Format::numberFormat($spyreport->points) . ' points';
					echo '</div>';
					echo '<div class="info middle">';
						echo 'coordonnées<br />';
						echo '<strong>';
							echo '<a href="' . APP_ROOT . 'map/place-' . $spyreport->rPlace . '">';
								echo Game::formatCoord($spyreport->xPosition, $spyreport->yPosition, $spyreport->position, $spyreport->rSector);
							echo '</a>';
						echo '</strong>';
					echo '</div>';
					echo '<img src="' . MEDIA . 'orbitalbase/place1-' . Game::getSizeOfPlanet($place_spy->population) . '.png" alt="planète" />';
					echo '<div class="info bottom">';
						echo '<strong>' . Format::numberFormat($place_spy->population * 1000000) . '</strong> habitants<br />';
						echo $place_spy->coefResources . ' % coeff. ressource<br />';
						echo $place_spy->coefHistory . ' % coeff. scientifique';
					echo '</div>';
				echo '</div>';

				echo '<div class="attack-link">';
					echo '<a href="#">Attaquer la planète</a>';
				echo '</div>';
			echo '</div>';

			echo '<div class="situation-info">';
				echo '<div class="item">';
					echo 'Ressources dans les entrepôts';
					echo '<span class="value">';
						echo $spyreport->success > SpyReport::STEP_RESOURCES
							? Format::number($spyreport->resources)
							: '???';
						echo ' <img class="icon-color" src="' . MEDIA . 'resources/resource.png" alt="ressources">';
					echo '</span>';
				echo '</div>';

				echo '<div class="item">';
					echo 'Investissement dans le contre-espionnage';
					echo '<span class="value">';
						echo $spyreport->success > SpyReport::STEP_RESOURCES
							? Format::number($spyreport->antiSpyInvest)
							: '???';
						echo ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits">';
					echo '</span>';
				echo '</div>';

				echo '<div class="item">';
					echo 'Revenus des routes commerciales';
					echo '<span class="value">';
						echo $spyreport->success > SpyReport::STEP_RESOURCES
							? Format::number($spyreport->commercialRouteIncome)
							: '???';
						echo ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits">';
					echo '</span>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>