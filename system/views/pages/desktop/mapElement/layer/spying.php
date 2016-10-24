<?php

use Asylamba\Classes\Container\Params;
use Asylamba\Classes\Worker\ASM;
use GalaxyConfiguration;
use Asylamba\Classes\Library\Game;

echo '<div id="spying" ' . (Params::check(Params::SHOW_MAP_ANTISPY) ? NULL : 'style="display:none;"') . '>';
	echo '<svg viewBox="0, 0, ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . ', ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
		for ($i = 0; $i < ASM::$obm->size(); $i++) {
			$base = ASM::$obm->get($i);

			$bigRadius = Game::getAntiSpyRadius($base->getAntiSpyAverage());

			echo '<circle cx="' . ($base->getXSystem() * GalaxyConfiguration::$scale) . '" cy="' . ($base->getYSystem() * GalaxyConfiguration::$scale) . '" r="' . ($bigRadius / 3) . '" />';
			echo '<circle cx="' . ($base->getXSystem() * GalaxyConfiguration::$scale) . '" cy="' . ($base->getYSystem() * GalaxyConfiguration::$scale) . '" r="' . ($bigRadius / 3 * 2) . '" />';
			echo '<circle cx="' . ($base->getXSystem() * GalaxyConfiguration::$scale) . '" cy="' . ($base->getYSystem() * GalaxyConfiguration::$scale) . '" r="' . ($bigRadius) . '" />';
		}
	echo '</svg>';
echo '</div>';
