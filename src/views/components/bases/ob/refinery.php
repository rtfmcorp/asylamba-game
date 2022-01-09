<?php
# refinery component
# in athena.bases package

# affichage de la raffinerie

# require
	# {orbitalBase}		ob_refinery

use App\Modules\Athena\Resource\OrbitalBaseResource;
use App\Classes\Library\Game;
use App\Classes\Library\Format;
use App\Modules\Zeus\Model\PlayerBonus;

$container = $this->getContainer();
$orbitalBaseHelper = $this->getContainer()->get(\App\Modules\Athena\Helper\OrbitalBaseHelper::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$mediaPath = $container->getParameter('media');

echo '<div class="component building">';
	echo '<div class="head skin-1">';
		echo '<img src="' . $mediaPath . 'orbitalbase/refinery.png" alt="" />';
		echo '<h2>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'frenchName') . '</h2>';
		echo '<em>Niveau ' . $ob_refinery->getLevelRefinery() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';

			echo '<div class="number-box">';
				echo '<span class="label">production par relève</span>';
				echo '<span class="value">';
					$production = Game::resourceProduction($orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $ob_refinery->getLevelRefinery(), 'refiningCoefficient'), $ob_refinery->getPlanetResources());
					echo Format::numberFormat($production);
					$refiningBonus = $session->get('playerBonus')->get(PlayerBonus::REFINERY_REFINING);

					if ($refiningBonus > 0) {
						echo '<span class="bonus">+' . Format::numberFormat(($production * $refiningBonus / 100)) . '</span>';
					}
					echo ' <img alt="ressources" src="' . $mediaPath . 'resources/resource.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box grey">';
				echo '<span class="label">coefficient ressource de la planète</span>';
				echo '<span class="value">' .  $ob_refinery->getPlanetResources() . ' %</span>';
			echo '</div>';

			echo '<div class="number-box ' . ($refiningBonus == 0 ? 'grey' : '') . '">';
				echo '<span class="label">bonus technologique de production</span>';
				echo '<span class="value">' .  $refiningBonus . ' %</span>';
			echo '</div>';

		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Contrôle du raffinage</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Production par relève</h4>';
			echo '<ul class="list-type-1">';
				$level = $ob_refinery->getLevelRefinery();
				$from  = ($level < 3)  ? 1  : $level - 2;
				$to    = ($level > 35) ? 41 : $level + 5;

				for ($i = $from; $i < $to; $i++) {
					echo ($i == $level) ? '<li class="strong">' : '<li>';
						echo '<span class="label">niveau ' . $i . '</span>';
						echo '<span class="value">';
							$production = Game::resourceProduction($orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'level', $i, 'refiningCoefficient'), $ob_refinery->getPlanetResources());
							echo Format::numberFormat($production);
							$refiningBonus = $session->get('playerBonus')->get(PlayerBonus::REFINERY_REFINING);
							
							if ($refiningBonus > 0) {
								echo '<span class="bonus">+' . Format::numberFormat(($production * $refiningBonus / 100)) . '</span>';
							}
							echo '<img class="icon-color" src="' . $mediaPath . 'resources/resource.png" alt="crédits" />';
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
			echo '<p class="long-info">' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::REFINERY, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
