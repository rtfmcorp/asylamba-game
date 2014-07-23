<?php
# rankVictory component
# in rank package

# liste les joueurs aux meilleures victoires

# require
	# _T PRM 		PLAYER_RANKING_FRONT

ASM::$prm->changeSession($PLAYER_RANKING_FRONT);

echo '<div class="component player rank">';
	echo '<div class="head">';
		echo '<h1>Joueur</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="info">Numéro de classement : #' . ASM::$prm->get()->rRanking . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';