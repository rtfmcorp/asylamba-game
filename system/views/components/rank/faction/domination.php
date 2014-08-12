<?php
# rankVictory component
# in rank package

# liste les joueurs aux meilleures victoires

# require
	# _T PRM 		FACTION_RANKING_DOMINATION

ASM::$frm->changeSession($FACTION_RANKING_DOMINATION);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
		echo '<h2>Territorial</h2>';
		echo '<em>Total de la population controlée</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$frm->size(); $i++) {
				echo ASM::$frm->get($i)->commonRender('domination');
			}
		echo '</div>';
	echo '</div>';
echo '</div>';