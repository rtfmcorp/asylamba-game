<?php

use Asylamba\Classes\Library\Game;

$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$galaxyConfiguration = $this->getContainer()->get(\Asylamba\Modules\Gaia\Galaxy\GalaxyConfiguration::class);

echo '<div id="own-base">';
	echo '<svg viewBox="0, 0, ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . ', ' . ($galaxyConfiguration->scale * $galaxyConfiguration->galaxy['size']) . '" xmlns="http://www.w3.org/2000/svg">';
		foreach ($playerBases as $base) {
			echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="22" />';
			
			if ($base->getId() == $defaultBase->getId()) {
				echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="16">';
					echo '<animate attributeType="XML" from="16" to="60" begin="0s" dur="1200ms" repeatCount="indefinite" attributeName="r" />';
					echo '<animate attributeType="CSS" from="0.2" to="0" begin="0s" dur="1200ms" repeatCount="indefinite" attributeName="opacity" />';
				echo '</circle>';

				echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="' . ((Game::getMaxTravelDistance($session->get('playerBonus')) * $galaxyConfiguration->scale) + 10) . '" style="fill: none; stoke-width: 6px; stroke: white; stroke-dasharray: 50 10;" />';
			} else {
				echo '<circle cx="' . ($base->getXSystem() * $galaxyConfiguration->scale) . '" cy="' . ($base->getYSystem() * $galaxyConfiguration->scale) . '" r="28" />';
			}
		}
	echo '</svg>';
echo '</div>';
