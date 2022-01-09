<?php
# playerRoleplayProfil componant
# in zeus package

# affiche le profil complet d'un joueur, permet la modification des informations

# require
	# {player}	player_playerRoleplayProfil


use App\Modules\Demeter\Resource\ColorResource;
use App\Classes\Library\Format;

$container = $this->getContainer();
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$mediaPath = $container->getParameter('media');

echo '<div class="component profil">';
	echo '<div class="head">';
		echo '<h1>Votre profil</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$status = ColorResource::getInfo($session->get('playerInfo')->get('color'), 'status');

			echo '<div class="center-box">';
				echo '<span class="label">' . $status[$player_playerRoleplayProfil->status - 1] . ' de ' . ColorResource::getInfo($session->get('playerInfo')->get('color'), 'popularName') . '</span>';
				echo '<span class="value">' . $player_playerRoleplayProfil->name . '</span>';
			echo '</div>';

			echo '<div class="profil-flag">';
				echo '<img ';
					echo 'src="' . $mediaPath . '/avatar/big/' . $player_playerRoleplayProfil->avatar . '.png" ';
					echo 'alt="avatar de ' . $player_playerRoleplayProfil->name . '" ';
				echo '/>';
				echo '<span class="level">' . $player_playerRoleplayProfil->level . '</span>';
			echo '</div>';

			$baseLevelPlayer = $this->getContainer()->getParameter('zeus.player.base_level');
			$exp = $player_playerRoleplayProfil->getExperience();
			$nlv = $baseLevelPlayer * (pow(2, ($player_playerRoleplayProfil->getLevel() - 1)));
			$clv = $baseLevelPlayer * (pow(2, ($player_playerRoleplayProfil->getLevel() - 2)));
			$prc = ((($exp - $clv) * 200) / $nlv);

			echo '<div class="number-box">';
				echo '<span class="label">expérience</span>';
				echo '<span class="value">' . Format::numberFormat($exp) . '</span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">expérience nécessaire pour le prochain niveau</span>';
				echo '<span class="value">' . Format::numberFormat($nlv) . '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $prc . '%;" class="content"></span>';
				echo '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
