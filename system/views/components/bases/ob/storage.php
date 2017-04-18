<?php
# storage component
# in athena.bases package

# affichage du stockage

# require
	# {orbitalBase}		ob_storage

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Zeus\Model\PlayerBonus;

$orbitalBaseHelper = $this->getContainer()->get('athena.orbital_base_helper');
$session = $this->getContainer()->get('app.session');

echo '<div class="component building">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/storage.png" alt="" />';
		echo '<h2>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'frenchName') . '</h2>';
		echo '<em>Niveau ' . $ob_storage->getLevelStorage() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';

			echo '<div class="number-box">';
				echo '<span class="label">ressources en stock</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_storage->getResourcesStorage());
					echo ' <img alt="ressources" src="' . MEDIA . 'resources/resource.png" class="icon-color">';
				echo '</span>';
				$storageSpace = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $ob_storage->getLevelStorage(), 'storageSpace');
				$storageBonus = $session->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
				if ($storageBonus > 0) {
					$storageSpace += ($storageSpace * $storageBonus / 100);
				}
				$percent = Format::numberFormat($ob_storage->getResourcesStorage() / $storageSpace * 100);
				echo '<span class="progress-bar hb bl" title="remplissage : ' . $percent . '%">';
					echo '<span style="width:' . $percent . '%;" class="content"></span>';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box ' . ($storageBonus == 0 ? 'grey' : '') . '">';
				echo '<span class="label">bonus technologique de stockage</span>';
				echo '<span class="value">' .  $storageBonus . ' %</span>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Gestion des stocks</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Stockage maximal de la raffinerie</h4>';
			echo '<ul class="list-type-1">';
				$level = $ob_storage->getLevelStorage();
				$from  = ($level < 3)  ? 1  : $level - 2;
				$to    = ($level > 35) ? 41 : $level + 5;
				for ($i = $from; $i < $to; $i++) {
					echo ($i == $level) ? '<li class="strong">' : '<li>';
						echo '<span class="label">niveau ' . $i . '</span>';
						echo '<span class="value">';
							echo Format::numberFormat($orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $i, 'storageSpace'));
							$storageSpace = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'level', $i, 'storageSpace');
							$storageBonus = $session->get('playerBonus')->get(PlayerBonus::REFINERY_STORAGE);
							if ($storageBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat(($storageSpace * $storageBonus / 100)) . '</span>';
							}
							echo '<img class="icon-color" src="' . MEDIA . 'resources/resource.png" alt="ressources" />';
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
			echo '<p class="long-info">' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::STORAGE, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
