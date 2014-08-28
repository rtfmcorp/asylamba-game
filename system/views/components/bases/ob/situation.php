<?php
# obSituation component
# in athena.bases package

# affichage de la vue de situation

# require
	# {orbitalBase}		ob_obSituation
	# [{commander}]		commanders_obSituation

echo '<div class="component generator">';
	echo '<div class="head skin-2">';
		echo '<h2>Vue de situation</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="build-item base-type">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'orbitalbase/base-type-' . $ob_obSituation->typeOfBase . '.jpg" alt="' . PlaceResource::get($ob_obSituation->typeOfBase, 'name') . '">';
					echo '<strong>' . $ob_obSituation->getName() . '</strong>';
					echo '<em>' . PlaceResource::get($ob_obSituation->typeOfBase, 'name') . '</em>';
				echo '</div>';

				echo '<p class="desc">' . PlaceResource::get($ob_obSituation->typeOfBase, 'desc') . '</p>';

				echo '<div class="list-choice">';
					echo '<button class="item-1 ' . ($ob_obSituation->typeOfBase == OrbitalBase::TYP_NEUTRAL ? 'done' : NULL) . '">';
						echo '<img src="' . MEDIA . 'orbitalbase/base-type-0.jpg" alt="' . PlaceResource::get($ob_obSituation->typeOfBase, 'name') . '">';
					echo '</button>';

					echo '<button class="item-2 ' . ($ob_obSituation->typeOfBase == OrbitalBase::TYP_COMMERCIAL ? 'done' : NULL) . '">';
						echo '<img src="' . MEDIA . 'orbitalbase/base-type-1.jpg" alt="' . PlaceResource::get($ob_obSituation->typeOfBase, 'name') . '">';
					echo '</button>';

					echo '<button class="item-3 ' . ($ob_obSituation->typeOfBase == OrbitalBase::TYP_MILITARY ? 'done' : NULL) . '">';
						echo '<img src="' . MEDIA . 'orbitalbase/base-type-2.jpg" alt="' . PlaceResource::get($ob_obSituation->typeOfBase, 'name') . '">';
					echo '</button>';

					echo '<button class="item-4 ' . ($ob_obSituation->typeOfBase == OrbitalBase::TYP_CAPITAL ? 'done' : NULL) . '">';
						echo '<img src="' . MEDIA . 'orbitalbase/base-type-3.jpg" alt="' . PlaceResource::get($ob_obSituation->typeOfBase, 'name') . '">';
					echo '</button>';
				echo '</div>';

				echo '<div class="list-desc">';
					echo '<div class="desc-choice">';
						echo '<h4>' . PlaceResource::get(OrbitalBase::TYP_NEUTRAL, 'name') . '</h4>';
						$fleetQuantity = PlaceResource::get(OrbitalBase::TYP_NEUTRAL, 'l-line') + PlaceResource::get(OrbitalBase::TYP_NEUTRAL, 'r-line');
						echo '<p><strong class="short">Flottes</strong>' . $fleetQuantity . '</p>';
						echo '<p><strong class="short">Impôt</strong>' . (PlaceResource::get(OrbitalBase::TYP_NEUTRAL, 'tax') * 100) . '%</p>';
					echo '</div>';

					echo '<div class="desc-choice">';
						if (($ob_obSituation->typeOfBase == OrbitalBase::TYP_NEUTRAL && CTR::$data->get('playerInfo')->get('credit') >= PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price') && $ob_obSituation->levelGenerator >= OBM_LEVEL_MIN_TO_CHANGE_TYPE) || (($ob_obSituation->typeOfBase == OrbitalBase::TYP_MILITARY || $ob_obSituation->typeOfBase == OrbitalBase::TYP_CAPITAL) && CTR::$data->get('playerInfo')->get('credit') >= PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price'))) {
							echo '<a href="' . APP_ROOT . 'action/a-changebasetype/baseid-' . $ob_obSituation->getId() . '/type-' . OrbitalBase::TYP_COMMERCIAL . '" class="button">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'name') . '<br />';
								echo  Format::numberFormat(PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price'));
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</a>';
						} elseif ($ob_obSituation->typeOfBase == OrbitalBase::TYP_COMMERCIAL) {
							# do nothing
						} else {
							echo '<span class="button disable">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'name') . '<br />';
								echo  Format::numberFormat(PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price'));
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</span>';
						}
						echo '<h4>Avantages &amp; Inconvénients</h4>';
						$fleetQuantity = PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'l-line') + PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'r-line');
						echo '<p><strong class="short">Flottes</strong>' . $fleetQuantity . '</p>';
						echo '<p><strong class="short">Impôt</strong>' . (PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'tax') * 100) . '%</p>';
						echo '<p><strong>Technologies</strong>Orienté commerce et production</p>';
						echo '<p><strong>Bâtiments</strong>Raffinerie et Plateforme Commerciale au niveau maximum</p>'; // et Générateur de Gravité
						echo '<hr />';
						echo '<p><strong>Nécessite</strong>Générateur niveau ' . OBM_LEVEL_MIN_TO_CHANGE_TYPE . '</p>';
					echo '</div>';

					echo '<div class="desc-choice">';
						if (($ob_obSituation->typeOfBase == OrbitalBase::TYP_NEUTRAL && CTR::$data->get('playerInfo')->get('credit') >= PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price') && $ob_obSituation->levelGenerator >= OBM_LEVEL_MIN_TO_CHANGE_TYPE) || (($ob_obSituation->typeOfBase == OrbitalBase::TYP_COMMERCIAL || $ob_obSituation->typeOfBase == OrbitalBase::TYP_CAPITAL) && CTR::$data->get('playerInfo')->get('credit') >= PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price'))) {
							echo '<a href="' . APP_ROOT . 'action/a-changebasetype/baseid-' . $ob_obSituation->getId() . '/type-' . OrbitalBase::TYP_MILITARY . '" class="button">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_MILITARY, 'name') . '<br />';
								echo  Format::numberFormat(PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price'));
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</a>';
						} elseif ($ob_obSituation->typeOfBase == OrbitalBase::TYP_MILITARY) {
							# do nothing
						} else {
							echo '<span class="button disable">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_MILITARY, 'name') . '<br />';
								echo  Format::numberFormat(PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price'));
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</span>';
						}
						echo '<h4>Avantages &amp; Inconvénients</h4>';
						$fleetQuantity = PlaceResource::get(OrbitalBase::TYP_MILITARY, 'l-line') + PlaceResource::get(OrbitalBase::TYP_MILITARY, 'r-line');
						echo '<p><strong class="short">Flottes</strong>' . $fleetQuantity . '</p>';
						echo '<p><strong class="short">Impôt</strong>' . (PlaceResource::get(OrbitalBase::TYP_MILITARY, 'tax') * 100) . '%</p>';
						echo '<p><strong>Technologies</strong>Orienté militaire</p>';
						echo '<p><strong>Bâtiments</strong>Chantier Alpha et Chantier de Ligne au niveau maximum</p>'; // et Colonne d'Assemblage
						echo '<hr />';
						echo '<p><strong>Nécessite</strong>Générateur niveau ' . OBM_LEVEL_MIN_TO_CHANGE_TYPE . '</p>';
					echo '</div>';

					echo '<div class="desc-choice">';
						$capitalQuantity = 0;
						for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
							if (CTR::$data->get('playerBase')->get('ob')->get($i)->get('type') == OrbitalBase::TYP_CAPITAL) {
								$capitalQuantity++;
							}
						}
						$totalPrice = ($capitalQuantity + 1) * PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'price');
						if ((($ob_obSituation->typeOfBase == OrbitalBase::TYP_COMMERCIAL || $ob_obSituation->typeOfBase == OrbitalBase::TYP_MILITARY) && CTR::$data->get('playerInfo')->get('credit') >= $totalPrice && $ob_obSituation->levelGenerator >= OBM_LEVEL_MIN_FOR_CAPITAL)) {
							echo '<a href="' . APP_ROOT . 'action/a-changebasetype/baseid-' . $ob_obSituation->getId() . '/type-' . OrbitalBase::TYP_CAPITAL . '" class="button">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'name') . '<br />';
								echo  Format::numberFormat($totalPrice);
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</a>';
						} elseif ($ob_obSituation->typeOfBase == OrbitalBase::TYP_CAPITAL) {
							# do nothing
						} else {
							echo '<span class="button disable">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'name') . '<br />';
								echo  Format::numberFormat($totalPrice);
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</span>';
						}
						echo '<h4>Avantages &amp; Inconvénients</h4>';
						$fleetQuantity = PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'l-line') + PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'r-line');
						echo '<p><strong class="short">Flottes</strong>' . $fleetQuantity . '</p>';
						echo '<p><strong class="short">Impôt</strong>' . (PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'tax') * 100) . '%</p>';
						echo '<p><strong>Technologies</strong>Toutes disponibles</p>';
						echo '<p><strong>Bâtiments</strong>Tous au niveau maximum</p>';
						echo '<hr />';
						echo '<p><strong>Nécessite</strong>Générateur niveau ' . OBM_LEVEL_MIN_FOR_CAPITAL . '</p>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component space size3">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="situation-content place1">';
				echo '<div class="toolbar">';
					echo '<a href="' . APP_ROOT . '/map/base-' . $ob_obSituation->getId() . '">Centrer sur la carte</a>';
					echo '<form action="' . APP_ROOT . 'action/a-renamebase/baseid-' . $ob_obSituation->getId() . '" method="POST">';
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
						echo '<a class="link hb ' . ($commander->line == 1 ? 'to-right' : 'to-left') . '" title="changer de ligne" href="' . APP_ROOT . 'action/a-changeline/id-' . $commander->id . '"></a>';
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
					echo '<div class="info bottom">';
						echo '<strong>' . Format::numberFormat($ob_obSituation->getPlanetPopulation() * 1000000) . '</strong> habitants<br />';
						echo $ob_obSituation->getPlanetResources() . ' % coeff. ressource<br />';
						echo $ob_obSituation->getPlanetHistory() . ' % coeff. historique';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			
		echo '</div>';
	echo '</div>';
echo '</div>';
?> 