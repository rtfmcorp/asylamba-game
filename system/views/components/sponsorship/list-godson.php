<?php

$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('session_wrapper');

$godSons = $playerManager->getGodSons($session->get('playerId'));

# display
echo '<div class="component player rank">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Liste de vos filleuls</h4>';

			foreach ($godSons as $player) {
				echo '<div class="player color' . $player->rColor . ' active">';
					echo '<a href="' . APP_ROOT . 'embassy/player-' . $player->id . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $player->avatar . '.png" alt="' . $player->name . '" class="picto">';
					echo '</a>';
					echo '<span class="title">' . $player->name . '</span>';
					echo '<strong class="name">' . $player->name . '</strong>';
					echo '<span class="experience">niveau ' . $player->level . '</span>';
				echo '</div>';
			}

			if (count($godSons) === 0) {
				echo '<p>Vous n\'avez encore aucun filleul.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';