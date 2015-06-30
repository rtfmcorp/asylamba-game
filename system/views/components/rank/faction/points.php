<?php
# rankVictory component
# in rank package

# classement faction en fonction de la somme des points des joueurs de chaque faction

# require
	# _T PRM 		FACTION_RANKING_POINTS

ASM::$frm->changeSession($FACTION_RANKING_POINTS);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
		echo '<h2>Classement de victoire</h2>';
		echo '<em>Classement cumulatif</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$frm->size(); $i++) {
				echo ASM::$frm->get($i)->commonRender('points');
			}
		echo '</div>';
	echo '</div>';
echo '</div>';