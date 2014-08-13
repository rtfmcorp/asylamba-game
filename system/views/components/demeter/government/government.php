<?php
# require
$S_PAM_DGG = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($PLAYER_GOV_TOKEN);

$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component profil">';
	echo '<div class="head skin-1">';
		echo '<h1>Gouvernement</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$have = FALSE;
			for ($i = 0; $i < ASM::$pam->size(); $i++) { 
				if (ASM::$pam->get($i)->status == PAM_CHIEF) {
					echo '<div class="center-box">';
						echo '<span class="label">' . $status[ASM::$pam->get($i)->status - 1] . ' de ' . ColorResource::getInfo(ASM::$pam->get($i)->rColor, 'popularName') . '</span>';
						echo '<span class="value">' . ASM::$pam->get($i)->name . '</span>';
					echo '</div>';

					echo '<div class="profil-flag color-' . ASM::$pam->get($i)->rColor . '">';
						echo '<img ';
							echo 'src="' . MEDIA . '/avatar/big/' . ASM::$pam->get($i)->avatar . '.png" ';
							echo 'alt="avatar de ' . ASM::$pam->get($i)->name . '" ';
						echo '/>';
					echo '</div>';

					$have = TRUE;
					break;
				}
			}
			if (!$have) {
				echo '<div class="center-box">';
					echo '<span class="value">Aucun ' . $status[5] . '</span>';
				echo '</div>';
			}

		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component profil player size1">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$list = array(PAM_MINISTER, PAM_WARLORD, PAM_TREASURER);

			foreach ($list as $type) {
				echo '<div class="center-box">';
					echo '<span class="value">' . $status[$type - 1] . '</span>';
				echo '</div>';

				$have = FALSE;
				for ($i = 0; $i < ASM::$pam->size(); $i++) { 
					if (ASM::$pam->get($i)->status == $type) {
						echo '<div class="player">';
							echo '<a href="' . APP_ROOT . 'diary/player-' .  ASM::$pam->get($i)->id . '">';
								echo '<img src="' . MEDIA . 'avatar/small/' .  ASM::$pam->get($i)->avatar . '.png" alt="' .  ASM::$pam->get($i)->name . '" />';
							echo '</a>';
							echo '<span class="title">' . $status[ ASM::$pam->get($i)->status - 1] . '</span>';
							echo '<strong class="name">' .  ASM::$pam->get($i)->name . '</strong>';
							echo '<span class="experience">' . Format::number( ASM::$pam->get($i)->factionPoint) . ' de prestige</span>';
						echo '</div>';

						$have = TRUE;
						break;
					}
				}
				if (!$have) {
					echo '<div class="center-box">';
						echo '<span class="label">Aucun joueur à ce poste</span>';
					echo '</div>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_DGG);
?>