<?php
echo '<div id="spying">';
	echo '<svg viewBox="0, 0, 5000, 5000" xmlns="http://www.w3.org/2000/svg">';
		for ($i = 0; $i < ASM::$obm->size(); $i++) {
			$base = ASM::$obm->get($i);

			$bigRadius = Game::getAntiSpyRadius($base->getAntiSpyAverage());

			echo '<circle cx="' . ($base->getXSystem() * 20) . '" cy="' . ($base->getYSystem() * 20) . '" r="' . ($bigRadius / 3) . '" />';
			echo '<circle cx="' . ($base->getXSystem() * 20) . '" cy="' . ($base->getYSystem() * 20) . '" r="' . ($bigRadius / 3 * 2) . '" />';
			echo '<circle cx="' . ($base->getXSystem() * 20) . '" cy="' . ($base->getYSystem() * 20) . '" r="' . ($bigRadius) . '" />';
		}
	echo '</svg>';
echo '</div>';
?>