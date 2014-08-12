<?php
# require
$S_PAM_DGS = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($PLAYER_SENATE_TOKEN);

echo '<div class="component player size2">';
	echo '<div class="head skin-2">';
		echo '<h2>Sénat</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="info">Bla</p>';
			for ($i = 0; $i < ASM::$pam->size(); $i++) {
				$p =  ASM::$pam->get($i);

				$status = ColorResource::getInfo($p->rColor, 'status');

				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $p->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $p->avatar . '.png" alt="' . $p->name . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[$p->status - 1] . '</span>';
					echo '<strong class="name">' . $p->name . '</strong>';
					echo '<span class="experience">' . Format::number($p->factionPoint) . ' de prestige</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_DGS);
?>