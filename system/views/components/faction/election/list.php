<?php
$S_CAM_1 = ASM::$cam->getCurrentSession();
ASM::$cam->changeSession($S_CAM_CAN);

$hasIPresented = FALSE;
for ($i = 0; $i < ASM::$cam->size(); $i++) { 
	if (ASM::$cam->get($i)->rPlayer == CTR::$data->get('playerId')) {
		$hasIPresented = TRUE;
		break;
	}
}

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Candidat' . Format::plural(ASM::$cam->size()) . ' à l\'élection</h4>';

			echo '<div class="set-item">';
				if ($faction->electionStatement == Color::CAMPAIGN && !$hasIPresented) {
					echo '<a class="' . (CTR::$get->equal('candidate', 'create')  ? 'active' : NULL) . ' item" href="' . APP_ROOT . 'faction/view-election/candidate-create">';
						echo '<div class="left">';
							echo '<span>+</span>';
						echo '</div>';

						echo '<div class="center">';
							echo 'Proposer sa candidature';
						echo '</div>';
					echo '</a>';
				}

				if (ASM::$cam->size() > 0) {
					for ($i = 0; $i < ASM::$cam->size(); $i++) {
						$candidat = ASM::$cam->get($i);
						$status = ColorResource::getInfo($faction->id, 'status');

						echo '<div class="item">';
							echo '<div class="left">';
								echo '<img src="' . MEDIA . 'avatar/small/' . $candidat->avatar . '.png" alt="' . $candidat->name . '" />';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong>' . $candidat->name . '</strong>';
								echo $status[$candidat->status - 1];
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (CTR::$get->equal('candidate', $candidat->id) ? 'active' : NULL) . '" href="' . APP_ROOT . 'faction/view-election/candidate-' . $candidat->id . '"></a>';
							echo '</div>';
						echo '</div>';
					}

					echo '</div>';
				} else {
					echo '</div>';
					echo '<p>Il n\'y a aucun candidat pour l\'instant.</p>';
				}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$cam->changeSession($S_CAM_1);