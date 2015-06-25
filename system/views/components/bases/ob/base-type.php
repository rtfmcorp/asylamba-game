<?php
# affichage du type de base

# require
	# {orbitalBase}		ob_obSituation
	# [{commander}]		commanders_obSituation

echo '<div class="component generator">';
	echo '<div class="head skin-2"></div>';
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
						if ($ob_obSituation->typeOfBase == OrbitalBase::TYP_NEUTRAL && CTR::$data->get('playerInfo')->get('credit') >= PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price') && $ob_obSituation->levelGenerator >= OBM_LEVEL_MIN_TO_CHANGE_TYPE) {
							echo '<a href="' . Format::actionBuilder('changebasetype', ['baseid' => $ob_obSituation->getId(), 'type' => OrbitalBase::TYP_COMMERCIAL]) . '" class="button">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'name') . '<br />';
								echo  Format::numberFormat(PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price'));
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</a>';
						} elseif (($ob_obSituation->typeOfBase == OrbitalBase::TYP_MILITARY) && CTR::$data->get('playerInfo')->get('credit') >= PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price')) {
							echo '<a href="' . Format::actionBuilder('changebasetype', ['baseid' => $ob_obSituation->getId(), 'type' => OrbitalBase::TYP_COMMERCIAL]) . '" class="button confirm" data-confirm-label="Transformer cette base supprimera toute la file de construction">';
								echo '<span class="text">Transformer en ' . PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'name') . '<br />';
								echo  Format::numberFormat(PlaceResource::get(OrbitalBase::TYP_COMMERCIAL, 'price'));
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</a>';
						} elseif ($ob_obSituation->typeOfBase == OrbitalBase::TYP_COMMERCIAL || $ob_obSituation->typeOfBase == OrbitalBase::TYP_CAPITAL) {
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
						echo '<p><strong>Bâtiments</strong>Plateforme Commerciale et Spatioport au niveau maximum</p>';
						echo '<hr />';
						echo '<p><strong>Nécessite</strong>Générateur niveau ' . OBM_LEVEL_MIN_TO_CHANGE_TYPE . '</p>';
					echo '</div>';

					echo '<div class="desc-choice">';
						if ($ob_obSituation->typeOfBase == OrbitalBase::TYP_NEUTRAL && CTR::$data->get('playerInfo')->get('credit') >= PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price') && $ob_obSituation->levelGenerator >= OBM_LEVEL_MIN_TO_CHANGE_TYPE) {
							echo '<a href="' . Format::actionBuilder('changebasetype', ['baseid' => $ob_obSituation->getId(), 'type' => OrbitalBase::TYP_MILITARY]) . '" class="button">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_MILITARY, 'name') . '<br />';
								echo  Format::numberFormat(PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price'));
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</a>';
						} elseif (($ob_obSituation->typeOfBase == OrbitalBase::TYP_COMMERCIAL) && CTR::$data->get('playerInfo')->get('credit') >= PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price')) {
							echo '<a href="' . Format::actionBuilder('changebasetype', ['baseid' => $ob_obSituation->getId(), 'type' => OrbitalBase::TYP_MILITARY]) . '" class="button confirm" data-confirm-label="Transformer cette base supprimera toute la file de construction">';
								echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_MILITARY, 'name') . '<br />';
								echo  Format::numberFormat(PlaceResource::get(OrbitalBase::TYP_MILITARY, 'price'));
								echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
							echo '</a>';
						} elseif ($ob_obSituation->typeOfBase == OrbitalBase::TYP_MILITARY || $ob_obSituation->typeOfBase == OrbitalBase::TYP_CAPITAL) {
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
						echo '<p><strong>Bâtiments</strong>Centre de Recyclage et Chantier de Ligne au niveau maximum</p>';
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
						$totalPrice = PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'price');
						if ((($ob_obSituation->typeOfBase == OrbitalBase::TYP_COMMERCIAL || $ob_obSituation->typeOfBase == OrbitalBase::TYP_MILITARY) && CTR::$data->get('playerInfo')->get('credit') >= $totalPrice && $ob_obSituation->levelGenerator >= OBM_LEVEL_MIN_FOR_CAPITAL)) {
							if ($capitalQuantity == 0) {
								echo '<a href="' . Format::actionBuilder('changebasetype', ['baseid' => $ob_obSituation->getId(), 'type' => OrbitalBase::TYP_CAPITAL]) . '" class="button">';
									echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'name') . '<br />';
									echo  Format::numberFormat($totalPrice);
									echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
								echo '</a>';
							} else {
								echo '<span class="button disable">';
									echo '<span class="text">Vous avez déjà une ' . PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'name') . '</span><br />';
								echo '</span>';
							}
						} elseif ($ob_obSituation->typeOfBase == OrbitalBase::TYP_CAPITAL) {
							# do nothing
						} else {
							if ($capitalQuantity == 0) {
								echo '<span class="button disable">';
									echo '<span class="text">Evoluer en ' . PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'name') . '<br />';
									echo  Format::numberFormat($totalPrice);
									echo ' <img class="icon-color" alt="crédits" src="' . MEDIA . 'resources/credit.png"></span>';
								echo '</span>';
							} else {
								echo '<span class="button disable">';
									echo '<span class="text">Vous avez déjà une ' . PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'name') . '</span><br />';
								echo '</span>';
							}
						}
						echo '<h4>Avantages &amp; Inconvénients</h4>';
						$fleetQuantity = PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'l-line') + PlaceResource::get(OrbitalBase::TYP_CAPITAL, 'r-line');
						echo '<p>Vous ne pouvez avoir qu\'une capitale</p>';
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