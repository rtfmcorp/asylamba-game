<?php
# listPlayer component
# in player.demeter package

# affichage des joueurs de la faction

# require
	# [{player}]	players_listPlayer

$status = ColorResource::getInfo($faction->id, 'status');

echo '<div class="component player size3">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Gouvernement</h4>';

			for ($i = 0; $i < count($players_listPlayer); $i++) {
				$p = $players_listPlayer[$i];

				if ($p->status == 2 AND $players_listPlayer[$i - 1]->status != 2) {
					echo '<h4>Sénat</h4>';
				} elseif ($p->status == 1 AND $players_listPlayer[$i - 1]->status != 1) {
					echo '<h4>--</h4>';
				}

				echo '<div class="player">';
					echo '<a href="' . APP_ROOT . 'diary/player-' . $p->getId() . '">';
						echo '<img src="' . MEDIA . 'avatar/small/' . $p->getAvatar() . '.png" alt="' . $p->getName() . '" />';
					echo '</a>';
					echo '<span class="title">' . $status[$p->getStatus() - 1] . '</span>';
					echo '<strong class="name">' . $p->getName() . '</strong>';
					echo '<span class="experience">' . Format::numberFormat($p->factionPoint) . ' points de prestige</span>';
					if (Utils::interval(Utils::now(), $p->getDLastActivity(), 's') < 600) {
						echo '<span class="online hb lt" title="est en ligne actuellement"></span>';
					} elseif (Utils::interval(Utils::now(), $p->getDLastActivity()) > PAM_TIME_ALLY_INACTIVE) {
						echo '<span class="inactive hb lt" title="ne s\'est plus connecté depuis une semaine"></span>';
					}
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>