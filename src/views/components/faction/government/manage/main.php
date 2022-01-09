<?php

use App\Classes\Library\Format;
use App\Modules\Demeter\Resource\ColorResource;
use App\Modules\Zeus\Model\Player;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');

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
				foreach ($governmentMembers as $minister) { 
					if ($minister->status == $type) {
						echo '<div class="player">';
							echo '<a href="' . $appRoot . 'embassy/player-' .  $minister->id . '">';
								echo '<img src="' . $mediaPath . 'avatar/small/' .  $minister->avatar . '.png" alt="' .  $minister->name . '"  class="picto" />';
							echo '</a>';
							echo '<span class="title">' . $status[$minister->status - 1] . '</span>';
							echo '<strong class="name">' .  $minister->name . '</strong>';
							echo '<span class="experience">' . Format::number($minister->factionPoint) . ' de prestige</span>';
						echo '</div>';

						echo '<a href="' . Format::actionBuilder('fireminister', $sessionToken, ['rplayer' => $minister->id]) . '" class="more-button">Démettre de ses fonctions</a>';

						$have = TRUE;
						break;
					}
				}
				if (!$have) {
					if ($session->get('playerInfo')->get('status') == Player::CHIEF) {
						echo '<form action="' . Format::actionBuilder('choosegovernment', $sessionToken, ['department' => $type]) . '" method="post" class="choose-government">';
							echo '<select name="rplayer">';
								echo '<option value="-1">Choisissez un joueur</option>';
								foreach ($senators as $senator) {
									echo '<option value="' . $senator->id . '">' . $status[$senator->status - 1] . ' ' . $senator->name . '</option>';
								}
							echo '</select>';
							echo '<button type="submit">Nommer au poste</button>';
						echo '</form>';
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
