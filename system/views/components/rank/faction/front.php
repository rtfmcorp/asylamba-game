<?php
# rankVictory component
# in rank package

# liste les joueurs aux meilleures victoires

# require
	# _T PRM 		FACTION_RANKING_FRONT

ASM::$frm->changeSession($FACTION_RANKING_FRONT);
$f = ASM::$frm->get(0);

# display
echo '<div class="component profil">';
	echo '<div class="head">';
		echo '<h1>Faction</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="center-box">';
				echo '<span class="label">Meilleure faction</span>';
				echo '<span class="value">' . ColorResource::getInfo($f->rFaction, 'popularName') . '</span>';
			echo '</div>';

			echo '<div class="profil-flag color-' . $f->rFaction . '">';
				echo '<img ';
					echo 'src="' . MEDIA . '/asylamba.jpg" ';
					echo 'alt="" ';
				echo '/>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';