<?php
# load player

# limit
$stepRank = 75;

if (in_array($mode, array('general', 'victory', 'defeat'))) {
	include_once ZEUS;
	
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession(FALSE);

	if ($mode == 'general') {
		ASM::$pam->load(array('statement' => array(PAM_ACTIVE)), array('experience', 'DESC'), array(0, $stepRank));
	} elseif ($mode == 'victory') {
		ASM::$pam->load(array('statement' => array(PAM_ACTIVE)), array('victory', 'DESC'), array(0, $stepRank));
	} elseif ($mode == 'defeat') {
		ASM::$pam->load(array('statement' => array(PAM_ACTIVE)), array('defeat', 'DESC'), array(0, $stepRank));
	}
} elseif ($mode == 'faction') {
	$db = DataBase::getInstance();
	$qr = $db->query('SELECT
			COUNT(s.id) AS nbSector,
			(SELECT COUNT(p.id) FROM player AS p WHERE p.rColor = c.id GROUP BY p.rColor) AS nbPlayer,
			c.id
		FROM sector AS s
		LEFT JOIN color AS c
			ON s.rColor = c.id
		GROUP BY s.rColor
		ORDER BY nbSector DESC, 
		nbPlayer DESC'
	);
	$factions = $qr->fetchAll();
}

# header part
echo '<div class="header">';
	echo '<ul>';
		echo '<li><a ' . (($mode == 'faction') ? 'class="active"' : '') . ' href="' . APP_ROOT . 'map/view-ranking/mode-faction" >Classement des Factions</a></li>';
		echo '<li>Classement joueur&#8195;';
			echo '<a ' . (($mode == 'general') ? 'class="active"' : '') . ' href="' . APP_ROOT . 'map/view-ranking/mode-general" >Général</a>';
			echo '<a ' . (($mode == 'victory') ? 'class="active"' : '') . ' href="' . APP_ROOT . 'map/view-ranking/mode-victory" >Victoires</a>';
			echo '<a ' . (($mode == 'defeat') ? 'class="active"' : '') . ' href="' . APP_ROOT . 'map/view-ranking/mode-defeat" >Défaites</a> ';
		echo '</li>';
	echo '</ul>';
	echo '<a href="#" class="button hb tl closeactionbox" title="fermer">×</a>';
echo '</div>';

# body part
echo '<div class="body">';
	echo '<a class="actbox-movers" id="actboxToLeft" href="#"></a>';
	echo '<a class="actbox-movers" id="actboxToRight" href="#"></a>';
	echo '<div class="rank">';
		echo '<ul>';
			echo '<li class="title">';
				if (in_array($mode, array('general', 'victory', 'defeat'))) {
					if ($mode == 'general') {
						echo '<h2>Classement Général</h2>';
					} elseif ($mode == 'victory') {
						echo '<h2>Classement des Victoires</h2>';
					} elseif ($mode == 'defeat') {
						echo '<h2>Classement des Défaites</h2>';
					}
					# echo 'Il y a 320 joueurs</p>';
				} elseif ($mode == 'faction') {
					echo '<h2>Classement des Factions</h2>';
				}
			echo '</li>';

			if (in_array($mode, array('general', 'victory', 'defeat'))) {
				for ($i = 0; $i < ASM::$pam->size(); $i++) { 
					$player = ASM::$pam->get($i);
					$status = ColorResource::getInfo($player->getRColor(), 'status');

					echo '<li class="item color' . $player->getRColor() . ' ' . (($player->getId() == CTR::$data->get('playerId')) ? 'active' : '') . '">';
						echo '<span class="number">' . ($i + 1) . '</span>';
						echo '<p class="avatar"><a href="' . APP_ROOT . 'diary/player-' . $player->getId() . '">';
							echo '<img src="' . MEDIA . 'avatar/medium/' . $player->getAvatar() . '.png" alt="avatar" />';
						echo '</a></p>';
						echo '<p class="text">';
							echo '<span>' . $status[$player->getStatus() - 1] . '</span>';
							echo '<strong>' . $player->getName() . '</strong>';
							echo '<span>de ' . ColorResource::getInfo($player->getRColor(), 'popularName') . '</span>';
						echo '</p>';
						echo '<p class="text">';
							echo '<span>niveau ' . $player->getLevel() . '</span>';
							echo '<span>' . $player->getVictory() . ' victoire' . Format::addPlural($player->getVictory()) . '</span>';
							echo '<span>' . $player->getDefeat() . ' défaite' . Format::addPlural($player->getDefeat()) . '</span>';
						echo '</p>';
					echo '</li>';
				}
			} elseif ($mode == 'faction') {
				for ($i = 0; $i < count($factions); $i++) { 
					echo '<li class="item color' . $factions[$i]['id'] . ' ' . (($factions[$i]['id'] == CTR::$data->get('playerInfo')->get('color')) ? 'active' : '') . '">';
						echo '<span class="number">' . ($i + 1) . '</span>';
						echo '<p class="avatar faction"><img src="' . MEDIA . 'ally/big/color' . $factions[$i]['id'] . '.png" alt="" /></p>';
						echo '<p class="text">';
							echo '<strong>' . ColorResource::getInfo($factions[$i]['id'], 'officialName') . '</strong>';
						echo '</p>';
						echo '<p class="text">';
							echo '<span>' . $factions[$i]['nbSector'] . ' secteur' . Format::addPlural($factions[$i]['nbSector']) . ' revendiqué' . Format::addPlural($factions[$i]['nbSector']) . '</span>';
						echo '</p>';
						echo '<p class="text">';
							echo '<span>' . $factions[$i]['nbPlayer'] . ' joueur' . Format::addPlural($factions[$i]['nbPlayer']) . '</span>';
						echo '</p>';
					echo '</li>';
				}
			}
		echo '</ul>';
	echo '</div>';
echo '</div>';

if (in_array($mode, array('general', 'victory', 'defeat'))) {
	ASM::$pam->changeSession($S_PAM1);
}
?>