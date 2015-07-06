<?php
$S_PAM_1 = ASM::$pam->getCurrentSession();
ASM::$pam->newSession();
ASM::$pam->load(
	['rGodFather' => CTR::$data->get('playerId')]
);

# display
echo '<div class="component player rank">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Liste de vos filleuls</h4>';

			for ($i = 0; $i < ASM::$pam->size(); $i++) {
				$player = ASM::$pam->get($i);

				echo '<div class="player color' . $player->rColor . ' active">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' . $player->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $player->avatar . '.png" alt="' . $player->name . '" class="picto">';
					echo '</a>';
					echo '<span class="title">' . $player->name . '</span>';
					echo '<strong class="name">' . $player->name . '</strong>';
					echo '<span class="experience">niveau ' . $player->level . '</span>';
				echo '</div>';
			}

			if (ASM::$pam->size() == 0) {
				echo '<p>Vous n\'avez encore aucun filleul.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_1);