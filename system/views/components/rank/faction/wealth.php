<?php
# rankVictory component
# in rank package

# classement faction en fonction de la richesse totale en crédits

# require
	# _T PRM 		FACTION_RANKING_WEALTH

ASM::$frm->changeSession($FACTION_RANKING_WEALTH);

echo '<div class="component player rank">';
	echo '<div class="head skin-4">';
		echo '<img class="main" alt="ressource" src="' . MEDIA . 'rank/cup.png">';
		echo '<h2>Richesse</h2>';
		echo '<em>Richesse totale de la faction</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			for ($i = 0; $i < ASM::$frm->size(); $i++) {
				echo ASM::$frm->get($i)->commonRender('wealth');
			}
		echo '</div>';
	echo '</div>';
echo '</div>';