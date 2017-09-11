<?php

use Asylamba\Classes\Container\Params;

$request = $this->getContainer()->get('app.request');
$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');

$rate = 400 / $galaxyConfiguration->galaxy['size'];
echo '<div id="map-content" ' . ($request->cookies->get('p' . Params::SHOW_MAP_MINIMAP, Params::$params[Params::SHOW_MAP_MINIMAP]) ? null : 'style="display:none;"') . '>';
    echo '<div class="mini-map">';
        echo '<svg class="sectors" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">';
            foreach ($sectors as $sector) {
                echo '<polygon ';
                echo 'class="ally' . $sector->getRColor() . ' moveTo" ';
                echo 'points="' . $galaxyConfiguration->getSectorCoord($sector->getId(), $rate, 0) . '" ';
                echo 'data-x-position="' . $sector->getXBarycentric() . '" data-y-position="' . $sector->getYBarycentric() . '" ';
                echo '/>';
            }
        echo '</svg>';
        echo '<div class="number">';
            foreach ($sectors as $sector) {
                $i = $sector->getId() - 1;
                echo '<span style="top: ' . ($galaxyConfiguration->sectors[$i]['display'][1] * $rate / 1.35) . 'px; left: ' . ($galaxyConfiguration->sectors[$i]['display'][0] * $rate / 1.35) . 'px;">';
                echo $sector->getId();
                echo '</span>';
            }
        echo '</div>';
        echo '<svg class="bases" viewBox="0, 0, 400, 400" xmlns="http://www.w3.org/2000/svg">';
            foreach ($playerBases as $base) {
                echo '<circle cx="' . ($base->getXSystem() * $rate) . '" cy="' . ($base->getYSystem() * $rate) . '" r="4" />';
            }
        echo '</svg>';
        echo '<div class="viewport"></div>';
    echo '</div>';
echo '</div>';
