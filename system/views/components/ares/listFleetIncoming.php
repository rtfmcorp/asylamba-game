<?php
# listFleetIncoming component
# in ares package

# affichage de la liste des flottes qui attaquent le joueurs

# require
	# [{commander}]		commander_listFleetIncoming

echo '<div class="component size2 list-fleet">';
	echo '<div class="head skin-2">';
		echo '<h2>Attaques entrantes</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($commander_listFleetIncoming as $commander) {
				$step = 0;
				for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
					$event = CTR::$data->get('playerEvent')->get($i);
					if ($event->get('eventType') == EVENT_INCOMING_ATTACK && $event->get('eventId') == $commander->getId()) {
						$info = $event->get('eventInfo');
						for ($j = 0; $j < count($info); $j++) { 
							if ($info[$j] === TRUE) { $step++; } else { break; }
						}
					}
				}

				echo '<div class="fleet-element progress color' . $commander->getPlayerColor() . '" data-progress-output="lite" data-progress-total-time="0" data-progress-current-time="' . (strtotime($commander->getArrivalDate()) - time()) . '">';
					echo '<img src="' . MEDIA . 'fleet/anchor.png" alt="à quai" class="status" />';

					echo '<span class="name">';
						if ($step >= 2) {
							echo '<strong>' . $commander->getName() . '</strong>';
							echo '<em>grade ' . $commander->getLevel() . '</em>';
						} else {
							echo '<strong>Grade</strong>';
							echo '<em>inconnu</em>';
						}
					echo '</span>';

					echo '<span class="pev">';
						if ($step >= 3) {
							echo $commander->getPev() . ' <img src="' . MEDIA . 'resources/pev.png" class="icon-color" alt="pev" />';
						} else {
							echo '??? <img src="' . MEDIA . 'resources/pev.png" class="icon-color" alt="pev" />';
						}
					echo '</span>';

					echo '<span class="location">';
						if ($step >= 2) {
							switch ($commander->getTypeOfMove()) {
								case COM_LOOT: $type = 'piller'; break;
								case COM_COLO: $type = 'conquérir'; break;
								default: $type = 'erreur'; break;
							}
							echo 'Se prépare à ' . $type . ' <a href="' . APP_ROOT . 'bases/base-' . $commander->getRPlaceDestination() . '">' . $commander->getDestinationPlaceName() . '</a><br />';
						} else {
							echo 'Aucune information sur la nature de l\'attaque<br />';
						}
						echo 'Base d\'attache &#8594; <a href="' . APP_ROOT . 'map/place-' . $commander->getRBase() . '">' . $commander->getOBName() . '</a>';				
					echo '</span>';

					echo '<span class="duration">';
						echo '<span class="progress-text"></span>';
					echo '</span>';
				echo '</div>';
			}
			if (count($commander_listFleetIncoming) == 0) {
				echo '<p class="info">Aucune attaque entrante détectée. <a href="' . APP_ROOT . 'financial">Modifiez les investissements de contre-espionnage pour être averti plus tôt.</a></p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';