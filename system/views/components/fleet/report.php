<?php
# report componant
# in aress package

# affiche le compte rendu d'un combat

# require
	# {report}		report
	# {player}		attacker_report
	# {player}		defender_report

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Ares\Resource\CommanderResources;
use Asylamba\Modules\Athena\Resource\ShipResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$commanderManager = $this->getContainer()->get(\Asylamba\Modules\Ares\Manager\CommanderManager::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

echo '<div class="component report">';
	echo '<div class="head skin-1">';
		echo '<img src="' . $mediaPath . 'avatar/medium/' . $attacker_report->avatar . '.png" alt="' . $attacker_report->name . '" />';
		echo '<h2>Attaquant</h2>';
		echo '<em>' . $attacker_report->name . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($report->rPlayerAttacker == $report->rPlayerWinner) {
				echo '<div class="number-box">';
					echo '<span class="value">Victoire</span>';
				echo '</div>';
				echo '<hr />';
				if ($report->rPlayerAttacker == $session->get('playerId')) {
					echo '<div class="commander">';
						echo '<a href="' . $appRoot . 'fleet"><img src="' . $mediaPath . 'commander/medium/' . $report->avatarA . '.png" alt="' . $report->nameA . '" /></a>';
						$level = $commanderManager->nbLevelUp($report->levelA, $report->experienceA + $report->expCom);
						echo '<em>' . CommanderResources::getInfo($report->levelA, 'grade') . ($level > 0 ? ' <span class="bonus">a passé ' . $level . ' grade' . Format::addPlural($level) . '</span>' : NULL) . '</em>';
						echo '<strong>' . $report->nameA . '</strong>';
						echo '<em>expérience : ' . Format::numberFormat($report->experienceA);
							echo ' <span class="bonus">+ ' . Format::numberFormat($report->expCom) . '</span>';
						echo '</em>';
						echo '<em>victoires : ' . $report->palmaresA . ' <span class="bonus">+ 1</span></em>';
						echo '<em><span class="bonus">' . Format::number($report->resources) . '</span> ressource' . Format::addPlural($report->resources) . ' gagnée' . Format::addPlural($report->resources) . '</em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<img src="' . $mediaPath . 'commander/medium/' . $report->avatarA . '.png" alt="' . $report->nameA . '" />';
						echo '<em>' . CommanderResources::getInfo($report->levelA, 'grade') . '</em>';
						echo '<strong>' . $report->nameA . '</strong>';
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
					echo '<img src="' . $mediaPath . 'fleet/memorial.png" alt="' . $report->rPlayerDefender . '" />';
					echo '<em>' . CommanderResources::getInfo($report->levelA, 'grade') . '</em>';
					echo '<strong>' . $report->nameA . '</strong>';
					if ($report->rPlayerAttacker == $session->get('playerId')) {
						echo '<em>expérience : ' . Format::numberFormat($report->experienceA) . '</em>';
						echo '<em>victoire : ' . $report->palmaresA . '</em>';
						echo '<em>&ensp;</em>';
					} else {
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
						echo '<em>&ensp;</em>';
					}
				echo '</div>';
			}

			$PEVBegin 		= 0;
			$PEVDiference 	= 0;
			$PEVEnd			= 0;

			for ($i = 0; $i < 12; $i++) { 
				$PEVBegin += $report->totalInBeginA[$i] * ShipResource::getInfo($i, 'pev');
				$PEVDiference += $report->diferenceA[$i] * ShipResource::getInfo($i, 'pev');
				$PEVEnd += $report->totalAtEndA[$i] * ShipResource::getInfo($i, 'pev');
			}

			echo '<div class="dammage">';
				echo '<table>';
					for ($i = 0; $i < 1; $i++) { 
						echo '<tr>';
							echo '<td>Evolution des PEV</td>';
							echo '<td>' . (($PEVBegin == 0) ? '—' : $PEVBegin) . '</td>';
							echo '<td>' . (($PEVDiference == 0) ? '—' : $PEVDiference) . '</td>';
							echo '<td>' . (($PEVEnd == 0) ? '—' : $PEVEnd) . '</td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';

			echo '<div class="dammage">';
				echo '<table>';
					for ($i = 0; $i < 12; $i++) { 
						echo '<tr>';
							echo '<td>' . ShipResource::getInfo($i, 'name') . '</td>';
							echo '<td>' . (($report->totalInBeginA[$i] == 0) ? '—' : '<span>' . $report->totalInBeginA[$i] . '</span>') . '</td>';
							echo '<td>' . (($report->diferenceA[$i] == 0) ? '—' : '<span>-' . $report->diferenceA[$i] . '</span>') . '</td>';
							echo '<td>' . (($report->totalAtEndA[$i] == 0) ? '—' : '<span>' . $report->totalAtEndA[$i] . '</span>') . '</td>';
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
			echo '<img src="' . $mediaPath . 'commander/medium/t1-c0.png" alt="Rebelle" />';
			echo '<h2>Défenseur</h2>';
			echo '<em>Rebelle</em>';
		} else {
			echo '<img src="' . $mediaPath . 'avatar/medium/' . $defender_report->avatar . '.png" alt="' . $defender_report->name . '" />';
			echo '<h2>Défenseur</h2>';
			echo '<em>' . $defender_report->name . '</em>';
		}
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($report->rPlayerDefender == $report->rPlayerWinner) {
				echo '<div class="number-box">';
					echo '<span class="value">Victoire</span>';
				echo '</div>';
				echo '<hr />';
				if ($report->rPlayerDefender == $session->get('playerId')) {
					echo '<div class="commander">';
						echo '<a href="' . $appRoot . 'fleet"><img src="' . $mediaPath . 'commander/medium/' . $report->avatarD . '.png" alt="' . $report->nameD . '" /></a>';
						$level = $commanderManager->nbLevelUp($report->levelD, $report->experienceD + $report->expCom);
						echo '<em>' . CommanderResources::getInfo($report->levelD, 'grade') . ($level > 0 ? ' <span class="bonus">a passé ' . $level . ' grade</span>' : NULL) . '</em>';
						echo '<strong>' . $report->nameD . '</strong>';
						echo '<em>expérience : ' . Format::numberFormat($report->experienceD);
							echo ' <span class="bonus">+ ' . Format::numberFormat($report->expCom) . '</span>';
						echo '</em>';
						echo '<em>victoire : ' . $report->palmaresD . ' <span class="bonus">+ 1</span></em>';
						echo '<em>&ensp;</em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<em>' . CommanderResources::getInfo($report->levelD, 'grade') . '</em>';
						if ($report->rPlayerDefender == 0) {
							echo '<img src="' . $mediaPath . 'commander/medium/t1-c0.png" alt="' . $report->nameD . '" />';
							echo '<strong>Rebelle</strong>';
						} else {
							echo '<img src="' . $mediaPath . 'commander/medium/' . $report->avatarD . '.png" alt="' . $report->nameD . '" />';
							echo '<strong>' . $report->nameD . '</strong>';
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
				if ($report->rPlayerDefender == $session->get('playerId')) {
					echo '<div class="commander">';
						echo '<img src="' . $mediaPath . 'fleet/memorial.png" alt="' . $report->nameD . '" />';
						echo '<em>' . CommanderResources::getInfo($report->levelD, 'grade') . '</em>';
						echo '<strong>' . $report->nameD . '</strong>';
						echo '<em>expérience : ' . Format::numberFormat($report->experienceD) . '</em>';
						echo '<em>victoire : ' . $report->palmaresD . '</em>';
						echo '<em>&ensp;</em>';
					echo '</div>';
				} else {
					echo '<div class="commander">';
						echo '<img src="' . $mediaPath . 'fleet/memorial.png" alt="' . $report->nameD . '" />';
						echo '<em>' . CommanderResources::getInfo($report->levelD, 'grade') . '</em>';
						if ($report->rPlayerDefender == 0) {
							echo '<strong>Rebelle</strong>';
						} else {
							echo '<strong>' . $report->nameD . '</strong>';
						}
						echo '<em>expérience : ---</em>';
						echo '<em>victoire : ---</em>';
						echo '<em>&ensp;</em>';
					echo '</div>';
				}
			}

		/*	if ($report->type == Commander::LOOT) {
				echo '<div class="dammage">';
					echo '<table>';
						echo '<tr>';
							if ($report->rPlayerAttacker != $report->rPlayerWinner) {
								if ($report->resources == 0) {
									echo '<td>Aucune ressource perdue</td>';
								} else {
									echo '<td>' . Format::number($report->resources) . ' ressources perdues</td>';
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
				$PEVBegin += $report->totalInBeginD[$i] * ShipResource::getInfo($i, 'pev');
				$PEVEnd += $report->diferenceD[$i] * ShipResource::getInfo($i, 'pev');
				$PEVDiference += $report->totalAtEndD[$i] * ShipResource::getInfo($i, 'pev');
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
							echo '<td>' . (($report->totalInBeginD[$i] == 0) ? '—' : '<span>' . $report->totalInBeginD[$i] . '</span>') . '</td>';
							echo '<td>' . (($report->diferenceD[$i] == 0) ? '—' : '<span>-' . $report->diferenceD[$i] . '</span>') . '</td>';
							echo '<td>' . (($report->totalAtEndD[$i] == 0) ? '—' : '<span>' . $report->totalAtEndD[$i] . '</span>') . '</td>';
						echo '</tr>';
					}
				echo '</table>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
