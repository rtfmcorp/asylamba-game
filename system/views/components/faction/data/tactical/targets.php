<?php
$mode = isset($targetMode) ? $targetMode : FALSE;

$sm = new SectorManager();
$sm->load();

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Objectifs de victoires</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 1; $i <= VictoryResources::size(); $i++) { 
				$targets = VictoryResources::getInfo($i ,'targets');
				$isValid = 0;

				echo '<h4>' . VictoryResources::getInfo($i ,'title') . '</h4>';
				echo '<div class="set-item">';
				foreach ($targets as $key => $target) {
					$sectors = 0;

					for ($j = 0; $j < $sm->size(); $j++) {
						if ($sm->get($j)->rColor == $faction->id && in_array($sm->get($j)->id, $target['sectors'])) {
							$sectors++;
						}
					}

					echo '<div class="item">';
						echo '<div class="left">';
							echo '<span ' . ($sectors >= $target['nb'] ? 'class="round-color' . $faction->id . '"' : NULL) . '>' . $sectors . '/' . $target['nb'] . '</span>';
						echo '</div>';
						echo '<div class="center">' .  $target['label'] . '</div>';
					echo '</div>';

					if ($sectors >= $target['nb']) {
						$isValid++;
					}
				}
				echo '</div>';

				if ($mode) {
					if ($isValid == count($targets)) {
						echo '<a class="more-button" href="' . Format::actionBuilder('iwin') . '">Revendiquer la victoire</a>';
					} else {
						echo '<span class="more-button">Tous les objectifs ne sont pas remplis</span>';
					}
				} else {
					echo '<p>' . VictoryResources::getInfo($i ,'infos') . '</p>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>