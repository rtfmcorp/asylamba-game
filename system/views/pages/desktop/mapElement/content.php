<?php
$gc = new GalaxyManager();
$scale = 400 / 250;

echo '<div id="map-content">';
	echo '<div id="mini-map">';
		echo '<div class="mini-map">';
			echo '<svg class="sectors" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">';
				for ($i = 0; $i < $sm->size(); $i++) {
					echo '<polygon ';
						echo 'class="ally' . $sm->get($i)->getRColor() . ' moveTo" ';
						echo 'points="' . $gc->getCoordPolygon($i, $scale, 0) . '" ';
						echo 'data-x-position="' . $sm->get($i)->getXBarycentric() . '" data-y-position="' . $sm->get($i)->getYBarycentric() . '" ';
					echo '/>';
				}
			echo '</svg>';
			echo '<div class="number">';
				for ($i = 0; $i < $sm->size(); $i++) {
					$link = $gc->getPlaceOfLink($i);
					echo '<span style="top: ' . (($link[0] * 300 / 1000) - 3) . 'px; left: ' . ($link[1] * 300 / 1000) . 'px;">';
						echo $i + 1;
					echo '</span>';
				}
			echo '</div>';
			echo '<svg class="bases" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">';
				for ($i = 0; $i < ASM::$obm->size(); $i++) {
					$base = ASM::$obm->get($i);
					echo '<circle cx="' . ($base->getXSystem() * $scale) . '" cy="' . ($base->getYSystem() * $scale) . '" r="4" />';
				}
			echo '</svg>';
			echo '<div class="viewport"></div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>