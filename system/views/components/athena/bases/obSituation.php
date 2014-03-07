<?php
# obSituation component
# in athena.bases package

# affichage de la vue de situation

# require
	# {orbitalBase}		ob_obSituation
	# [{commander}]		commanders_obSituation

echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/situation.png" alt="" />';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo 'test';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component size3">';
	echo '<div class="head">';
		echo '<h1>Vue de situation</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="situation-content place1">';
				echo '<div class="toolbar">';
					echo '<a href="#" class="hb lb" title="pas encore implémenté">Abandonner la planète</a>';
					echo '<a href="' . APP_ROOT . '/map/base-' . $ob_obSituation->getId() . '">Centrer sur la carte</a>';
					echo '<form action="' . APP_ROOT . 'action/a-renamebase/baseid-' . $ob_obSituation->getId() . '" method="POST">';
						echo '<input type="text" name="name" value="' . $ob_obSituation->getName() . '" />';
						echo '<input type="submit" class="button" value=" " />';
					echo '</form>';
				echo '</div>';

				echo '<span class="hb line-help line-1" title="première ligne, défend les pillages et les conquêtes, vraie ligne de défense">?</span>';
				echo '<span class="hb line-help line-2" title="force de réserve, défend uniquement les conquêtes, utile pour les troupes d\'attaque">?</span>';
				
				for ($i = 0; $i < 3; $i++) { 
					if (isset($commanders_obSituation[$i])) {
						$commander = $commanders_obSituation[$i];
						echo '<a href="' . APP_ROOT . 'fleet/view-movement/commander-' . $commander->getId() . '/sftr-3" class="commander full position-1-' . ($i + 1) . '">';
							echo '<img src="' . MEDIA . 'map/fleet/' . (($commander->getStatement() == COM_AFFECTED) ? 'army' : 'army-away') . '.png" alt="plein" />';
							echo '<span class="info">';
								echo CommanderResources::getInfo($commander->getLevel(), 'grade') . ' <strong>' . $commander->getName() . '</strong><br />';
								echo $commander->getPev() . ' Pev';
								if ($commander->getStatement() == COM_MOVING) {
									echo '<br />&#8594;	';
									switch ($commander->getTypeOfMove()) {
										case COM_MOVE: echo 'déplacement'; break;
										case COM_LOOT: echo 'pillage'; break;
										case COM_COLO: echo 'colonisation'; break;
										case COM_BACK: echo 'retour'; break;
										default: break;
									}
								}
							echo '</span>';
						echo '</a>';
					} else {
						echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obSituation->getId() . '/view-school" class="commander empty position-1-' . ($i + 1) . '">';
							echo '<img src="' . MEDIA . 'map/fleet/army-empty.png" alt="vide" />';
							echo '<span class="info">';
								echo 'Affecter<br />';
								echo 'un officier';
							echo '</span>';
						echo '</a>';
					}
				}

				echo '<a class="commander full position-2-1" href="/expansion/dev12/fleet/view-movement/commander-1/sftr-3"><img alt="plein" src="http://localhost/expansion/dev12/public/media/map/fleet/army.png"><span class="info">Lieutenant <strong>Kolver</strong><br>0 Pev</span></a>';
				#echo '<a class="commander full position-2-2" href="/expansion/dev12/fleet/view-movement/commander-1/sftr-3"><img alt="plein" src="http://localhost/expansion/dev12/public/media/map/fleet/army.png"><span class="info">Lieutenant <strong>Kolver</strong><br>0 Pev</span></a>';
				echo '<a class="commander full position-2-3" href="/expansion/dev12/fleet/view-movement/commander-1/sftr-3"><img alt="plein" src="http://localhost/expansion/dev12/public/media/map/fleet/army.png"><span class="info">Lieutenant <strong>Kolver</strong><br>0 Pev</span></a>';

				echo '<div class="stellar">';
					echo '<div class="info top">';
						echo 'Base militaire<br />';
						echo '<strong>' . $ob_obSituation->getName() . '</strong><br />';
						echo Format::numberFormat($ob_obSituation->getPoints()) . ' points';
					echo '</div>';
					echo '<div class="info middle">';
						echo 'coordonnées<br />';
						echo '<strong>' . Game::formatCoord($ob_obSituation->getXSystem(), $ob_obSituation->getYSystem(), $ob_obSituation->getPosition(), $ob_obSituation->getSector()) . '</strong>';
					echo '</div>';
					echo '<img src="' . MEDIA . 'map/place/place1-' . Game::getSizeOfPlanet($ob_obSituation->getPlanetPopulation()) . '.png" alt="planète" />';
					echo '<div class="info bottom">';
						echo '<strong>' . Format::numberFormat($ob_obSituation->getPlanetPopulation() * 1000000) . '</strong> habitants<br />';
						echo $ob_obSituation->getPlanetResources() . ' % coeff. ressource<br />';
						echo $ob_obSituation->getPlanetHistory() . ' % coeff. historique';
					echo '</div>';
				echo '</div>';

			echo '</div>';
			echo '<div class="situation-info">';
				echo '<div class="item">';
					echo '<span class="label">ressources en stock</span>';
					echo '<span class="value">' . Format::numberFormat($ob_obSituation->getResourcesStorage()) . '</span>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?> 