<?php
# listPlayer component
# in player.demeter package

# affichage des joueurs de la faction

# require
	# [{player}]	players_listPlayer

echo '<div class="component player size3">';
	echo '<div class="head skin-2">';
		echo '<h2>Joueurs</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($players_listPlayer as $p) {
				$status = ColorResource::getInfo($p->getRColor(), 'status');

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