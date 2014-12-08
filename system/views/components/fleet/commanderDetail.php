<?php
# commanderDetail component
# in ares package

# affichage le détail d'un commandant

# require
	# {commander}		commander_commanderDetail

echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'commander/medium/' . $commander_commanderDetail->avatar . '.png" alt="' . $commander_commanderDetail->getName() . '" />';
		echo '<h2>' . $commander_commanderDetail->name . '</h2>';
		echo '<em>' . CommanderResources::getInfo($commander_commanderDetail->level, 'grade') . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($commander_commanderDetail->getStatement() == COM_AFFECTED) {
				echo '<div class="tool">';
					echo '<span><a href="' . Format::actionBuilder('emptycommander', ['id' => $commander_commanderDetail->id]) '">retirer tous les vaisseaux</a></span>';
					echo '<span><a href="' . Format::actionBuilder('firecommander', ['id' => $commander_commanderDetail->id]) '" class="hb lt" title="licencier l\'officier">&#215;</a></span>';
					echo '<span><a href="' . Format::actionBuilder('affectcommander', ['id' => $commander_commanderDetail->id]) . '" class="hb lt" title="remettre dans l\'école">E</a></span>';
				echo '</div>';
			}

			if ($commander_commanderDetail->getStatement() == COM_INSCHOOL) {
				echo '<div class="number-box">';
					echo '<span class="label">état de l\'officier</span>';
					echo '<span class="value">A l\'école</span>';
				echo '</div>';
			} elseif ($commander_commanderDetail->getStatement() == COM_AFFECTED) {
				echo '<div class="number-box">';
					echo '<span class="label">état de l\'officier</span>';
					echo '<span class="value">A quai</span>';
				echo '</div>';
			} elseif ($commander_commanderDetail->getStatement() == COM_MOVING) {
				echo '<div class="number-box">';
					echo '<span class="label">état de l\'officier</span>';
					echo '<span class="value">En mission</span>';
				echo '</div>';
				# affichage conditionnel d'info
				switch ($commander_commanderDetail->getTypeOfMove()) {
					case COM_MOVE: 
						echo '<div class="number-box">';
							echo '<span class="label">mission</span>';
							echo '<span class="value">Déplacement</span>';
						echo '</div>';
						echo '<div class="number-box">';
							echo '<span class="label">vers</span>';
							echo '<span class="value">' . $commander_commanderDetail->destinationPlaceName . '</span>';
						echo '</div>'; break;
					case COM_LOOT: 
						echo '<div class="number-box">';
							echo '<span class="label">mission</span>';
							echo '<span class="value">Pillage</span>';
						echo '</div>';
						echo '<div class="number-box">';
							echo '<span class="label">cible</span>';
							echo '<span class="value">' . $commander_commanderDetail->destinationPlaceName . '</span>';
						echo '</div>'; break;
					case COM_COLO: 
						echo '<div class="number-box">';
							echo '<span class="label">mission</span>';
							echo '<span class="value">Colonisation</span>';
						echo '</div>';
						echo '<div class="number-box">';
							echo '<span class="label">cible</span>';
							echo '<span class="value">' . $commander_commanderDetail->destinationPlaceName . '</span>';
						echo '</div>'; break;
					case COM_BACK: 
						echo '<div class="number-box">';
							echo '<span class="label">mission</span>';
							echo '<span class="value">Retour victorieux</span>';
						echo '</div>';
						echo '<div class="number-box">';
							echo '<span class="label">ressources transportées</span>';
							echo '<span class="value">' . Format::numberFormat($commander_commanderDetail->getResourcesTransported()) . '</span>';
						echo '</div>'; break;
					default: break;
				}
			} elseif ($commander_commanderDetail->getStatement() == COM_DEAD) {
				echo '<div class="number-box">';
					echo '<span class="label">état de l\'officier</span>';
					echo '<span class="value">Tombé au combat</span>';
				echo '</div>';
			}
			echo '<hr />';

			echo '<div class="number-box grey">';
				echo '<span class="label">nom</span>';
				echo '<span class="value">' . $commander_commanderDetail->getName() . '</span>';
			echo '</div>';
			echo '<div class="number-box">';
				echo '<span class="label">victoire' . Format::addPlural($commander_commanderDetail->getPalmares()) . '</span>';
				echo '<span class="value">' . $commander_commanderDetail->getPalmares() . '</span>';
			echo '</div>';
			echo '<div class="number-box grey">';
				echo '<span class="label">grade</span>';
				echo '<span class="value">' . $commander_commanderDetail->getLevel() . '</span>';
			echo '</div>';

			if (in_array($commander_commanderDetail->getStatement(), array(COM_AFFECTED, COM_MOVING, COM_INSCHOOL))) {
				echo '<div class="number-box grey">';
					echo '<span class="label">expérience</span>';
					$expToLvlUp = $commander_commanderDetail->experienceToLevelUp();
					$percent = Format::percent($commander_commanderDetail->getExperience() - ($expToLvlUp / 2), $expToLvlUp - ($expToLvlUp / 2));
					echo '<span class="value">' . Format::numberFormat($commander_commanderDetail->getExperience()) . ' / ' . Format::numberFormat($commander_commanderDetail->experienceToLevelUp()) . '</span>';
					echo '<span title="' . $percent . ' %" class="progress-bar hb bl">';
						echo '<span class="content" style="width: ' . $percent . '%;"></span>';
					echo '</span>';
				echo '</div>';
			}

		echo '</div>';
	echo '</div>';
echo '</div>';