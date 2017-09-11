<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;

$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');

echo '<div id="systems">';
    $systems = $this->getContainer()->get('gaia.system_manager')->getAll();

    # own bases
    $basesId = array();
    foreach ($playerBases as $base) {
        $basesId[]  = $base->getSystem();
    }

    foreach ($systems as $system) {
        $owner = (in_array($system->getId(), $basesId)) ? 'class="own"' : '';
        echo '<a ';
        echo 'href="#" ';
        echo 'class="loadSystem ' . $systemId . ' ' . ($system->getId() == $systemId ? 'active' : null) . '" ';
        echo 'data-system-id="' . $system->getId() . '" ';
        echo 'data-x-position="' . $system->xPosition . '" data-y-position="' . $system->yPosition . '" ';
        echo 'style="top: ' . ($system->yPosition * $galaxyConfiguration->scale - 10) . 'px; left: ' . ($system->xPosition * $galaxyConfiguration->scale - 10) . 'px">';
        echo '<img src="' . MEDIA . 'map/systems/t' . $system->typeOfSystem . 'c' . $system->rColor . '.png" ' . $owner . ' />';
        echo '</a>';
    }

    foreach ($sectors as $sector) {
        echo '<span ';
        echo 'class="sector-number color' . $sector->getRColor() . ' sh" ';
        echo 'data-target="sector-info-' . $sector->getId() . '" ';
        echo 'style="left: ' . $sector->getXBarycentric() * $galaxyConfiguration->scale . 'px; top: ' . $sector->getYBarycentric() * $galaxyConfiguration->scale . 'px">';
        echo $sector->getId();
        echo '</span>';

        echo '<div id="sector-info-' . $sector->getId() . '" class="sector-info color' . $sector->getRColor() . '" style="left: ' . ($sector->getXBarycentric() * $galaxyConfiguration->scale + 55) . 'px; top: ' . ($sector->getYBarycentric() * $galaxyConfiguration->scale - 10) . 'px">';
        echo '<h2>' . $sector->getName() . '</h2>';
        echo '<p><a href="#">+</a> ';
        if ($sector->getRColor() != 0) {
            echo 'Revendiqué par ' . ColorResource::getInfo($sector->getRColor(), 'popularName') . ' | ' . $sector->getTax() . '% de taxe' . ' | rapporte ' . $sector->points . Format::addPlural($sector->points, ' points', ' point');
        } else {
            echo 'Non revendiqué | Aucune taxe' . ' | rapporte ' . $sector->points . Format::addPlural($sector->points, ' points', ' point') . '</p>';
        }
        echo '</p>';
        echo '</div>';
    }
    
echo '</div>';
