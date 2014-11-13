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
						echo '<a href="' . APP_ROOT . 'fleet"><img src="' . MEDIA . 'commander/medium/' . $report_report->avatarA . '.png" alt="' . $report_report->nameA . '" /></a>';
						$level = Commander::nbLevelUp($report_report->levelA, $report_report->experienceA + $report_report->expCom);
						echo '<em>' . CommanderResources::getInfo($report_report->levelA, 'grade') . ($level > 0 ? ' <span class="bonus">a passé ' . $level . ' grade' . Format::addPlural($level) . '</span>' : NULL) . '</em>';
						echo '<strong>' . $report_report->nameA . '</strong>';
						echo '<em>expérience : ' . Format::numberFormat($report_report->experienceA);
							echo ' <span class="bonus">+ ' . Format::numberFormat($report_report->expCom) . '</span>';
						echo '</em>';
						echo '<em>victoires : ' . $report_report->palmaresA . ' <span class="bonus">+ 1</span></em>';
						echo '<em><span class="bonus">' . Format::number($report_report->resources) . '</span> ressource' . Format::addPlural($report_report->resources) . ' gagnée' . Format::addPlural($report_report->resources) . '</em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'commander/medium/' . $report_report->avatarA . '.png" alt="' . $report_report->nameA . '" />';
						echo '<em>' . CommanderResources::getInfo($report_report->levelA, 'grade') . '</em>';
						echo '<strong>' . $report_report->nameA . '</strong>';
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
						echo '<em>&ensp;</em>';
					echo '</div>';
				}
			} else {
				echo '<div class="number-box grey">';
					echo '<span class="value">Défaite</span>';
				echo '</div>';
				echo '<hr />';
				echo '<div class="commander">';
					echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $report_report->rPlayerDefender . '" />';
					echo '<em>' . CommanderResources::getInfo($report_report->levelA, 'grade') . '</em>';
					echo '<strong>' . $report_report->nameA . '</strong>';
					if ($report_report->rPlayerAttacker == CTR::$data->get('playerId')) {
						echo '<em>expérience : ' . Format::numberFormat($report_report->experienceA) . '</em>';
						echo '<em>victoire : ' . $report_report->palmaresA . '</em>';
						echo '<em>&ensp;</em>';
					} else {
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
						echo '<em>&ensp;</em>';
					}
				echo '</div>';
			}

			$PEVBegin 		= 0;
			$PEVEnd			= 0;
			$PEVDiference 	= 0;

			for ($i = 0; $i < 12; $i++) { 
				$PEVBegin += $report_report->totalInBeginA[$i] * ShipResource::getInfo($i, 'pev');
				$PEVEnd += $report_report->diferenceA[$i] * ShipResource::getInfo($i, 'pev');
				$PEVDiference += $report_report->totalAtEndA[$i] * ShipResource::getInfo($i, 'pev');
			}

			echo '<div class="dammage">';
				echo '<table>';
					for ($i = 0; $i < 1; $i++) { 
						echo '<tr>';
							echo '<td>Evolution des PEV</td>';
							echo '<td>' . (($PEVBegin == 0) ? '—' : $PEVBegin) . '</td>';
							echo '<td>' . (($PEVEnd == 0) ? '—' : $PEVEnd) . '</td>';
							echo '<td>' . (($PEVDiference == 0) ? '—' : $PEVDiference) . '</td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';

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
			echo '<img src="' . MEDIA . 'commander/medium/t1-c0.png" alt="Rebelle" />';
			echo '<h2>Défenseur</h2>';
			echo '<em>Rebelle</em>';
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
						echo '<a href="' . APP_ROOT . 'fleet"><img src="' . MEDIA . 'commander/medium/' . $report_report->avatarD . '.png" alt="' . $report_report->nameD . '" /></a>';
						$level = Commander::nbLevelUp($report_report->levelD, $report_report->experienceD + $report_report->expCom);
						echo '<em>' . CommanderResources::getInfo($report_report->levelD, 'grade') . ($level > 0 ? ' <span class="bonus">a passé ' . $level . ' grade</span>' : NULL) . '</em>';
						echo '<strong>' . $report_report->nameD . '</strong>';
						echo '<em>expérience : ' . Format::numberFormat($report_report->experienceD);
							echo ' <span class="bonus">+ ' . Format::numberFormat($report_report->expCom) . '</span>';
						echo '</em>';
						echo '<em>victoire : ' . $report_report->palmaresD . ' <span class="bonus">+ 1</span></em>';
						echo '<em>&ensp;</em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<em>' . CommanderResources::getInfo($report_report->levelD, 'grade') . '</em>';
						if ($report_report->rPlayerDefender == 0) {
							echo '<img src="' . MEDIA . 'commander/medium/t1-c0.png" alt="' . $report_report->nameD . '" />';
							echo '<strong>Rebelle</strong>';
						} else {
							echo '<img src="' . MEDIA . 'commander/medium/' . $report_report->avatarD . '.png" alt="' . $report_report->nameD . '" />';
							echo '<strong>' . $report_report->nameD . '</strong>';
						}
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
						echo '<em>&ensp;</em>';
					echo '</div>';
				}
			} else {
				echo '<div class="number-box grey">';
					echo '<span class="value">Défaite</span>';
				echo '</div>';
				echo '<hr />';
				if ($report_report->rPlayerDefender == CTR::$data->get('playerId')) {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $report_report->nameD . '" />';
						echo '<em>' . CommanderResources::getInfo($report_report->levelD, 'grade') . '</em>';
						echo '<strong>' . $report_report->nameD . '</strong>';
						echo '<em>expérience : ' . Format::numberFormat($report_report->experienceD) . '</em>';
						echo '<em>victoire : ' . $report_report->palmaresD . '</em>';
						echo '<em>&ensp;</em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<img src="' . MEDIA . 'fleet/memorial.png" alt="' . $report_report->nameD . '" />';
						echo '<em>' . CommanderResources::getInfo($report_report->levelD, 'grade') . '</em>';
						if ($report_report->rPlayerDefender == 0) {
							echo '<strong>Rebelle</strong>';
						} else {
							echo '<strong>' . $report_report->nameD . '</strong>';
						}
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
						echo '<em>&ensp;</em>';
					echo '</div>';
				}
			}

		/*	if ($report_report->type == Commander::LOOT) {
				echo '<div class="dammage">';
					echo '<table>';
						echo '<tr>';
							if ($report_report->rPlayerAttacker != $report_report->rPlayerWinner) {
								if ($report_report->resources == 0) {
									echo '<td>Aucune ressource perdue</td>';
								} else {
									echo '<td>' . Format::number($report_report->resources) . ' ressources perdues</td>';
								}
							} else {
								echo '<td></td>';
							}
						echo '</tr>';
					echo '</table>';
				echo '</div>';
			}*/

			$PEVBegin 		= 0;
			$PEVEnd			= 0;
			$PEVDiference 	= 0;

			for ($i = 0; $i < 12; $i++) { 
				$PEVBegin += $report_report->totalInBeginD[$i] * ShipResource::getInfo($i, 'pev');
				$PEVEnd += $report_report->diferenceD[$i] * ShipResource::getInfo($i, 'pev');
				$PEVDiference += $report_report->totalAtEndD[$i] * ShipResource::getInfo($i, 'pev');
			}

			echo '<div class="dammage">';
				echo '<table>';
					for ($i = 0; $i < 1; $i++) { 
						echo '<tr>';
							echo '<td>Evolution des PEV</td>';
							echo '<td>' . (($PEVBegin == 0) ? '—' : $PEVBegin) . '</td>';
							echo '<td>' . (($PEVEnd == 0) ? '—' : $PEVEnd) . '</td>';
							echo '<td>' . (($PEVDiference == 0) ? '—' : $PEVDiference) . '</td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';

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