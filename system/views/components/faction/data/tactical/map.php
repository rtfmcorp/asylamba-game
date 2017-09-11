<?php


$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');
$sectorManager = $this->getContainer()->get('gaia.sector_manager');

$sectors = $sectorManager->getAll();
$rate = 750 / $galaxyConfiguration->galaxy['size'];

echo '<div class="component size2">';
    echo '<div class="head skin-2">';
        echo '<h2>Carte tactique</h2>';
    echo '</div>';
    echo '<div class="fix-body">';
        echo '<div class="body">';
            echo '<div class="tactical-map">';
                echo '<svg class="sectors" viewBox="0, 0, 750, 750" xmlns="http://www.w3.org/2000/svg">';
                    foreach ($sectors as $sector) {
                        echo '<polygon data-id="' . $sector->getId() . '"';
                        echo 'class="ally' . $sector->getRColor() . ' ' . ($sector->getRColor() != 0 ? 'enabled' : 'disabled') . '" ';
                        echo 'points="' . $galaxyConfiguration->getSectorCoord($sector->getId(), $rate, 0) . '" ';
                        echo '/>';
                    }
                echo '</svg>';
                echo '<div class="number">';
                    foreach ($sectors as $sector) {
                        $i = $sector->getId() - 1;
                        echo '<span id="sector' . $sector->getId() . '" class="ally' . $sector->getRColor() . '" style="top: ' . ($galaxyConfiguration->sectors[$i]['display'][1] * $rate / 1.35) . 'px; left: ' . ($galaxyConfiguration->sectors[$i]['display'][0] * $rate / 1.35) . 'px;">';
                        echo $sector->getId();
                        echo '</span>';
                    }
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
echo '</div>';
