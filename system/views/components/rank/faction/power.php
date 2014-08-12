<?php
# rankVictory component
# in rank package

# liste les joueurs aux meilleures victoires

# require
	# _T PRM 		FACTION_RANKING_POWER

ASM::$frm->changeSession($FACTION_RANKING_POWER);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
		echo '<h2>Général</h2>';
		echo '<em>Total des points des joueurs de la faction</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$frm->size(); $i++) {
				echo ASM::$frm->get($i)->commonRender('power');
			}
		echo '</div>';
	echo '</div>';
echo '</div>';