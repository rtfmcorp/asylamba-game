<?php
# listPlayer component
# in player.demeter package

# affichage des joueurs de la faction

# require
	# PAM/Token 		PAM_LAST_TOKEN

$status = ColorResource::getInfo($faction->id, 'status');

$S_PAM_LAST = ASM::$pam->getCurrentSession();
ASM::$pam->changeSession($PAM_LAST_TOKEN);

echo '<div class="component player">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Nouveaux membres</h4>';

			for ($i = 0; $i < ASM::$pam->size(); $i++) {
				$p = ASM::$pam->get($i);

				if (Utils::interval($p->dInscription, Utils::now(), 's') > 259200) {
					if ($i == 0) {
						echo '<p>Aucun nouveau membre ces 3 derniers jours</p>';
					}
					break;
				}

				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $p->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $p->avatar . '.png" alt="' . $p->name . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[$p->status - 1] . '</span>';
					echo '<strong class="name">' . $p->name . '</strong>';

					if ($p->id != CTR::$data->get('playerId')) {
						echo '<span class="experience"><a href="' . APP_ROOT . 'message/mode-create/sendto-' . $p->id . '">Souhaiter la bienvenue</a></span>';
					}
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_LAST);
?>