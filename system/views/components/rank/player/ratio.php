<?php
# rankVictory component
# in rank package

# liste les joueurs aux meilleures victoires

# require
	# _T PRM 		PLAYER_RANKING_GENERAL

ASM::$prm->changeSession($PLAYER_RANKING_RATIO);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'resources/resource.png">';
		echo '<h2>Ratio victoires/défaites</h2>';
		echo '<em>bla</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$prm->size(); $i++) { 
				echo ASM::$prm->get($i)->commonRender('ratio');
			}
		echo '</div>';
	echo '</div>';
echo '</div>';