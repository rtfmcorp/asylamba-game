<?php
$S_CAM_CAN = ASM::$cam->getCurrentSession();
ASM::$cam->changeSession($S_CAM_CAN);

$hasIPresented = FALSE;
for ($i = 0; $i < ASM::$cam->size(); $i++) { 
	if (ASM::$cam->get($i)->rPlayer == CTR::$data->get('playerId')) {
		$hasIPresented = TRUE;
		break;
	}
}

echo '<div class="component new-message">';
	echo '<div class="head skin-2">';
		echo '<h2>Présentez-vous</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if (CTR::$data->get('playerInfo')->get('status') >= 2) {
				echo '<form action="' . Format::actionBuilder('postulate', ['relection' => ASM::$elm->get(0)->id]) . '" method="post">';
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

ASM::$cam->changeSession($S_CAM_1);
