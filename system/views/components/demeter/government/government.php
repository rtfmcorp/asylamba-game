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

echo '<div class="component profil">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="center-box">';
				echo '<span class="value">' . $status[4] . '</span>';
			echo '</div>';

			if (ASM::$pam->size() - 1 >= 1) {
				$n5 = ASM::$pam->get(1);
				if ($n5->status == PAM_MINISTER) {
					echo 'un ministre : ' . $n5->name;
				} else {
					echo 'pas de ministre';
				}
			}

			echo '<div class="center-box">';
				echo '<span class="value">' . $status[3] . '</span>';
			echo '</div>';

			if (ASM::$pam->size() - 1 >= 2) {
				$n4 = ASM::$pam->get(2);
				if ($n4->status == PAM_WARLORD) {
					echo 'un chef de guerre : ' . $n4->name;
				} else {
					echo 'pas de chef de guerre';
				}
			}

			echo '<div class="center-box">';
				echo '<span class="value">' . $status[2] . '</span>';
			echo '</div>';

			if (ASM::$pam->size() - 1 >= 3) {
				$n3 = ASM::$pam->get(3);
				if ($n3->status == PAM_TREASURER) {
					echo 'un trésorier : ' . $n3->name;
				} else {
					echo 'pas de trésorier';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$pam->changeSession($S_PAM_DGG);
?>