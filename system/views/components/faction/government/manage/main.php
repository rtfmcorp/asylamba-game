<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Zeus\Model\Player;

$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('app.session');
$sessionToken = $session->get('token');

# require
$S_PAM_DGG = $playerManager->getCurrentSession();
$playerManager->changeSession($PLAYER_GOV_TOKEN);

$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component profil player size1">';
	echo '<div class="head skin-2">';
		echo '<h2>Nomination</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$list = array(Player::MINISTER, Player::WARLORD, Player::TREASURER);

			foreach ($list as $type) {
				echo '<h4>' . $status[$type - 1] . '</h4>';

				$have = FALSE;
				for ($i = 0; $i < $playerManager->size(); $i++) { 
					if ($playerManager->get($i)->status == $type) {
						echo '<div class="player">';
							echo '<a href="' . APP_ROOT . 'embassy/player-' .  $playerManager->get($i)->id . '">';
								echo '<img src="' . MEDIA . 'avatar/small/' .  $playerManager->get($i)->avatar . '.png" alt="' .  $playerManager->get($i)->name . '"  class="picto" />';
							echo '</a>';
							echo '<span class="title">' . $status[$playerManager->get($i)->status - 1] . '</span>';
							echo '<strong class="name">' .  $playerManager->get($i)->name . '</strong>';
							echo '<span class="experience">' . Format::number($playerManager->get($i)->factionPoint) . ' de prestige</span>';
						echo '</div>';

						echo '<a href="' . Format::actionBuilder('fireminister', $sessionToken, ['rplayer' => $playerManager->get($i)->id]) . '" class="more-button">Démettre de ses fonctions</a>';

						$have = TRUE;
						break;
					}
				}
				if (!$have) {
					if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
						$S_PAM_DGG2 = $playerManager->getCurrentSession();
						$playerManager->changeSession($PLAYER_SENATE_TOKEN);

						echo '<form action="' . Format::actionBuilder('choosegovernment', $sessionToken, ['department' => $type]) . '" method="post" class="choose-government">';
							echo '<select name="rplayer">';
								echo '<option value="-1">Choisissez un joueur</option>';
								for ($j = 0; $j < $playerManager->size(); $j++) {
									echo '<option value="' . $playerManager->get($j)->id . '">' . $status[$playerManager->get($j)->status - 1] . ' ' . $playerManager->get($j)->name . '</option>';
								}
							echo '</select>';
							echo '<button type="submit">Nommer au poste</button>';
						echo '</form>';

						$playerManager->changeSession($S_PAM_DGG2);
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

$playerManager->changeSession($S_PAM_DGG);