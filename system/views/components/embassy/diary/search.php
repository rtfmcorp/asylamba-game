<?php
# search componant
# in player.diary package

# affiche la recherche d'un joueur

# require
#	{player}	player_selected
# 	{ob}		ob_selected
#	bool 		player_ishim

use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Classes\Library\Format;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);

$status = ColorResource::getInfo($player_selected->rColor, 'status');

echo '<div class="component search-player size2 color' . $player_selected->rColor . '">';
	echo '<div class="box">';
		echo '<div class="top">';
			echo '<form action="' . Format::actionBuilder('searchplayer', $sessionToken) . '" method="post">';
				echo '<label>';
					echo 'Recherchez un joueur';
					echo '<input type="hidden" name="playerid" class="autocomplete-hidden" />';
					echo '<input type="text" name="name" class="autocomplete-player ac_input" autocomplete="off" />';
				echo '</label>';
			echo '</form>';
		echo '</div>';
		echo '<div class="center">';
			echo '<img src="' . $mediaPath . 'avatar/big/' . $player_selected->avatar . '	.png" alt="avatar de ' . $player_selected->name . '" class="avatar" />';

			echo '<div class="right">';
				echo '<h1>' . $player_selected->name . '</h1>';
				echo '<p>' . $status[$player_selected->status - 1] . ' de ' . ColorResource::getInfo($player_selected->rColor, 'popularName') . '</p>';
				echo '<p>niveau ' . $player_selected->level . '</p>';
				
				if ($player_selected->id != $session->get('playerId')) {
					echo '<hr />';
					echo '<p><a href="' . $appRoot . 'message/conversation-new/sendto-' . $player_selected->id . '" style="text-decoration: none; color: white; border-bottom: solid 1px #4f4f4f">Envoyer un message</a></p>';
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
