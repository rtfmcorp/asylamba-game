<?php

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Worker\ASM;
use GalaxyConfiguration;
use Asylamba\Classes\Library\Game;

echo '<div id="own-base">';
	echo '<svg viewBox="0, 0, ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . ', ' . (GalaxyConfiguration::$scale * GalaxyConfiguration::$galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
		for ($i = 0; $i < ASM::$obm->size(); $i++) {
			$base = ASM::$obm->get($i);
			echo '<circle cx="' . ($base->getXSystem() * GalaxyConfiguration::$scale) . '" cy="' . ($base->getYSystem() * GalaxyConfiguration::$scale) . '" r="22" />';
			
			if ($base->getId() == $defaultBase->getId()) {
				echo '<circle cx="' . ($base->getXSystem() * GalaxyConfiguration::$scale) . '" cy="' . ($base->getYSystem() * GalaxyConfiguration::$scale) . '" r="16">';
					echo '<animate attributeType="XML" from="16" to="60" begin="0s" dur="1200ms" repeatCount="indefinite" attributeName="r" />';
					echo '<animate attributeType="CSS" from="0.2" to="0" begin="0s" dur="1200ms" repeatCount="indefinite" attributeName="opacity" />';
				echo '</circle>';

				echo '<circle cx="' . ($base->getXSystem() * GalaxyConfiguration::$scale) . '" cy="' . ($base->getYSystem() * GalaxyConfiguration::$scale) . '" r="' . ((Game::getMaxTravelDistance(CTR::$data->get('playerBonus')) * GalaxyConfiguration::$scale) + 10) . '" style="fill: none; stoke-width: 6px; stroke: white; stroke-dasharray: 50 10;" />';
			} else {
				echo '<circle cx="' . ($base->getXSystem() * GalaxyConfiguration::$scale) . '" cy="' . ($base->getYSystem() * GalaxyConfiguration::$scale) . '" r="28" />';
			}
		}
	echo '</svg>';
echo '</div>';
