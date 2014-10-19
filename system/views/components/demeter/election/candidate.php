<?php
$S_CAM_CAN = ASM::$cam->getCurrentSession();
ASM::$cam->changeSession($S_CAM_CAN);

$S_VOM_CAN = ASM::$vom->getCurrentSession();
ASM::$vom->changeSession($VOM_ELC_TOKEN);

$hasIPresented = FALSE;
for ($i = 0; $i < ASM::$cam->size(); $i++) { 
	if (ASM::$cam->get($i)->rPlayer == CTR::$data->get('playerId')) {
		$hasIPresented = TRUE;
		break;
	}
}

$parser = new Parser();

$hasVoted = FALSE;
if (ASM::$vom->size() == 1) {
	$hasVoted = TRUE;
}

if ($faction->electionStatement == Color::CAMPAIGN && !$hasIPresented) {
	echo '<div class="component new-message">';
		echo '<div class="head skin-2">';
			echo '<h2>Présentez-vous</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				if (CTR::$data->get('playerInfo')->get('status') >= 2) {
					echo '<form action="' . APP_ROOT . 'action/a-postulate/relection-' . ASM::$elm->get(0)->id . '" method="post">';
						echo '<p><label for="program">Votre message politique</label></p>';
						echo '<p class="input input-area"><textarea id="program" name="program" required style="height: 300px;"></textarea></p>';

						echo '<p class="button"><button type="submit">Se présenter</button></p>';
					echo '</form>';
				} else {
					echo '<form action="#" method="post">';
						echo '<p><label for="program">Votre message politique</label></p>';
						echo '<p class="input input-area"><textarea id="program" name="program" disabled style="height: 300px;">';
							echo 'Vous ne pouvez pas vous présenter, vous n\'avez pas assez de prestige.';
						echo '</textarea></p>';

						echo '<p class="button"><button type="submit" disabled>Se présenter</button></p>';
					echo '</form>';
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

for ($i = 0; $i < ASM::$cam->size(); $i++) {
	$candidat = ASM::$cam->get($i);
	$status = ColorResource::getInfo($faction->id, 'status');

	echo '<div class="component player profil size1">';
		echo '<div class="head skin-2">';
			if ($i == 0) {
				echo '<h2>Liste des candidats</h2>';
			}
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				if ($faction->electionStatement == Color::ELECTION) {
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
					echo '<a href="' . APP_ROOT . 'diary/player-' . $candidat->id . '">';
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
}

ASM::$cam->changeSession($S_CAM_1);
ASM::$vom->changeSession($S_VOM_CAN);
?>