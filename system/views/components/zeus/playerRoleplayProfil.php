<?php
# playerRoleplayProfil componant
# in zeus package

# affiche le profil complet d'un joueur, permet la modification des informations

# require
	# {player}	player_playerRoleplayProfil

echo '<div class="component profil thread">';
	echo '<div class="head">';
		echo '<h1>Votre profil</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$status = ColorResource::getInfo(CTR::$data->get('playerInfo')->get('color'), 'status');

			echo '<div class="number-box">';
				echo '<span class="label">' . $status[$player_playerRoleplayProfil->getStatus() - 1] . ' de ' . ColorResource::getInfo(CTR::$data->get('playerInfo')->get('color'), 'popularName') . '</span>';
				echo '<span class="value">' . $player_playerRoleplayProfil->getName() . '</span>';
			echo '</div>';

			echo '<img ';
				echo 'src="' . MEDIA . '/avatar/big/' . $player_playerRoleplayProfil->getAvatar() . '.png" ';
				echo 'alt="avatar de ' . $player_playerRoleplayProfil->getName() . '" ';
				echo 'class="main-avatar" ';
			echo '/>';

			echo '<form method="POST" action="' . APP_ROOT . 'action/a-setdescription">';
				echo '<p class="input input-area">';
					echo '<textarea name="description" id="description" placeholder="Racontez au monde vos ambitions de domination !">';
						echo $player_playerRoleplayProfil->getDescription();
					echo '</textarea>';
				echo '</p>';
				echo '<p class="button">';
					echo '<input type="submit" value="modifier">';
				echo '</p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>