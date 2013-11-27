<?php
# report componant
# in aress package

# affiche le compte rendu d'un combat

# require
	# {report}		report_report
	# {player}		attacker_report
	# {player}		defender_report

echo '<div class="component report">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'avatar/medium/' . $attacker_report->getAvatar() . '.png" alt="' . $attacker_report->getName() . '" />';
		echo '<h2>Attaquant</h2>';
		echo '<em>' . $attacker_report->getName() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($report_report->rPlayerAttacker == $report_report->rPlayerWinner) {
				echo '<div class="number-box">';
					echo '<span class="value">Victoire</span>';
				echo '</div>';
				echo '<hr />';
				if ($report_report->rPlayerAttacker == CTR::$data->get('playerId')) {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c' . $attacker_report->getRColor() . '.png" alt="' . $commanders_report[0]->name . '" />';
						echo '<strong>Commandant ' . $commanders_report[0]->name . '</strong>';
						echo '<em>grade : ' . $commanders_report[0]->level;
							$level = Commander::nbLevelUp($commanders_report[0]->level, $commanders_report[0]->experience + $report_report->expCom);
							if ($level > 0) {
								echo ' <span class="bonus">+ ' . $level . '</span>';
							}
						echo '</em>';
						echo '<em>expérience : ' . Format::numberFormat($commanders_report[0]->experience);
							echo ' <span class="bonus">+ ' . Format::numberFormat($report_report->expCom) . '</span>';
						echo '</em>';
						echo '<em>victoire : ' . $commanders_report[0]->palmares . ' <span class="bonus">+ 1</span></em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c' . $attacker_report->getRColor() . '.png" alt="' . $commanders_report[0]->name . '" />';
						echo '<strong>Commandant ' . $commanders_report[0]->name . '</strong>';
						echo '<em>grade : ' . $commanders_report[0]->level . '</em>';
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
					echo '</div>';
				}
			} else {
				echo '<div class="number-box grey">';
					echo '<span class="value">Défaite</span>';
				echo '</div>';
				echo '<hr />';
				echo '<div class="commander">';
					echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $commanders_report[0]->name . '" />';
					echo '<strong>Commandant ' . $commanders_report[0]->name . '</strong>';
					echo '<em>grade : ' . $commanders_report[0]->level . '</em>';
					if ($report_report->rPlayerAttacker == CTR::$data->get('playerId')) {
						echo '<em>expérience : ' . Format::numberFormat($commanders_report[0]->experience) . '</em>';
						echo '<em>victoire : ' . $commanders_report[0]->palmares . '</em>';
					} else {
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
					}
				echo '</div>';
			}

			$stateAttacker = $report_report->getShipsStateAttacker($commanders_report);

			echo '<div class="dammage">';
				echo '<table>';
					for ($i = 0; $i < 12; $i++) { 
						echo '<tr>';
							echo '<td>' . ShipResource::getInfo($i, 'name') . '</td>';
							echo '<td>' . (($stateAttacker[0][$i] == 0) ? '—' : '<span>' . $stateAttacker[0][$i] . '</span>') . '</td>';
							echo '<td>' . (($stateAttacker[2][$i] == 0) ? '—' : '<span>-' . $stateAttacker[2][$i] . '</span>') . '</td>';
							echo '<td>' . (($stateAttacker[1][$i] == 0) ? '—' : '<span>' . $stateAttacker[1][$i] . '</span>') . '</td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component report">';
	echo '<div class="head skin-1">';
		if ($defender_report == FALSE) {
			echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c0.png" alt="Commandant rebelle" />';
			echo '<h2>Défenseur</h2>';
			echo '<em>Commandant rebelle</em>';
		} else {
			echo '<img src="' . MEDIA . 'avatar/medium/' . $defender_report->getAvatar() . '.png" alt="' . $defender_report->getName() . '" />';
			echo '<h2>Défenseur</h2>';
			echo '<em>' . $defender_report->getName() . '</em>';
		}
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($report_report->rPlayerDefender == $report_report->rPlayerWinner) {
				echo '<div class="number-box">';
					echo '<span class="value">Victoire</span>';
				echo '</div>';
				echo '<hr />';
				if ($report_report->rPlayerDefender == CTR::$data->get('playerId')) {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c' . $attacker_report->getRColor() . '.png" alt="' . $commanders_report[1]->name . '" />';
						echo '<strong>Commandant ' . $commanders_report[1]->name . '</strong>';
						echo '<em>grade : ' . $commanders_report[1]->level . ' <span class="bonus">+ </span></em>';
						echo '<em>expérience : ' . Format::numberFormat($commanders_report[1]->experience);
							echo ' <span class="bonus">+ ' . Format::numberFormat($report_report->expCom) . '</span>';
						echo '</em>';
						echo '<em>victoire : ' . $commanders_report[1]->palmares . ' <span class="bonus">+ 1</span></em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						if ($report_report->rPlayerDefender == 0) {
							echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c0.png" alt="' . $commanders_report[1]->name . '" />';
							echo '<strong>Commandant rebelle</strong>';
						} else {
							echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c' . $attacker_report->getRColor() . '.png" alt="' . $commanders_report[1]->name . '" />';
							echo '<strong>Commandant ' . $commanders_report[1]->name . '</strong>';
						}
						echo '<em>grade : ' . $commanders_report[1]->level . '</em>';
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
					echo '</div>';
				}
			} else {
				echo '<div class="number-box grey">';
					echo '<span class="value">Défaite</span>';
				echo '</div>';
				echo '<hr />';
				if ($report_report->rPlayerDefender == CTR::$data->get('playerId')) {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $commanders_report[1]->name . '" />';
						echo '<strong>Commandant ' . $commanders_report[1]->name . '</strong>';
						echo '<em>grade : ' . $commanders_report[1]->level . '</em>';
						echo '<em>expérience : ' . Format::numberFormat($commanders_report[1]->experience) . '</em>';
						echo '<em>victoire : ' . $commanders_report[1]->palmares . '</em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $commanders_report[1]->name . '" />';
						if ($report_report->rPlayerDefender == 0) {
							echo '<strong>Commandant rebelle</strong>';
						} else {
							echo '<strong>Commandant ' . $commanders_report[1]->name . '</strong>';
						}
						echo '<em>grade : ' . $commanders_report[1]->level . '</em>';
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
					echo '</div>';
				}
			}

			$stateDefender = $report_report->getShipsStateDefender($commanders_report);

			echo '<div class="dammage">';
				echo '<table>';
					for ($i = 0; $i < 12; $i++) { 
						echo '<tr>';
							echo '<td>' . ShipResource::getInfo($i, 'name') . '</td>';
							echo '<td>' . (($stateDefender[0][$i] == 0) ? '—' : '<span>' . $stateDefender[0][$i] . '</span>') . '</td>';
							echo '<td>' . (($stateDefender[2][$i] == 0) ? '—' : '<span>-' . $stateDefender[2][$i] . '</span>') . '</td>';
							echo '<td>' . (($stateDefender[1][$i] == 0) ? '—' : '<span>' . $stateDefender[1][$i] . '</span>') . '</td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>