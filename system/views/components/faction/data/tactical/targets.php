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
			if ($faction->isWinner == Color::WIN_TARGET) {
				$restTime = (strtotime($faction->dClaimVictory) + (HOURS_TO_WIN * 3600)) - strtotime(Utils::now());

				echo '<h4>Vos objectifs sont remplis</h4>';

				if ($restTime > 0) {
					echo '<p>Vos objectifs ont été validés, vous devez maintenant tenir vos positions pendant encore :</p>';

					echo '<div class="number-box">';
						echo '<span class="value">' . Chronos::secondToFormat($restTime, 'lite') . '</span>';
					echo '</div>';
				} else {
					if ($mode) {
						echo '<a class="more-button" href="' . Format::actionBuilder('ireallywin') . '">Revendiquer la victoire définitive</a>';
					} else {
						echo '<p>Le chef de votre faction peut revendiquer la victoire définitive.</p>';
					}
				}
			} elseif ($faction->isWinner == Color::WIN_CONFIRM) {
				echo '<h4>Vous avez gagné</h4>';
			}

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
					if ($isValid == count($targets) AND Utils::now() > '2015-05-11 08:00:00') {
						echo '<a class="more-button" href="' . Format::actionBuilder('iwin') . '">Revendiquer la victoire</a>';
					} else {
						echo '<span class="more-button">Tous les objectifs ne sont pas remplis</span>';
					}
				} else {
					echo '<p>' . VictoryResources::getInfo($i ,'infos') . '</p>';
				}
			}
			echo '<p>Les revendications territoriales doivent être tenues pendant ' . HOURS_TO_WIN . ' relèves pour que la victoire soit validée.</p>';
			echo '<p>La victoire ne peut être revendiquée qu\'à partir du 11 mai 2015 à 10h00 (UTC+1, heure d\'été).</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';