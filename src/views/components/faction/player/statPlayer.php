<?php

use App\Classes\Library\Utils;
use App\Modules\Demeter\Resource\ColorResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);

# statPlayer component
# in player.demeter package

# affichage des stats joueurs de la faction

# require
	# int 		nbPlayer_statPlayer
	# int 		nbOnlinePlayer_statPlayer
	# int 		nbOfflinePlayer_statPlayer
	# int 		avgVictoryPlayer_statPlayer
	# int 		avgDefeatPlayer_statPlayer
	# int 		avgPointsPlayer_statPlayer

$status = ColorResource::getInfo($faction->id, 'status');

# work
echo '<div class="component player rank">';
	echo '<div class="head skin-1">';
		echo '<h1>Membres</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">Joueurs actifs dans la faction</span>';
				echo '<span class="value">' . $nbPlayer_statPlayer . '</span>';
			echo '</div>';

			echo '<div class="number-box grey">';
				echo '<span class="label">Joueurs en ligne actuellement</span>';
				echo '<span class="value">' . $nbOnlinePlayer_statPlayer . '</span>';
			echo '</div>';
			
			echo '<h4>Nouveaux membres</h4>';

			$lastFactionPlayers = $playerManager->getLastFactionPlayers($faction->id);
			
			foreach ($lastFactionPlayers as $p) {
				if (Utils::interval($p->dInscription, Utils::now(), 's') > 259200) {
					if ($i == 0) {
						echo '<p>Aucun nouveau membre ces 3 derniers jours</p>';
					}
					break;
				}

				echo '<div class="player">';
					echo '<a href="' . $appRoot . 'embassy/player-' . $p->id . '">';
						echo '<img src="' . $mediaPath . 'avatar/small/' . $p->avatar . '.png" class="picto" alt="' . $p->name . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[$p->status - 1] . '</span>';
					echo '<strong class="name">' . $p->name . '</strong>';

					if ($p->id != $session->get('playerId')) {
						echo '<span class="experience"><a href="' . $appRoot . 'message/conversation-new/sendto-' . $p->id . '">Souhaiter la bienvenue</a></span>';
					}
				echo '</div>';
			}

		echo '</div>';
	echo '</div>';
echo '</div>';
