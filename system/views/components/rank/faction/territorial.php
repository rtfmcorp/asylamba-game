<?php
# rankVictory component
# in rank package

# classement faction en fonction de la possession de secteurs

# require
	# _T PRM 		FACTION_RANKING_TERRITORIAL

ASM::$frm->changeSession($FACTION_RANKING_TERRITORIAL);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
		echo '<h2>Territorial</h2>';
		echo '<em>Nombre de points des secteurs controlés</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$frm->size(); $i++) {
				echo ASM::$frm->get($i)->commonRender('territorial');
			}
		echo '</div>';
	echo '</div>';
echo '</div>';