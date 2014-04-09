<?php
# report componant
# in poseidon package

# affiche un rapport d'espionnage

# require 
	# {report}		report_report
	# {place}		place_report


echo '<div class="component size3 space">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'avatar/medium/' . $report_report->enemyAvatar . '.png" alt="' . $report_report->enemyName . '" />';
		echo '<h2>Lieu espionné</h2>';
		echo '<em>' . $report_report->placeName . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="situation-content place1">';

				echo '<span class="hb line-help line-1" title="première ligne, défend les pillages et les conquêtes, vraie ligne de défense">I</span>';
				echo '<span class="hb line-help line-2" title="force de réserve, défend uniquement les conquêtes, utile pour les troupes d\'attaque">II</span>';

				$lLine = 0; $rLine = 0;
				$llp = PlaceResource::get($report_report->typeOfOrbitalBase, 'l-line-position');
				$rlp = PlaceResource::get($report_report->typeOfOrbitalBase, 'r-line-position');
				$commanders = unserialize($report_report->commanders);
				foreach ($commanders as $commander) {

# TODO
					echo '<a href="#" class="commander full position-1-' . (1 == 1 ? $llp[$lLine] : $rlp[$rLine]) . '">';
						echo '<img src="' . MEDIA . 'map/fleet/army.png" alt="plein" />';
						echo '<span class="info">';

						echo '</span>';
					echo '</a>';

					//echo '<a href="' . APP_ROOT . 'fleet/view-movement/commander-' . $commander->getId() . '/sftr-3" class="commander full position-' . $commander->line . '-' . ($commander->line == 1 ? $llp[$lLine] : $rlp[$rLine]) . '">';
						//echo '<img src="' . MEDIA . 'map/fleet/' . (($commander->getStatement() == COM_AFFECTED) ? 'army' : 'army-away') . '.png" alt="plein" />';
						//echo '<span class="info">';
							//echo CommanderResources::getInfo($commander->getLevel(), 'grade') . ' <strong>' . $commander->getName() . '</strong><br />';
							//echo $commander->getPev() . ' Pev';
							/*if ($commander->getStatement() == COM_MOVING) {
								echo '<br />&#8594;	';
								switch ($commander->getTypeOfMove()) {
									case COM_MOVE: echo 'déplacement'; break;
									case COM_LOOT: echo 'pillage'; break;
									case COM_COLO: echo 'colonisation'; break;
									case COM_BACK: echo 'retour'; break;
									default: break;
								}
							}*/
						//echo '</span>';
					//echo '</a>';


				}

				for ($lLine; $lLine < PlaceResource::get($report_report->typeOfOrbitalBase, 'l-line'); $lLine++) { 
					echo '<a href="' . APP_ROOT . 'bases/base-' . $report_report->rPlace . '/view-school" class="commander empty position-1-' . $llp[$lLine] . '">';
						echo '<img src="' . MEDIA . 'map/fleet/army-empty.png" alt="vide" />';
					echo '</a>';
				}

				for ($rLine; $rLine < PlaceResource::get($report_report->typeOfOrbitalBase, 'r-line'); $rLine++) { 
					echo '<a href="' . APP_ROOT . 'bases/base-' . $report_report->rPlace . '/view-school" class="commander empty position-2-' . $rlp[$rLine] . '">';
						echo '<img src="' . MEDIA . 'map/fleet/army-empty.png" alt="vide" />';
					echo '</a>';
				}

				echo '<div class="stellar">';
					echo '<div class="info top">';
						echo PlaceResource::get($report_report->typeOfOrbitalBase, 'name') . '<br />';
						echo '<strong>' . $report_report->placeName . '</strong><br />';
						echo Format::numberFormat($report_report->points) . ' points';
					echo '</div>';
					echo '<div class="info middle">';
						echo 'coordonnées<br />';
						echo '<strong>' . Game::formatCoord($report_report->xPosition, $report_report->yPosition, $report_report->position, $report_report->rSector) . '</strong>';
					echo '</div>';
					echo '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($place_report->population) . '.png" alt="planète" />';
					echo '<div class="info bottom">';
						echo '<strong>' . Format::numberFormat($place_report->population * 1000000) . '</strong> habitants<br />';
						echo $place_report->coefResources . ' % coeff. ressource<br />';
						echo $place_report->coefHistory . ' % coeff. historique';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>