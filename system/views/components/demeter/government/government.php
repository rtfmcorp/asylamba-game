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
			$n6 = ASM::$pam->get(0);

			if ($n6->status == PAM_CHIEF) {
				echo '<div class="center-box">';
					echo '<span class="label">' . $status[$n6->status - 1] . ' de ' . ColorResource::getInfo($n6->rColor, 'popularName') . '</span>';
					echo '<span class="value">' . $n6->name . '</span>';
				echo '</div>';

				echo '<div class="profil-flag color-' . $n6->rColor . '">';
					echo '<img ';
						echo 'src="' . MEDIA . '/avatar/big/' . $n6->avatar . '.png" ';
						echo 'alt="avatar de ' . $n6->name . '" ';
					echo '/>';
				echo '</div>';
			} else {
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
			echo '<div class="center-box">';
				echo '<span class="value">' . $status[4] . '</span>';
			echo '</div>';

			if ((ASM::$pam->size() - 1) >= 1 && ASM::$pam->get(1)->status == PAM_MINISTER) {
				$n5 = ASM::$pam->get(1);
				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $n5->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $n5->avatar . '.png" alt="' . $n5->name . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[$n5->status - 1] . '</span>';
					echo '<strong class="name">' . $n5->name . '</strong>';
					echo '<span class="experience">' . Format::number($n5->factionPoint) . ' de prestige</span>';
				echo '</div>';
			} else {
				echo '<div class="center-box">';
					echo '<span class="label">Aucun joueur à ce poste</span>';
				echo '</div>';
			}

			echo '<div class="center-box">';
				echo '<span class="value">' . $status[3] . '</span>';
			echo '</div>';

			if ((ASM::$pam->size() - 1 >= 2) && ASM::$pam->get(2)->status == PAM_WARLORD) {
				$n4 = ASM::$pam->get(2);
				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $n4->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $n4->avatar . '.png" alt="' . $n4->name . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[$n4->status - 1] . '</span>';
					echo '<strong class="name">' . $n4->name . '</strong>';
					echo '<span class="experience">' . Format::number($n4->factionPoint) . ' de prestige</span>';
				echo '</div>';
			} else {
				echo '<div class="center-box">';
					echo '<span class="label">Aucun joueur à ce poste</span>';
				echo '</div>';
			}

			echo '<div class="center-box">';
				echo '<span class="value">' . $status[2] . '</span>';
			echo '</div>';

			if ((ASM::$pam->size() - 1 >= 3) && ASM::$pam->get(3)->status == PAM_TREASURER) {
				$n3 = ASM::$pam->get(3);
				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $n3->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $n3->avatar . '.png" alt="' . $n3->name . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[$n3->status - 1] . '</span>';
					echo '<strong class="name">' . $n3->name . '</strong>';
					echo '<span class="experience">' . Format::number($n3->factionPoint) . ' de prestige</span>';
				echo '</div>';
			} else {
				echo '<div class="center-box">';
					echo '<span class="label">Aucun joueur à ce poste</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_DGG);
?>