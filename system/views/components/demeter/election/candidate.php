<?php
$S_CAM_1 = ASM::$cam->getCurrentSession();
ASM::$cam->changeSession($S_CAM_CAN);

$hasIPresented = TRUE;
for ($i = 0; $i < ASM::$cam->size(); $i++) { 
	if (ASM::$cam->get($i)->rPlayer == CTR::$data->get('playerId')) {
		$hasIPresented = FALSE;
		break;
	}
}

if (ASM::$cam->size() == 0 || $hasIPresented) {
	echo '<div class="component new-message">';
		echo '<div class="head skin-2">';
			echo '<h2>Présentez-vous</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo '<form action="' . APP_ROOT . 'action/a-postulate/relection-' . ASM::$elm->get(0)->id . '" method="post">';
					echo '<p><label for="program">Votre message politique</label></p>';
					echo '<p class="input input-area"><textarea id="program" name="program" style="height: 300px;"></textarea></p>';

					echo '<p class="button"><button type="submit">Se présenter</button></p>';
				echo '</form>';
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
				echo '<div class="center-box">';
					echo '<span class="label">Se présente</span>';
				echo '</div>';

				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $candidat->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $candidat->id . '.png" alt="' . $candidat->id . '" />';
					echo '</a>';
					echo '<span class="title">Titre ' . $status[3 - 1] . '</span>';
					echo '<strong class="name">Nom ' . $candidat->id . '</strong>';
					echo '<span class="experience">' . Format::number($candidat->id) . ' de prestige</span>';
				echo '</div>';

				echo '<div class="center-box">';
					echo '<span class="label">Par son programme politique</span>';
				echo '</div>';

				echo '<p class="info">' . $candidat->program .'</p>';
			echo '</div>';
		echo '</div>';
	echo '</div>';	
}

ASM::$cam->changeSession($S_CAM_1);
?>