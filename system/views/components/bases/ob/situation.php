<?php
# obSituation component
# in athena.bases package

# affichage de la vue de situation

# require
	# {orbitalBase}		ob_obSituation
	# [{commander}]		commanders_obSituation

echo '<div class="component space size3">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="situation-content place1">';
				echo '<div class="toolbar">';
					echo '<a href="' . APP_ROOT . '/map/base-' . $ob_obSituation->getId() . '">Centrer sur la carte</a>';
					echo '<form action="' . Format::actionBuilder('renamebase', ['baseid' => $ob_obSituation->getId()]) . '" method="POST">';
						echo '<input type="text" name="name" value="' . $ob_obSituation->getName() . '" />';
						echo '<input type="submit" class="button" value=" " />';
					echo '</form>';
				echo '</div>';

				echo '<span class="hb line-help line-1" title="La première ligne de défense est là pour défendre votre planète en cas d\'attaque. Dès qu\'un ennemi vous attaque, il va engager le combat avec une flotte de cette ligne.">I</span>';
				echo '<span class="hb line-help line-2" title="La deuxième ligne de défense est la ligne de réserve, elle ne défendra en aucun cas contre une attaque dont le but est le pillage. Par contre, elle prendra le relais en ce qui concerne la défense face à des envahisseurs si la première ligne est tombée.">II</span>';

				$lLine = 0; $rLine = 0;
				$llp = PlaceResource::get($ob_obSituation->typeOfBase, 'l-line-position');
				$rlp = PlaceResource::get($ob_obSituation->typeOfBase, 'r-line-position');
				foreach ($commanders_obSituation as $commander) {
					echo '<div class="commander position-' . $commander->line . '-' . ($commander->line == 1 ? $llp[$lLine] : $rlp[$rLine]) . '">';
						echo '<a href="' . APP_ROOT . 'fleet/view-movement/commander-' . $commander->getId() . '/sftr-3" class="commander full">';
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
						echo '<a class="link hb ' . ($commander->line == 1 ? 'to-right' : 'to-left') . '" title="changer de ligne" href="' . Format::actionBuilder('changeline', ['id' => $commander->id]) . '"></a>';
					echo '</div>';

					if ($commander->line == 1) {
						$lLine++;
					} else {
						$rLine++;
					}
				}

				for ($lLine; $lLine < PlaceResource::get($ob_obSituation->typeOfBase, 'l-line'); $lLine++) { 
					echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obSituation->getId() . '/view-school" class="commander empty position-1-' . $llp[$lLine] . '">';
						echo '<img src="' . MEDIA . 'map/fleet/army-empty.png" alt="vide" />';
						echo '<span class="info">';
							echo 'Affecter<br />';
							echo 'un officier';
						echo '</span>';
					echo '</a>';
				}

				for ($rLine; $rLine < PlaceResource::get($ob_obSituation->typeOfBase, 'r-line'); $rLine++) { 
					echo '<a href="' . APP_ROOT . 'bases/base-' . $ob_obSituation->getId() . '/view-school" class="commander empty position-2-' . $rlp[$rLine] . '">';
						echo '<img src="' . MEDIA . 'map/fleet/army-empty.png" alt="vide" />';
						echo '<span class="info">';
							echo 'Affecter<br />';
							echo 'un officier';
						echo '</span>';
					echo '</a>';
				}

				echo '<div class="stellar">';
					echo '<div class="info top">';
						echo PlaceResource::get($ob_obSituation->typeOfBase, 'name') . '<br />';
						echo '<strong>' . $ob_obSituation->getName() . '</strong><br />';
						echo Format::numberFormat($ob_obSituation->getPoints()) . ' points';
					echo '</div>';
					echo '<div class="info middle">';
						echo 'coordonnées<br />';
						echo '<strong>' . Game::formatCoord($ob_obSituation->getXSystem(), $ob_obSituation->getYSystem(), $ob_obSituation->getPosition(), $ob_obSituation->getSector()) . '</strong>';
					echo '</div>';
					echo '<img src="' . MEDIA . 'orbitalbase/place1-' . Game::getSizeOfPlanet($ob_obSituation->getPlanetPopulation()) . '.png" alt="planète" />';

					$science = Game::getImprovementFromScientificCoef($ob_obSituation->getPlanetHistory());
					echo '<div class="info bottom">';
						echo '<strong>' . Format::numberFormat($ob_obSituation->getPlanetPopulation() * 1000000) . '</strong> habitants<br />';
						echo '<strong>' . $ob_obSituation->getPlanetResources() . '</strong> % coeff. ressource<br />';
						echo '<strong>' . $science . '</strong> % de bonus scientifique';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
		echo '</div>';
	echo '</div>';
echo '</div>';
?> 