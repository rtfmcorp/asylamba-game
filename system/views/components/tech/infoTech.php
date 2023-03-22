<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;

$container = $this->getContainer();
$mediaPath = $container->getParameter('media');

echo '<div class="component panel-info">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>' . $name . '</h4>';
			echo '<a href="#" class="removeInfoPanel remove-info hb lt" title="fermer le panneau">x</a>';

			echo '<p class="info">' . $shortDescription . '</p>';

			echo '<div class="table"><table>';
				echo '<tr>';
					echo '<td class="hb lt" title="coût de recherche en ressource">coût en ressource</td>';
					echo '<td>' . Format::numberFormat($resource) . ' <img src="' .  $mediaPath. 'resources/resource.png" alt="ressource" class="icon-color" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="hb lt" title="coût de recherche en crédit">coût en crédit</td>';
					echo '<td>' . Format::numberFormat($credit) . ' <img src="' .  $mediaPath. 'resources/credit.png" alt="credit" class="icon-color" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="hb lt" title="temps de recherche (heures:minutes:secondes)">temps</td>';
					echo '<td>' . Chronos::secondToFormat($time, 'lite') . ' <img src="' .  $mediaPath. 'resources/time.png" alt="relève" class="icon-color" /></td>';
				echo '</tr>';
				echo '<tr>';
					echo '<td class="hb lt" title="points que rapporte la recherche de la technologie au joueur">points</td>';
					echo '<td>' . Format::numberFormat($points) . '</td>';
				echo '</tr>';
			echo '</table></div>';

			# description
			echo '<h4>Prérequis pour le prochain niveau</h4>';
			echo '<p class="info">';
				echo 'Technosphère, niv. ' . $technosphere . '<br />';
				foreach ($researchList as $research) {
					echo $research[0] . ', niv. ' . $research[1];
					echo $research[2] ? '<img class="requierement" src="' . $mediaPath . 'resources/check.png" alt="" />' : NULL;
					echo '<br />';
				}
			echo '</p>';

			# description
			echo '<h4>Description</h4>';
			echo '<p class="info">' . $description . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
