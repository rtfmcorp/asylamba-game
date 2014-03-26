<?php
# listFleet component
# in ares package

# affichage de la liste des flottes du joueur

# require
	# [{commander}]		commander_listFleet

echo '<div class="component size2 list-fleet">';
	echo '<div class="head skin-2">';
		echo '<h2>Flottes en activité</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < count($commander_listFleet); $i++) {
				$commander = $commander_listFleet[$i];
				if (in_array($commander->getStatement(), array(COM_AFFECTED, COM_MOVING))) {		
					$active = (CTR::$get->exist('commander') AND CTR::$get->get('commander') == $commander->getId()) ? 'active' : '';
					echo '<div class="fleet-element ' . $active . ' progress" data-progress-output="lite" data-progress-total-time="0" data-progress-current-time="' . (strtotime($commander->getArrivalDate()) - time()) . '">';

						if ($commander->getStatement() == COM_AFFECTED) {
							echo '<img src="' . MEDIA . 'fleet/anchor.png" alt="à quai" class="status" />';
						} elseif ($commander->getStatement() == COM_MOVING) {
							switch ($commander->getTypeOfMove()) {
								case COM_BACK: echo '<img src="' . MEDIA . 'fleet/back.png" alt="retour" class="status" />'; break;
								default: 	   echo '<img src="' . MEDIA . 'fleet/loot.png" alt="attaque" class="status" />'; break;
							}
						}

						echo '<span class="name">';
							echo '<strong>' . $commander->getName() . '</strong>';
							echo '<em>grade ' . $commander->getLevel() . '</em>';
						echo '</span>';

						echo '<span class="pev">';
							echo $commander->getPev() . ' <img src="' . MEDIA . 'resources/pev.png" class="icon-color" alt="pev" />';
						echo '</span>';

						echo '<span class="location">';
						if ($commander->getStatement() == COM_AFFECTED) {
							echo 'A quai<br />';
							echo 'Base d\'attache &#8594; <a href="' . APP_ROOT . 'bases/base-' . $commander->getRBase() . '">' . $commander->getOBName() . '</a>';
						} elseif ($commander->getStatement() == COM_MOVING) {
							switch ($commander->getTypeOfMove()) {
								case COM_MOVE: 
									echo 'Se déplace vers ' . $commander->getDestinationPlaceName() . '<br />';
									echo 'Base d\'attache &#8594; <a href="' . APP_ROOT . 'bases/base-' . $commander->getRBase() . '">' . $commander->getOBName() . '</a>'; break;
								case COM_LOOT: 
									echo 'En route pour piller <a href="' . APP_ROOT . 'map/place-' . $commander->getRPlaceDestination() . '">' . $commander->getDestinationPlaceName() . '</a><br />';
									echo 'Base d\'attache &#8594; <a href="' . APP_ROOT . 'bases/base-' . $commander->getRBase() . '">' . $commander->getOBName() . '</a>'; break;
								case COM_COLO: 
									echo 'En route pour coloniser <a href="' . APP_ROOT . 'map/place-' . $commander->getRPlaceDestination() . '">' . $commander->getDestinationPlaceName() . '</a><br />';
									echo 'Base d\'attache &#8594; <a href="' . APP_ROOT . 'bases/base-' . $commander->getRBase() . '">' . $commander->getOBName() . '</a>'; break;
								case COM_BACK: 
									echo 'Revient vers <a href="' . APP_ROOT . 'bases/base-' . $commander->getRBase() . '">' . $commander->getOBName() . '</a> après une attaque<br />';
									echo 'Transporte ' . Format::numberFormat($commander->getResourcesTransported()) . ' ressources'; break;
								default: break;
							}
						}
						echo '</span>';

						if ($commander->getStatement() == COM_MOVING) {
							echo '<span class="duration">';
								echo '<span class="progress-text"></span>';
							echo '</span>';
						}

						echo '<a href="' . APP_ROOT . 'fleet/view-movement/commander-' . $commander->getId() . '/sftr-3" class="button hb lt" title="gérer le commandant et sa flotte">';
							echo '+';
						echo '</a>';

						# $percent Utils::interval($commander->getArrivalDate(), 's');
						/*echo '<span class="progress-bar hb bl" title="' . $percent . '% de progression">';
							echo '<span style="width:' . $percent . '%;" class="content"></span>';
						echo '</span>';*/
					echo '</div>';
				}

				$nextCommander = (isset($commander_listFleet[($i + 1)])) ? $commander_listFleet[($i + 1)] : FALSE;
				if ($nextCommander && $commander->getRBase() !== $nextCommander->getRBase()) {
					echo '<hr />';
				}
			}
			if (count($commander_listFleet) == 0) {
				echo '<p class="info">Vous n\'avez aucun commandant en fonction. <a href="' . APP_ROOT . 'bases/view-school">Vers l\'école de commandement</a></p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';