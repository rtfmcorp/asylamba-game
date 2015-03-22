<?php
# listPlayer component
# in player.demeter package

# affichage des joueurs de la faction

# require
	# [{player}]	players_listPlayer

$status = ColorResource::getInfo($faction->id, 'status');

$fplayers = array(
	'Gouvernement' => array(),
	'Sénat' => array(),
	'Peuple' => array()
);

for ($i = 0; $i < count($players_listPlayer); $i++) {
	$p = $players_listPlayer[$i];

	if ($p->status == 1) {
		$fplayers['Peuple'][] = $p;
	} elseif ($p->status == 2) {
		$fplayers['Sénat'][] = $p;
	} else {
		$fplayers['Gouvernement'][] = $p;
	}
}

echo '<div class="component player size3">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			foreach ($fplayers as $type => $players) {
				echo '<h4>' . $type . '</h4>';

				if (empty($players)) {
					echo '<p>aucun joueur</p>';
				}

				foreach ($players as $p) {
					echo '<div class="player">';
						echo '<a href="' . APP_ROOT . 'embassy/player-' . $p->getId() . '">';
							echo '<img src="' . MEDIA . 'avatar/small/' . $p->getAvatar() . '.png" class="picto" alt="' . $p->getName() . '" />';
						echo '</a>';
						echo '<span class="title">' . $status[$p->getStatus() - 1] . '</span>';
						echo '<strong class="name">' . $p->getName() . '</strong>';
						echo '<span class="experience">' . Format::numberFormat($p->factionPoint) . ' points de prestige</span>';
						if (Utils::interval(Utils::now(), $p->getDLastActivity(), 's') < (TIME_EVENT_UPDATE * 2)) {
							echo '<span class="online hb lt" title="est en ligne actuellement"></span>';
						} elseif (Utils::interval(Utils::now(), $p->getDLastActivity()) > PAM_TIME_ALLY_INACTIVE) {
							echo '<span class="inactive hb lt" title="ne s\'est plus connecté depuis une semaine"></span>';
						}
					echo '</div>';
				}
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>