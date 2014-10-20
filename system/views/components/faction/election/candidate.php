<?php
$parser = new Parser();
$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component player profil size1">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if ($faction->electionStatement == Color::ELECTION) {
				$hasVoted = FALSE;
				if (ASM::$vom->size() == 1) {
					$hasVoted = TRUE;
				}
				
				echo '<div class="build-item">';
					if ($hasVoted) {
						if (ASM::$vom->get()->rCandidate == $candidat->rPlayer) {
							echo '<span class="button disable" style="text-align: center;">';
								echo '<span class="text" style="line-height: 35px;">Vous avez voté pour lui</span>';
							echo '</span>';
						} else {
							echo '<span class="button disable" style="text-align: center;">';
								echo '<span class="text" style="line-height: 35px;">---</span>';
							echo '</span>';
						}
					} else {
						echo '<a class="button" href="' . APP_ROOT . 'action/a-vote/relection-' . $rElection . '/rcandidate-' . $candidat->rPlayer . '" style="text-align: center;">';
							echo '<span class="text" style="line-height: 35px;">Voter</span>';
						echo '</a>';
					}
				echo '</div>';
			} else {
				echo '<div class="center-box">';
					echo '<span class="label">Se présente</span>';
				echo '</div>';
			}

			echo '<div class="player">';
				echo '<a href="' . APP_ROOT . 'diary/player-' . $candidat->rPlayer . '">';
					echo '<img src="' . MEDIA . 'avatar/small/' . $candidat->avatar . '.png" alt="' . $candidat->name . '" />';
				echo '</a>';
				echo '<span class="title">' . $status[$candidat->status - 1] . '</span>';
				echo '<strong class="name"> ' . $candidat->name . '</strong>';
				echo '<span class="experience">' . Format::number($candidat->factionPoint) . ' de prestige</span>';
			echo '</div>';

			echo '<div class="center-box">';
				echo '<span class="label">Par son programme politique</span>';
			echo '</div>';

			echo '<p class="info">' . $parser->parse($candidat->program) .'</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component size2">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p>File de discussion pour la campagne</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$cam->changeSession($S_CAM_1);
?>