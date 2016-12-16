<?php
# commanderFleet component
# in ares package

# affichage le détail d'un commandant

# require
	# {commander}		commander_commanderFleet
	# {orbitalBase}		ob_commanderFleet

use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Ares\Model\Commander;

$dockStorage = $ob_commanderFleet->getShipStorage();
$lineCoord   = $commander_commanderFleet->getFormatLineCoord();

echo '<div class="component size2 commander-fleet">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'commander/medium/' . $commander_commanderDetail->avatar . '.png" alt="' . $commander_commanderDetail->getName() . '" />';
		echo '<h2>' . $commander_commanderDetail->name . '</h2>';
		echo '<em>' . CommanderResources::getInfo($commander_commanderDetail->level, 'grade') . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="fleet">';
				echo '<table class="army commanderTransfer">';
					echo '<tr>';
						echo '<td></td>';
						for ($i = 5; $i > 0; $i--) { 
							echo '<td>#' . $i . '</td>';
						}
					echo '</tr><tr>';
						echo '<td>Ceci représente la composition de votre flotte. Chaque carré montre une escadrille. Vous pouvez remplir cette escadrille en cliquant dessus puis en cliquant sur un type de vaisseau dans les colonnes à droite.<br /><br />La première colonne représente votre escadrille et la seconde votre hangar.<br /><br />En pressant CTRL + clic (CMD + clic), vous pouvez transférer le maximum de vaisseaux possible.</td>';
					for ($i = 5; $i > 0; $i--) { 
						echo '<td>';
						if (max($lineCoord) >= $i) {
							$nbInColumn = 0;

							foreach ($lineCoord as $k => $v) {
								if ($v == $i) {
									$nbr = $k + 1;
									$nbInColumn++;

									if ($nbr == count($lineCoord)) {
										echo '<span class="block" title="prochaine escadrille disponible">';
											echo '<strong>' . $nbr . '</strong>';
											echo '<em>---</em>';
										echo '</span>';
									} else {
										if ($commander_commanderFleet->getSquadron($k)->getPev() == 0) {
											$full = 'full0';
										} elseif ($commander_commanderFleet->getSquadron($k)->getPev() < 50) {
											$full = 'full1';
										} elseif ($commander_commanderFleet->getSquadron($k)->getPev() < 99) {
											$full = 'full2';
										} else {
											$full = 'full3';
										}

										echo '<span ';
											echo 'class="block squadron ' . $full . '"';
											echo 'data-squadron-id="' . ($nbr - 1) . '"';
											echo 'data-squadron-ships="[' . implode(', ', $commander_commanderFleet->getSquadron($k)->getArrayOfShips()) . ']"';
											echo 'data-squadron-pev="' . $commander_commanderFleet->getSquadron($k)->getPev() . '"';
										echo '>';
											echo '<strong>' . $nbr . '</strong>';
											echo '<em>' . $commander_commanderFleet->getSquadron($k)->getPev(). '/100</em>';
										echo '</span>';
									}
								}
							}

							for ($j = $nbInColumn; $j < 5; $j++) { 
								echo '<span class="block empty"></span>';
							}
						} else {
							for ($j = 0; $j < 5; $j++) { 
								echo '<span class="block empty"></span>';
							}
						}
						echo '</td>';
					}
				echo '</tr></table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

if ($commander_commanderFleet->getStatement() == Commander::AFFECTED) {
	echo '<div ';
		echo 'class="component commander-fleet baseTransfer" ';
		echo 'data-base="' . $commander_commanderFleet->getRBase() . '" ';
		echo 'data-commander="' . $commander_commanderFleet->getId() . '" ';
	echo '>';
		echo '<div class="head skin-3">';
			echo '<img class="left" src="' . MEDIA . 'fleet/commanders.png" alt="/" />';
			echo '<img class="right" src="' . MEDIA . 'orbitalbase/dock2.png" alt="/" />';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo '<div class="list-ship squadron">';
					for ($i = 0; $i < 12; $i++) { 
						echo '<a href="#" class="empty" data-ship-id="' . $i . '">';
							echo '<img src="' .  MEDIA . 'ship/picto/' . ShipResource::getInfo($i, 'imageLink') . '.png" alt="" />';
							echo '<span class="text">';
								echo '<span class="quantity">0</span>';
								echo '<span>' . ShipResource::getInfo($i, 'codeName') . '</span>';
							echo '</span>';
						echo '</a>';
					}
				echo '</div>';
				echo '<div class="list-ship dock">';
					for ($i = 0; $i < 12; $i++) {
						echo '<a href="#" ' . (($dockStorage[$i] == 0) ? 'class="empty"' : '') . ' data-ship-id="' . $i . '">';
							echo '<img src="' .  MEDIA . 'ship/picto/' . ShipResource::getInfo($i, 'imageLink') . '.png" alt="" />';
							echo '<span class="text">';
								echo '<span class="quantity">' . $dockStorage[$i] . '</span>';
								echo '<span>' . ShipResource::getInfo($i, 'codeName') . '</span>';
							echo '</span>';
						echo '</a>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}