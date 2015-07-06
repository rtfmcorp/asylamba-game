<?php
# rankVictory component
# in rank package

# classement faction en fonction de la somme des points des joueurs de chaque faction

# require
	# _T PRM 		FACTION_RANKING_POINTS

ASM::$frm->changeSession($FACTION_RANKING_POINTS);

echo '<div class="component player profil rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
		echo '<h2>Classement de victoire</h2>';
		echo '<em>Classement cumulatif. La victoire est remportée à ' . POINTS_TO_WIN . ' points.</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			if (Utils::interval(SERVER_START_TIME, Utils::now(), 'h') > HOURS_BEFORE_START_OF_RANKING) {
				for ($i = 0; $i < ASM::$frm->size(); $i++) {
					echo ASM::$frm->get($i)->commonRender('points');
				}
			} else {
				echo '<div class="center-box">';
					echo '<span class="label">La classement de victoire n\'est pas encore activé. Il le sera à partir du </span>';
					echo '<span class="value">' . date("d.m.Y à H:i:s", strtotime(Utils::addSecondsToDate(SERVER_START_TIME, HOURS_BEFORE_START_OF_RANKING * 3600))) . '</span>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';