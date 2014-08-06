<?php
# commanderFleet component
# in ares package

# affichage le détail d'un commandant

# require
	# {commander}		commander_commanderFleet
	# {orbitalBase}		ob_commanderFleet

$dockStorage = $ob_commanderFleet->getShipStorage();
$lineCoord   = $commander_commanderFleet->getFormatLineCoord();

echo '<div class="component size2 commander-fleet">';
	echo '<div class="head skin-2">';
		echo '<h2>Représentation des escadrilles</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="fleet">';
				echo '<table class="army commanderTransfer"><tr>';
					for ($i = 5; $i > 0; $i--) { 
						echo '<td>#' . $i . '</td>';
					}
				echo '</tr><tr>';
					for ($i = 5; $i > 0; $i--) { 
						echo '<td>';
						if (max($lineCoord) >= $i) {
							foreach ($lineCoord as $k => $v) {
								if ($v == $i) {
									$nbr = $k + 1;
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
											echo 'class="block squadron  ' . $full . '"';
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
						}
						echo '</td>';
					}
				echo '</tr></table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

if ($commander_commanderFleet->getStatement() == COM_AFFECTED) {
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