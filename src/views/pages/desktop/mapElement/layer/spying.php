<?php

use App\Classes\Container\Params;
use App\Classes\Library\Game;

$galaxyConfiguration = $this->getContainer()->get(\App\Modules\Gaia\Galaxy\GalaxyConfiguration::class);

echo '<div id="spying" ' . ($request->cookies->get('p' . Params::SHOW_MAP_ANTISPY, Params::$params[Params::SHOW_MAP_ANTISPY]) ? NULL : 'style="display:none;"') . '>';
	echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
		foreach ($playerBases as $base) {
			$bigRadius = Game::getAntiSpyRadius($base->getAntiSpyAverage());

			echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="' . ($bigRadius / 3) . '" />';
			echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="' . ($bigRadius / 3 * 2) . '" />';
			echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="' . ($bigRadius) . '" />';
		}
	echo '</svg>';
echo '</div>';
