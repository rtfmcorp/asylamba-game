<?php
# playerRoleplayProfil componant
# in zeus package

# affiche le profil complet d'un joueur, permet la modification des informations

# require
	# {player}	player_playerRoleplayProfil

echo '<div class="component profil">';
	echo '<div class="head">';
		echo '<h1>Votre profil</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$status = ColorResource::getInfo(CTR::$data->get('playerInfo')->get('color'), 'status');

			echo '<div class="center-box">';
				echo '<span class="label">' . $status[$player_playerRoleplayProfil->status - 1] . ' de ' . ColorResource::getInfo(CTR::$data->get('playerInfo')->get('color'), 'popularName') . '</span>';
				echo '<span class="value">' . $player_playerRoleplayProfil->name . '</span>';
			echo '</div>';

			echo '<div class="profil-flag">';
				echo '<img ';
					echo 'src="' . MEDIA . '/avatar/big/' . $player_playerRoleplayProfil->avatar . '.png" ';
					echo 'alt="avatar de ' . $player_playerRoleplayProfil->name . '" ';
				echo '/>';
				echo '<span class="level">' . $player_playerRoleplayProfil->level . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>