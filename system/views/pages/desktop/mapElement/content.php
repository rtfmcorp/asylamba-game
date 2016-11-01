<?php

use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Worker\ASM;

$rate = 400 / GalaxyConfiguration::$galaxy['size'];
echo '<div id="map-content" ' . (Params::check(Params::SHOW_MAP_MINIMAP) ? NULL : 'style="display:none;"') . '>';
	echo '<div class="mini-map">';
		echo '<svg class="sectors" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">';
			for ($i = 0; $i < $sm->size(); $i++) {
				echo '<polygon ';
					echo 'class="ally' . $sm->get($i)->getRColor() . ' moveTo" ';
					echo 'points="' . GalaxyConfiguration::getSectorCoord($sm->get($i)->getId(), $rate, 0) . '" ';
					echo 'data-x-position="' . $sm->get($i)->getXBarycentric() . '" data-y-position="' . $sm->get($i)->getYBarycentric() . '" ';
				echo '/>';
			}
		echo '</svg>';
		echo '<div class="number">';
			for ($i = 0; $i < $sm->size(); $i++) {
				echo '<span style="top: ' . (GalaxyConfiguration::$sectors[$i]['display'][1] * $rate / 1.35) . 'px; left: ' . (GalaxyConfiguration::$sectors[$i]['display'][0] * $rate / 1.35) . 'px;">';
					echo $sm->get($i)->getId();
				echo '</span>';
			}
		echo '</div>';
		echo '<svg class="bases" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">';
			for ($i = 0; $i < ASM::$obm->size(); $i++) {
				$base = ASM::$obm->get($i);
				echo '<circle cx="' . ($base->getXSystem() * $rate) . '" cy="' . ($base->getYSystem() * $rate) . '" r="4" />';
			}
		echo '</svg>';
		echo '<div class="viewport"></div>';
	echo '</div>';
echo '</div>';
