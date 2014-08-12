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
		echo '<img src="' . MEDIA . 'avatar/medium/' . $attacker_report->avatar . '.png" alt="' . $attacker_report->name . '" />';
		echo '<h2>Attaquant</h2>';
		echo '<em>' . $attacker_report->name . '</em>';
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
						echo '<img src="' . MEDIA . 'commander/medium/' . $report_report->rPlayerAttacker . '.png" alt="' . $report_report->rPlayerAttacker . '" />';
						echo '<strong>Commandant ' . $report_report->rPlayerAttacker . '</strong>';
						echo '<em>grade : ' . $report_report->rPlayerAttacker;
							/*$level = Commander::nbLevelUp($report_report->level, $report_report->experience + $report_report->expCom);
							if ($level > 0) {
								echo ' <span class="bonus">+ ' . $level . '</span>';
							}*/
						echo '</em>';
						echo '<em>expérience : ' . Format::numberFormat($report_report->expPlayerA);
							echo ' <span class="bonus">+ ' . Format::numberFormat($report_report->expPlayerA) . '</span>';
						echo '</em>';
						echo '<em>victoire : ' . $report_report->rPlayerAttacker . ' <span class="bonus">+ 1</span></em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'commander/medium/' . $report_report->rPlayerDefender . '.png" alt="' . $report_report->rPlayerDefender . '" />';
						echo '<strong>Commandant ' . $report_report->rPlayerDefender . '</strong>';
						echo '<em>grade : ' . $report_report->rPlayerDefender . '</em>';
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
					echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $report_report->rPlayerDefender . '" />';
					echo '<strong>Commandant ' . $report_report->rPlayerDefender . '</strong>';
					echo '<em>grade : ' . $report_report->rPlayerDefender . '</em>';
					if ($report_report->rPlayerAttacker == CTR::$data->get('playerId')) {
						echo '<em>expérience : ' . Format::numberFormat($report_report->rPlayerDefender) . '</em>';
						echo '<em>victoire : ' . $report_report->rPlayerDefender . '</em>';
					} else {
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
					}
				echo '</div>';
			}

			echo '<div class="dammage">';
				echo '<table>';
					for ($i = 0; $i < 12; $i++) { 
						echo '<tr>';
							echo '<td>' . ShipResource::getInfo($i, 'name') . '</td>';
							echo '<td>' . (($report_report->totalInBeginA[$i] == 0) ? '—' : '<span>' . $report_report->totalInBeginA[$i] . '</span>') . '</td>';
							echo '<td>' . (($report_report->diferenceA[$i] == 0) ? '—' : '<span>-' . $report_report->diferenceA[$i] . '</span>') . '</td>';
							echo '<td>' . (($report_report->totalAtEndA[$i] == 0) ? '—' : '<span>' . $report_report->totalAtEndA[$i] . '</span>') . '</td>';
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
			echo '<img src="' . MEDIA . 'avatar/medium/' . $defender_report->avatar . '.png" alt="' . $defender_report->name . '" />';
			echo '<h2>Défenseur</h2>';
			echo '<em>' . $defender_report->name . '</em>';
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
						echo '<img src="' . MEDIA . 'commander/medium/' . $defender_report->rColor . '.png" alt="' . $report_report->rPlayerDefender . '" />';
						echo '<strong>Commandant ' . $report_report->rPlayerDefender . '</strong>';
						echo '<em>grade : ' . $report_report->rPlayerDefender . ' <span class="bonus">+ </span></em>';
						echo '<em>expérience : ' . Format::numberFormat($report_report->rPlayerDefender);
							echo ' <span class="bonus">+ ' . Format::numberFormat($report_report->expCom) . '</span>';
						echo '</em>';
						echo '<em>victoire : ' . $report_report->rPlayerDefender . ' <span class="bonus">+ 1</span></em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						if ($report_report->rPlayerDefender == 0) {
							echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c0.png" alt="' . $report_report->rPlayerDefender . '" />';
							echo '<strong>Commandant rebelle</strong>';
						} else {
							echo '<img src="' . MEDIA . 'commander/medium/c1-l1-c' . $attacker_report->getRColor() . '.png" alt="' . $report_report->rPlayerDefender . '" />';
							echo '<strong>Commandant ' . $report_report->rPlayerDefender . '</strong>';
						}
						echo '<em>grade : ' . $report_report->rPlayerDefender . '</em>';
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
						echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $report_report->rPlayerDefender . '" />';
						echo '<strong>Commandant ' . $report_report->rPlayerDefender . '</strong>';
						echo '<em>grade : ' . $report_report->rPlayerDefender . '</em>';
						echo '<em>expérience : ' . Format::numberFormat($report_report->rPlayerDefender) . '</em>';
						echo '<em>victoire : ' . $report_report->rPlayerDefender . '</em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $report_report->rPlayerDefender . '" />';
						if ($report_report->rPlayerDefender == 0) {
							echo '<strong>Commandant rebelle</strong>';
						} else {
							echo '<strong>Commandant ' . $report_report->rPlayerDefender . '</strong>';
						}
						echo '<em>grade : ' . $report_report->rPlayerDefender . '</em>';
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
					echo '</div>';
				}
			}

			echo '<div class="dammage">';
				echo '<table>';
					for ($i = 0; $i < 12; $i++) { 
						echo '<tr>';
							echo '<td>' . ShipResource::getInfo($i, 'name') . '</td>';
							echo '<td>' . (($report_report->totalInBeginD[$i] == 0) ? '—' : '<span>' . $report_report->totalInBeginD[$i] . '</span>') . '</td>';
							echo '<td>' . (($report_report->diferenceD[$i] == 0) ? '—' : '<span>-' . $report_report->diferenceD[$i] . '</span>') . '</td>';
							echo '<td>' . (($report_report->totalAtEndD[$i] == 0) ? '—' : '<span>' . $report_report->totalAtEndD[$i] . '</span>') . '</td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';