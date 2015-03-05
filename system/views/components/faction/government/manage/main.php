<?php
# require
$S_PAM_DGG = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($PLAYER_GOV_TOKEN);

$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component profil player size1">';
	echo '<div class="head skin-2">';
		echo '<h2>Nomination</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$list = array(PAM_MINISTER, PAM_WARLORD, PAM_TREASURER);

			foreach ($list as $type) {
				echo '<h4>' . $status[$type - 1] . '</h4>';

				$have = FALSE;
				for ($i = 0; $i < ASM::$pam->size(); $i++) { 
					if (ASM::$pam->get($i)->status == $type) {
						echo '<div class="player">';
							echo '<a href="' . APP_ROOT . 'diary/player-' .  ASM::$pam->get($i)->id . '">';
								echo '<img src="' . MEDIA . 'avatar/small/' .  ASM::$pam->get($i)->avatar . '.png" alt="' .  ASM::$pam->get($i)->name . '"  class="picto"/>';
							echo '</a>';
							echo '<span class="title">' . $status[ ASM::$pam->get($i)->status - 1] . '</span>';
							echo '<strong class="name">' .  ASM::$pam->get($i)->name . '</strong>';
							echo '<span class="experience">' . Format::number( ASM::$pam->get($i)->factionPoint) . ' de prestige</span>';
						echo '</div>';

						echo '<a href="' . Format::actionBuilder('fireminister', ['rplayer' => ASM::$pam->get($i)->id]) . '" class="more-button">Démettre de ses fonctions</a>';

						$have = TRUE;
						break;
					}
				}
				if (!$have) {
					if (CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
						$S_PAM_DGG2 = ASM::$pam->getCurrentSession();
						ASM::$pam->changeSession($PLAYER_SENATE_TOKEN);

						echo '<form action="' . Format::actionBuilder('choosegovernment', ['department' => $type]) . '" method="post" class="choose-government">';
							echo '<select name="rplayer">';
								echo '<option value="-1">Choisissez un joueur</option>';
								for ($j = 0; $j < ASM::$pam->size(); $j++) {
									echo '<option value="' . ASM::$pam->get($j)->id . '">' . $status[ ASM::$pam->get($i)->status - 1] . ' ' . ASM::$pam->get($j)->name . '</option>';
								}
							echo '</select>';
							echo '<button type="submit">Nommer au poste</button>';
						echo '</form>';

						ASM::$pam->changeSession($S_PAM_DGG2);
					} else {
						echo '<div class="center-box">';
							echo '<span class="label">Aucun joueur à ce poste</span>';
						echo '</div>';
					}
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_DGG);
?>