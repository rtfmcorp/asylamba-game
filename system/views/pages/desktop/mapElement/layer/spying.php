<?php

use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Library\Game;

$galaxyConfiguration = $this->getContainer()->get('gaia.galaxy_configuration');

echo '<div id="spying" ' . ($request->cookies->get('p' . Params::SHOW_MAP_ANTISPY, Params::$params[Params::SHOW_MAP_ANTISPY]) ? null : 'style="display:none;"') . '>';
    echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
        foreach ($playerBases as $base) {
            $bigRadius = Game::getAntiSpyRadius($base->getAntiSpyAverage());

            echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="' . ($bigRadius / 3) . '" />';
            echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="' . ($bigRadius / 3 * 2) . '" />';
            echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="' . ($bigRadius) . '" />';
        }
    echo '</svg>';
echo '</div>';
