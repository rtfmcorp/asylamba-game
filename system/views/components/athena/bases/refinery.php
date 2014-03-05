<?php
# refinery component
# in athena.bases package

# affichage de la raffinerie

# require
	# {orbitalBase}		ob_refinery

echo '<div class="component building">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/refinery.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(1, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_refinery->getLevelRefinery() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				if ($ob_refinery->getIsProductionRefinery() == 1) {
					echo '<span><a href="' . APP_ROOT . '/action/a-switchrefinerymode/baseid-' . $ob_refinery->getId() . '">passer en mode stockage</a></span>';
				} else {
					echo '<span><a href="' . APP_ROOT . '/action/a-switchrefinerymode/baseid-' . $ob_refinery->getId() . '">passer en mode production</a></span>';
				}
				echo '<span><a href="#" class="hb lt sh" data-target="info-refinery" title="information">?</a></span>';
			echo '</div>';

			echo '<p class="info" id="info-refinery" style="display:none;">La raffinerie possède deux modes : production ou stockage. Le mode de production fera monter plus vite votre stock de ressources
			alors que le mode stockage vous permettra de vous absenter de plus longues périodes.</p>';

			echo '<div class="number-box grey">';
				echo '<span class="label">mode de la raffinerie</span>';
				echo '<span class="value">';
					echo ($ob_refinery->getIsProductionRefinery() == 1) ? 'Production' : 'Stockage';
				echo '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">production par relève</span>';
				echo '<span class="value">';
					$production = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(1, 'level', $ob_refinery->getLevelRefinery(), 'refiningCoefficient'), $ob_refinery->getPlanetResources());
					echo Format::numberFormat($production);
					$refiningBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_REFINING);
					if ($ob_refinery->getIsProductionRefinery() == 1 && $refiningBonus > 0) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * OBM_COEFPRODUCTION) + ($production * $refiningBonus / 100)) . '</span>';
					} elseif ($ob_refinery->getIsProductionRefinery() == 1) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * OBM_COEFPRODUCTION)) . '</span>';
					} elseif ($refiningBonus > 0) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * $refiningBonus / 100)) . '</span>';
					}
					echo ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">ressources en stock</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_refinery->getResourcesStorage());
					echo ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color">';
				echo '</span>';
				$storageSpace = OrbitalBaseResource::getBuildingInfo(1, 'level', $ob_refinery->getLevelRefinery(), 'storageSpace');
				$storageBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
				if ($ob_refinery->getIsProductionRefinery() == 0 && $storageBonus > 0) {
					$storageSpace += ($storageSpace * OBM_COEFPRODUCTION) + ($storageSpace * $storageBonus / 100);
				} elseif ($ob_refinery->getIsProductionRefinery() == 0) {
					$storageSpace += ($storageSpace * OBM_COEFPRODUCTION);
				} elseif ($storageBonus > 0) {
					$storageSpace += ($storageSpace * $storageBonus / 100);
				}
				$percent = Format::numberFormat($ob_refinery->getResourcesStorage() / $storageSpace * 100);
				echo '<span class="progress-bar hb bl" title="remplissage : ' . $percent . '%">';
					echo '<span style="width:' . $percent . '%;" class="content"></span>';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box grey">';
				echo '<span class="label">coefficient ressource de la planète</span>';
				echo '<span class="value">' .  $ob_refinery->getPlanetResources() . ' %</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box">';
				if ($ob_refinery->getIsProductionRefinery() == 1) {
					echo '<span class="label">bonus du mode production</span>';
					echo '<span class="value">' .  OBM_COEFPRODUCTION * 100 . ' %</span>';
				} else {
					echo '<span class="label">bonus du mode stockage</span>';
					echo '<span class="value">' .  OBM_COEFPRODUCTION * 100 . ' %</span>';
				}
			echo '</div>';

			echo '<div class="number-box ' . ($refiningBonus == 0 ? 'grey' : '') . '">';
				echo '<span class="label">bonus technologique de production</span>';
				echo '<span class="value">' .  $refiningBonus . ' %</span>';
			echo '</div>';

			echo '<div class="number-box ' . ($storageBonus == 0 ? 'grey' : '') . '">';
				echo '<span class="label">bonus technologique de stockage</span>';
				echo '<span class="value">' .  $storageBonus . ' %</span>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Contrôle du raffinage</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Production par relèves de la raffinerie</h3>';
			echo '<ul class="list-type-1">';
				$level = $ob_refinery->getLevelRefinery();
				$from  = ($level < 3)  ? 1  : $level - 2;
				$to    = ($level > 15) ? 21 : $level + 5;
				for ($i = $from; $i < $to; $i++) {
					echo ($i == $level) ? '<li class="strong">' : '<li>';
						echo '<span class="label">niveau ' . $i . '</span>';
						echo '<span class="value">';
							$production = Game::resourceProduction(OrbitalBaseResource::getBuildingInfo(1, 'level', $i, 'refiningCoefficient'), $ob_refinery->getPlanetResources());
							echo Format::numberFormat($production);
							$refiningBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_REFINING);
							if ($ob_refinery->getIsProductionRefinery() == 1 && $refiningBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat(($production * OBM_COEFPRODUCTION) + ($production * $refiningBonus / 100)) . '</span>';
							} elseif ($ob_refinery->getIsProductionRefinery() == 1) {
								echo '<span class="bonus">+' . Format::numberFormat(($production * OBM_COEFPRODUCTION)) . '</span>';
							} elseif ($refiningBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat(($production * $refiningBonus / 100)) . '</span>';
							}
							echo '<img class="icon-color" src="' . MEDIA . 'resources/resource.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
				}
				echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Gestion des stocks</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Stockage maximal de la raffinerie</h3>';
			echo '<ul class="list-type-1">';
				$level = $ob_refinery->getLevelRefinery();
				$from  = ($level < 3)  ? 1  : $level - 2;
				$to    = ($level > 15) ? 21 : $level + 5;
				for ($i = $from; $i < $to; $i++) {
					echo ($i == $level) ? '<li class="strong">' : '<li>';
						echo '<span class="label">niveau ' . $i . '</span>';
						echo '<span class="value">';
							echo Format::numberFormat(OrbitalBaseResource::getBuildingInfo(1, 'level', $i, 'storageSpace'));
							$storageSpace = OrbitalBaseResource::getBuildingInfo(1, 'level', $i, 'storageSpace');
							$storageBonus = CTR::$data->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
							if ($ob_refinery->getIsProductionRefinery() == 0 && $storageBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat(($storageSpace * OBM_COEFPRODUCTION) + ($storageSpace * $storageBonus / 100)) . '</span>';
							} elseif ($ob_refinery->getIsProductionRefinery() == 0) {
								echo '<span class="bonus">+' . Format::numberFormat(($storageSpace * OBM_COEFPRODUCTION)) . '</span>';
							} elseif ($storageBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat(($storageSpace * $storageBonus / 100)) . '</span>';
							}
							echo '<img class="icon-color" src="' . MEDIA . 'resources/resource.png" alt="crédits" />';
						echo '</span>';
					echo '</li>';
				}
				echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(1, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>